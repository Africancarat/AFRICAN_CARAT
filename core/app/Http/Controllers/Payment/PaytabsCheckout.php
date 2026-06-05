<?php

namespace App\Http\Controllers\Payment;

use App\Helpers\EmailHelper;
use App\Models\Order;
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Jobs\EmailSendJob;
use App\Models\Item;
use App\Models\Notification;
use App\Models\PaymentSetting;
use App\Models\PromoCode;
use App\Models\Setting;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\TrackOrder;
use App\Repositories\Front\CartRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaytabsCheckout
{
    public function store(Request $request)
    {
        $state = State::whereStatus(1)->count() != 0  ? 'required' : '';
        $shipping = ShippingService::whereStatus(1)->count() == 0 || PriceHelper::CheckDigital() == true? 'required' : '';

        if($request->single_page_checkout == 1){
            $request->validate([
                'state_id' => $state,
                "shipping_id" => $shipping,
                'bill_first_name' => 'required',
                'bill_last_name' => 'required',
                'bill_email' => 'required',
                'bill_phone' => 'required',
                'bill_address1' => 'required',
                'bill_city' => 'required',
                'bill_zip' => 'required',
            ]);
        }else{
            $request->validate([
                'state_id' => $state,
                "shipping_id" => $shipping,
            ]);
        }

        PriceHelper::checkCheckout($request);


        $data = $request->all();
        $user = Auth::user();
        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;

        foreach ($cart as $key => $items) {

            $total += $items['main_price'] * $items['qty'];
            $option_price += $items['attribute_price'];
            $cart_total = $total + $option_price;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }


        if (!PriceHelper::Digital()) {
            $shipping = null;
        } else {
            $shipping = ShippingService::findOrFail($data['shipping_id']);
        }

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }
        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $grand_total += PriceHelper::StatePrce($data['state_id'], $cart_total);
        $total_amount = PriceHelper::setConvertPrice($grand_total);
        $orderData['state'] =  $data['state_id'] ? json_encode(State::findOrFail($data['state_id']), true) : null;
        $orderData['cart'] = json_encode(CartRepository::sanitizeCartForOrderJson($cart), true);
        $orderData['discount'] = json_encode($discount, true);
        $orderData['shipping'] = json_encode($shipping, true);
        $orderData['tax'] = $total_tax;
        $orderData['state_price'] = PriceHelper::StatePrce($data['state_id'], $cart_total);
        $orderData['shipping_info'] = json_encode(Session::get('shipping_address'), true);
        $orderData['billing_info'] = json_encode(Session::get('billing_address'), true);
        $orderData['payment_method'] = 'Paytabs';
        $orderData['user_id'] = isset($user) ? $user->id : 0;
        $orderData['transaction_number'] = Str::random(10);
        $orderData['currency_sign'] = PriceHelper::setCurrencySign();
        $orderData['currency_value'] = PriceHelper::setCurrencyValue();
        $orderData['payment_status'] = 'Unpaid';
        $orderData['order_status'] = 'Pending';
        $order = Order::create($orderData);


        $customer_name = $user ? $user->name : Session::get('billing_address')['bill_first_name'];
        $customer_email = $user ? $user->email : Session::get('billing_address')['bill_email'];
        $customer_phone = Session::get('billing_address')['bill_phone'];
        $customer_address = Session::get('billing_address')['bill_address1'];
        $customer_city = Session::get('billing_address')['bill_city'];
        $customer_state = '';
        $customer_country = Session::get('billing_address')['bill_country'];
        $customer_zip = Session::get('billing_address')['bill_zip'];
        $customer_ip = request()->ip();


        $paymentData = PaymentSetting::whereUniqueKeyword('paytabs')->first();
        $paydata = $paymentData->convertJsonData();

        // Call PayTabs to create a payment page
        $endpoint = 'https://secure-global.paytabs.com/payment/request';
        $payload = [
            'profile_id' => $paydata['profile_id'],
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => (string)$order->id,
            'cart_description' => "Order #{$order->transaction_number}",
            'cart_amount' => $total_amount,
            'customer_details' => [
                'name' => $customer_name,
                'email' => $customer_email,
                'phone' => $customer_phone,
                'street1' => $customer_address,
                'city' => $customer_city,
                'state' => $customer_state,
                'country' => $customer_country,
                'zip' => $customer_zip,
                'ip' => $customer_ip,
            ],
            'return' => route('paytab.callback'),
            'callback' => route('paytab.callback'),
            'language' => 'en',
            "cart_currency" => PriceHelper::setCurrencyName(),
        ];
        // 'return' => "https://webhook.site/df566272-ce10-4474-b271-7fe1130f8bd0",
        // 'callback' => "https://webhook.site/df566272-ce10-4474-b271-7fe1130f8bd0",

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: " . $paydata['client_secret'],
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status == 200) {
            $response = json_decode($response, true);
            Session::put('order_data', $orderData);
            Session::put('order_input_data', $data);
        } else {
            $order->delete();
            return redirect()->route('front.checkout.cancle')->withError('Payment Failed');
        }


        if (isset($response['callback'])) {
            // Save the order and redirect the user to the payment page
            $order->transaction_number = $response['tran_ref'];
            $order->save();
            return redirect($response['redirect_url']);
        } else {
            return redirect()->route('front.checkout.cancle')->withError('Payment initiation failed');
        }
    }



    public function paytabCallback(Request $request)
    {

        $response = $request->all();

        $order = Order::where('transaction_number', $response['tranRef'])->first();

        if (!$order) {
            return redirect()->route('front.checkout.cancle')->withError('Payment Failed');
        }

        if ($response['respStatus'] == 'A' && $response['respMessage'] = "Authorised") {
            $cart = Session::get('cart');
            $user = Auth::user();
            $total_tax = 0;
            $cart_total = 0;
            $total = 0;
            $option_price = 0;
            

            foreach ($cart as $key => $items) {

                $total += $items['main_price'] * $items['qty'];
                $option_price += $items['attribute_price'];
                $cart_total = $total + $option_price;
                $item = Item::findOrFail($key);
                if ($item->tax) {
                    $total_tax += $item::taxCalculate($item) * $items['qty'];
                }
            }


            $order_input_data = Session::get('order_input_data');
            if (!PriceHelper::Digital()) {
                $shipping = null;
            } else {
                $shipping = ShippingService::findOrFail($order_input_data['shipping_id']);
            }
            $discount = [];
            if (Session::has('coupon')) {
                $discount = Session::get('coupon');
            }


            $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
            $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
            $grand_total += PriceHelper::StatePrce($order_input_data['state_id'], $cart_total);

            $total_amount = PriceHelper::setConvertPrice($grand_total);


            $order->txnid = $response['tranRef'];
            $order->payment_status = 'Paid';


            $new_txn =  $new_txn = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
            $order->transaction_number = $new_txn;
            $order->save();


            PriceHelper::Transaction($order->id, $order->transaction_number, EmailHelper::getEmail(), PriceHelper::OrderTotal($order, 'trns'));
            PriceHelper::LicenseQtyDecrese($cart);
            PriceHelper::LicenseQtyDecrese($cart);

            if (Session::has('copon')) {
                $code = PromoCode::find(Session::get('copon')['code']['id']);
                $code->no_of_times--;
                $code->update();
            }

            if ($discount) {
                $coupon_id = $discount['code']['id'];
                $get_coupon = PromoCode::findOrFail($coupon_id);
                $get_coupon->no_of_times -= 1;
                $get_coupon->update();
            }



            TrackOrder::create([
                'title' => 'Pending',
                'order_id' => $order->id,
            ]);

            Notification::create([
                'order_id' => $order->id
            ]);

            $setting = Setting::first();
            if ($setting->is_twilio == 1) {
                // message
                $sms = new SmsHelper();
                $user_number = json_decode($order->billing_info, true)['bill_phone'];
                if ($user_number) {
                    $sms->SendSms($user_number, "'purchase'", $order->transaction_number);
                }
            }

            $emailData = [
                'to' => EmailHelper::getEmail(),
                'type' => "Order",
                'user_name' => isset($user) ? $user->displayName() : Session::get('billing_address')['bill_first_name'],
                'order_cost' => $total_amount,
                'transaction_number' => $order->transaction_number,
                'site_title' => Setting::first()->title,
            ];

            $setting = Setting::first();
            if ($setting->is_queue_enabled == 1) {
                dispatch(new EmailSendJob($emailData, "template"));
            } else {
                $email = new EmailHelper();
                $email->sendTemplateMail($emailData, "template");
            }
            Session::put('order_id', $order->id);
            Session::forget('cart');
            Session::forget('discount');
            Session::forget('order_data');
            Session::forget('order_payment_id');
            Session::forget('coupon');
            return redirect()->route('front.checkout.success');
        } else {
            return redirect()->route('front.checkout.cancle')->withError('Payment Field');
        }
    }
}
