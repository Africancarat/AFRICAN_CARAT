<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Order,
    Models\PaymentSetting,
    Traits\StripeCheckout,
    Traits\MollieCheckout,
    Traits\PaypalCheckout,
    Traits\PaystackCheckout,
    Http\Controllers\Controller,
    Http\Requests\PaymentRequest,
    Traits\CashOnDeliveryCheckout,
    Traits\BankCheckout,
};
use App\Helpers\FinancingHelper;
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Setting;
use App\Models\ShippingService;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mollie\Laravel\Facades\Mollie;
use Stripe\Price;

class CheckoutController extends Controller
{

    use StripeCheckout {
        StripeCheckout::__construct as private __stripeConstruct;
    }
    use PaypalCheckout {
        PaypalCheckout::__construct as private __paypalConstruct;
    }
    use MollieCheckout {
        MollieCheckout::__construct as private __MollieConstruct;
    }


    use BankCheckout;
    use PaystackCheckout;
    use CashOnDeliveryCheckout;

    public function __construct()
    {
        $setting = Setting::first();
        if ($setting->is_guest_checkout != 1) {
            $this->middleware('auth');
        }
        $this->middleware('localize');
        $this->__stripeConstruct();
        $this->__paypalConstruct();
    }

    public function checkoutPage()
    {

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total  + $total_tax);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;


        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        $this->attachCheckoutFinanceContext($data);

        return view('front.checkout.index', $data);
    }

    public function ship_address()
    {
        $setting = Setting::first();

        if ($setting->is_single_checkout == 1) {
            return redirect(route("front.checkout"));
        }


        Session::forget('shipping_address');
        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        $this->attachCheckoutFinanceContext($data);

        return view('front.checkout.billing', $data);
    }



    public function billingStore(Request $request)
    {
        // laravel validation
        $request->validate([
            'bill_first_name' => 'required',
            'bill_last_name' => 'required',
            'bill_email' => 'required|email',
            'bill_phone' => 'required',
            'bill_address1' => 'required',
            'bill_city' => 'required',
            'bill_zip' => 'required',
        ]);

        if ($request->same_ship_address) {
            Session::put('billing_address', $request->all());

            if (PriceHelper::CheckDigital()) {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->bill_last_name,
                    "ship_email" => $request->bill_email,
                    "ship_phone" => $request->bill_phone,
                    "ship_company" => $request->bill_company,
                    "ship_address1" => $request->bill_address1,
                    "ship_address2" => $request->bill_address2,
                    "ship_zip" => $request->bill_zip,
                    "ship_city" => $request->bill_city,
                    "ship_country" => $request->bill_country,
                ];
            } else {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->bill_last_name,
                    "ship_email" => $request->bill_email,
                    "ship_phone" => $request->bill_phone,
                ];
            }
            Session::put('shipping_address', $shipping);
        } else {
            Session::put('billing_address', $request->all());
            Session::forget('shipping_address');
        }

        if (Session::has('shipping_address')) {
            return redirect()->route('front.checkout.payment');
        } else {
            return redirect()->route('front.checkout.shipping');
        }
    }


    public function shipping()
    {

        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        $this->attachCheckoutFinanceContext($data);

        return view('front.checkout.shipping', $data);
    }

    public function shippingStore(Request $request)
    {

        // laravel validation
        $request->validate([
            'ship_first_name' => 'required',
            'ship_last_name' => 'required',
            'ship_email' => 'required|email',
            'ship_phone' => 'required',
            'ship_address1' => 'required',
            'ship_zip' => 'required',
            'ship_city' => 'required',
        ]);

        Session::put('shipping_address', $request->all());
        return redirect(route('front.checkout.payment'));
    }



    public function payment()
    {
        if (!Session::has('billing_address')) {
            return redirect(route('front.checkout.billing'));
        }

        if (!Session::has('shipping_address')) {
            return redirect(route('front.checkout.shipping'));
        }


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total  + $total_tax);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;


        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        $this->attachCheckoutFinanceContext($data);

        return view('front.checkout.payment', $data);
    }

    public function checkout(PaymentRequest $request)
    {



        PriceHelper::checkCheckout($request);

        $input = $request->all();

        $checkout = false;
        $payment_redirect = false;
        $payment = null;

        if (Session::has('currency')) {
            $currency = Currency::findOrFail(Session::get('currency'));
        } else {
            $currency = Currency::where('is_default', 1)->first();
        }


        $usd_supported = array(
            "USD",
            "AED",
            "AFN",
            "ALL",
            "AMD",
            "ANG",
            "AOA",
            "ARS",
            "AUD",
            "AWG",
            "AZN",
            "BAM",
            "BBD",
            "BDT",
            "BGN",
            "BIF",
            "BMD",
            "BND",
            "BOB",
            "BRL",
            "BSD",
            "BWP",
            "BYN",
            "BZD",
            "CAD",
            "CDF",
            "CHF",
            "CLP",
            "CNY",
            "COP",
            "CRC",
            "CVE",
            "CZK",
            "DJF",
            "DKK",
            "DOP",
            "DZD",
            "EGP",
            "ETB",
            "EUR",
            "FJD",
            "FKP",
            "GBP",
            "GEL",
            "GIP",
            "GMD",
            "GNF",
            "GTQ",
            "GYD",
            "HKD",
            "HNL",
            "HTG",
            "HUF",
            "IDR",
            "ILS",
            "INR",
            "ISK",
            "JMD",
            "JPY",
            "KES",
            "KGS",
            "KHR",
            "KMF",
            "KRW",
            "KYD",
            "KZT",
            "LAK",
            "LBP",
            "LKR",
            "LRD",
            "LSL",
            "MAD",
            "MDL",
            "MGA",
            "MKD",
            "MMK",
            "MNT",
            "MOP",
            "MUR",
            "MVR",
            "MWK",
            "MXN",
            "MYR",
            "MZN",
            "NAD",
            "NGN",
            "NIO",
            "NOK",
            "NPR",
            "NZD",
            "PAB",
            "PEN",
            "PGK",
            "PHP",
            "PKR",
            "PLN",
            "PYG",
            "QAR",
            "RON",
            "RSD",
            "RUB",
            "RWF",
            "SAR",
            "SBD",
            "SCR",
            "SEK",
            "SGD",
            "SHP",
            "SLE",
            "SOS",
            "SRD",
            "STD",
            "SZL",
            "THB",
            "TJS",
            "TOP",
            "TRY",
            "TTD",
            "TWD",
            "TZS",
            "UAH",
            "UGX",
            "UYU",
            "UZS",
            "VND",
            "VUV",
            "WST",
            "XAF",
            "XCD",
            "XOF",
            "XPF",
            "YER",
            "ZAR",
            "ZMW"
        );


        $paypal_supported = ['USD', 'EUR', 'AUD', 'BRL', 'CAD', 'HKD', 'JPY', 'MXN', 'NZD', 'PHP', 'GBP', 'RUB'];
        $paystack_supported = ['NGN', "GHS", "USD", "ZAR", "KES"];
        switch ($input['payment_method']) {

            case 'Stripe':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->stripeSubmit($input);
                break;

            case 'Paypal':
                if (!in_array($currency->name, $paypal_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->paypalSubmit($input);
                break;


            case 'Mollie':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->MollieSubmit($input);
                break;

            case 'Paystack':
                if (!in_array($currency->name, $paystack_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment = $this->PaystackSubmit($input);

                break;

            case 'Bank':
                $checkout = true;
                $payment = $this->BankSubmit($input);
                break;

            case 'Paytabs':
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->PayTabsSubmit($input);
                break;

            case 'Cash On Delivery':
                $checkout = true;
                $payment = $this->cashOnDeliverySubmit($input);
                break;
        }



        if ($checkout) {
            if ($payment_redirect) {

                if ($payment['status']) {
                    return redirect()->away($payment['link']);
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            } else {
                if ($payment['status']) {
                    return redirect()->route('front.checkout.success');
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentRedirect(Request $request)
    {
        $responseData = $request->all();

        if (isset($responseData['session_id'])) {
            $payment = $this->stripeNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } elseif (Session::has('order_payment_id')) {
            $payment = $this->paypalNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function mollieRedirect(Request $request)
    {

        $responseData = $request->all();

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));
        $responseData['payment_id'] = $payment->id;
        if ($payment->status == 'paid') {
            $payment = $this->mollieNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentSuccess()
    {
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            $order = Order::find($order_id);
            $cart = json_decode($order->cart, true);
            $setting = Setting::first();
            if ($setting->is_twilio == 1) {
                // message
                $sms = new SmsHelper();
                $user_number = $order->user->phone;
                if ($user_number) {
                    $sms->SendSms($user_number, "'purchase'");
                }
            }
            return view('front.checkout.success', compact('order', 'cart'));
        }
        return redirect()->route('front.index');
    }



    public function paymentCancle()
    {
        $message = '';
        if (Session::has('message')) {
            $message = Session::get('message');
            Session::forget('message');
        } else {
            $message = __('Payment Failed!');
        }
        Session::flash('error', $message);
        return redirect()->route('front.checkout.billing');
    }

    public function stateSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];
        if ($shipping_id) {
            $shipping = ShippingService::findOrFail($shipping_id);
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id) {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    public function shippingSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = ShippingService::findOrFail($shipping_id);

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id && $state_id != 'undefined') {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['shipping_price'] = PriceHelper::setCurrencyPrice($shipping->price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    /** EMI / BNPL panel + checkout experience partials reuse the same principal total. */
    private function attachCheckoutFinanceContext(array &$data): void
    {
        $data['financing_summary'] = FinancingHelper::plansForPrincipalInr((float) $data['grand_total']);
    }
}
