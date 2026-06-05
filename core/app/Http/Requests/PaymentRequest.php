<?php

namespace App\Http\Requests;

use App\Helpers\PriceHelper;
use App\Models\ShippingService;
use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        if(PriceHelper::CheckDigital() == false){
            return [];
        }
        $state = State::whereStatus(1)->count() != 0  ? 'required' : '';
        
        $shipping = ShippingService::whereStatus(1)->count() == 0 || PriceHelper::CheckDigital() == true? 'required' : '';

        if($this->single_page_checkout == 1){
            return [
                'state_id' => $state,
                "shipping_id" => $shipping,
                'bill_first_name' => 'required',
                'bill_last_name' => 'required',
                'bill_email' => 'required',
                'bill_phone' => 'required',
                'bill_address1' => 'required',
                'bill_city' => 'required',
                'bill_zip' => 'required',
            ];
        }else{
            return [
                'state_id' => $state,
                "shipping_id" => $shipping,
            ];
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'state_id.required'   => __('Please select your shipping state.'),
            'shipping_id.required'   => __('Please select your shipping method.'),
        ];
    }

}
