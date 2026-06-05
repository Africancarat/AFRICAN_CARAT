<?php

namespace App\Http\Controllers\Auth\User;

use App\{
  Http\Controllers\Controller,
  Http\Requests\AuthRequest,
};
use App\Helpers\EmailHelper;
use App\Jobs\EmailSendJob;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
  public function __construct()
  {

    $this->middleware('guest', ['except' => ['logout', 'userLogout', 'verifySubmit']]);

    $setting = Setting::first();
    if ($setting->recaptcha == 1) {
      Config::set('captcha.sitekey', $setting->google_recaptcha_site_key);
      Config::set('captcha.secret', $setting->google_recaptcha_secret_key);
    }
  }

  public function showForm()
  {

    return view('user.auth.login');
  }

  public function login(AuthRequest $request)
  {

    // Attempt to log the user in
    if (Auth::attempt(['email' => $request->login_email, 'password' => $request->login_password])) {
      // if successful, then redirect to their intended location
      $setting = Setting::first();

      if (!Auth::user()->email_verify && $setting->is_mail_verify == 1) {
        Session::flash('error', __('Email not verify ! Please check your email for verification code.'));

        $user = Auth::user();
        $verify = rand(pow(10, 6 - 1), pow(10, 6) - 1);
        $emailData = [
          'to' => $user->email,
          'subject' => "Email Verification",
          'body' => "Your verification code is " . $verify,
        ];
        $user->update(['email_token' => $verify]);


        if ($setting->is_mail_verify == 1) {
          if ($setting->is_queue_enabled == 1) {
            dispatch(new EmailSendJob($emailData));
          } else {
            $email = new EmailHelper();
            $email->sendCustomMail($emailData, "custom");
          }
          Auth::logout();
          return redirect()->route("user.verify");
        }
      }


      if ($request->has('modal')) {
        return redirect()->back();
      } else {
        return redirect()->intended(route('user.dashboard'));
      }
    }

    // if unsuccessful, then redirect back to the login with the form data
    Session::flash('error', __('Email Or Password Doesn\'t Match !'));
    return redirect()->back();
  }

  public function showVerifyForm()
  {

    return view('user.auth.verify');
  }

  public function verifySubmit(Request $request)
  {


    $user = User::where('email_token', $request->verify)->first();
    if (!$user) {
      Session::flash('error', __("Verify Code Doesn't Match !"));
      return redirect()->back();
    }
    if ($user->email_token == $request->verify) {
      $user->update(['email_token' => null, 'email_verify' => 1]);

      $setting = Setting::first();
      $emailData = [
        'to' => $user->email,
        'type' => "Registration",
        'user_name' => $user->displayName(),
        'order_cost' => '',
        'transaction_number' => '',
        'site_title' => Setting::first()->title,
      ];

      if ($setting->is_mail_verify == 1) {
        dispatch(new EmailSendJob($emailData,"template"));
      } else {
        $email = new EmailHelper();
        $email->sendTemplateMail($emailData, "template");
      }

      Auth::login($user);
      return redirect()->route('user.dashboard')->withSuccess(__('Email Verified Successfully.'));
    }


    // if unsuccessful, then redirect back to the login with the form data
    Session::flash('error', __('Email Or Password Doesn\'t Match !'));
    return redirect()->back();
  }

  public function logout()
  {
    Auth::logout();
    return redirect('/');
  }
}
