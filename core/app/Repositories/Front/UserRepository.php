<?php

namespace App\Repositories\Front;

use App\{
    Models\User,
    Models\Setting,
    Helpers\EmailHelper,
    Models\Notification
};
use App\Helpers\ImageHelper;
use App\Jobs\EmailSendJob;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserRepository
{

    public function register($request)
    {


        $input = $request->all();

        $user = new User;
        $input['password'] = bcrypt($request['password']);
        $input['email'] = $input['email'];
        $input['first_name'] = $input['first_name'];
        $input['last_name'] = $input['last_name'];
        $input['phone'] = $input['phone'];
        $verify = rand(pow(10, 6 - 1), pow(10, 6) - 1);
        $input['email_token'] = $verify;
        $user->fill($input)->save();


        Notification::create(['user_id' => $user->id]);
        $emailData = [
            'to' => $user->email,
            'subject' => "Email Verification",
            'body' => "Your verification code is " . $verify,
        ];
        $setting = Setting::first();

        if ($setting->is_mail_verify == 1) {
            if ($setting->is_queue_enabled == 1) {
                dispatch(new EmailSendJob($emailData));
            } else {
                $email = new EmailHelper();
                $email->sendCustomMail($emailData, "custom");
            }
        }
    }





    public function profileUpdate($request)
    {

        $input = $request->all();
        if ($request['user_id']) {
            $user = User::findOrFail($request['user_id']);
        } else {
            $user = Auth::user();
        }


        if ($request->password) {
            $input['password'] = bcrypt($input['password']);
            $user->password = $input['password'];
            $user->update();
        } else {
            unset($input['password']);
        }
      

        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file, 'images', $user, 'images', 'photo');
        }

        if ($request->newsletter) {
            if (!Subscriber::where('email', $user->email)->exists()) {
                Subscriber::insert([
                    'email' => $user->email
                ]);
            }
        } else {
            Subscriber::where('email', $user->email)->delete();
        }

        $user->fill($input)->save();
    }
}
