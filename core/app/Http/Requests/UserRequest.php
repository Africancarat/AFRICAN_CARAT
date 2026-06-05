<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = Auth::check() ? ',' . Auth::user()->id : '';
        $setting = Setting::first();
        $password = Auth::check() ? '' : 'required|';
        $check = Auth::check() ? 'nullable|min:6|max:16' : "min:6|max:16|confirmed";

        $recaptcha = $setting->recaptcha == 1 && !Auth::check() ? 'required|captcha' : 'nullable';

        return [
            'g-recaptcha-response' => $recaptcha,
            'first_name' => $password.'|max:255',
            'photo'      => [
            'mimes:jpeg,jpg,png,svg', 
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['jpeg', 'jpg', 'png', 'svg'];
                    if ($value->isValid() && $value->isFile()) {
                        if (!in_array($value->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'])) {
                            return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                        }
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (!in_array($extension, $allowedExtensions)) {
                            return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                        }
                    } else {
                        return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                    }
                },
                'max:2048'
            ],
            'last_name'  => 'required|max:255',
            'phone'      => 'required|max:255',
            'email'      => Auth::guard('admin') ? 'required|email': 'required|email|unique:users,email'. $id,
            'password'   => $password.$check,
            'password_confirmation'   => $password,
            'honeypot'   => 'max:0',
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
            'first_name.required' => __('First Name is required.'),
            'last_name.required' => __('Last Name field is required.'),
            'country.required' => __('Country is required.'),
            'city.required' => __('City is required.'),
            'address.required' => __('Address is required.'),
            'zip.required' => __('Zip Code is required.'),
            'phone.required' => __('Phone Number is required.'),
            'email.required' => __('Email field is required.'),
            'email.email'   => __('The email must be a valid email address.'),
            'password.required'    => __('Password field is required.'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again later or contact site admin.'),
            'honeypot.max' => __('Please verify that you are not a robot.'),
        ];
    }
}
