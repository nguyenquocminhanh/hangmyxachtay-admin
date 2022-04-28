<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Http\Requests\ForgetRequest;
use DB;
use Illuminate\Support\Facades\Hash;

use Mail;
use App\Mail\ForgetMail;

class ForgetController extends Controller
{
    public function ForgetPassword(ForgetRequest $request) {
        $email = $request->email;

        // check if email is exist
        // NOT EXISTS
        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'Email Invalid',
            ], 401);
        }


        // EXISTS email
        // generate Random Token
        $token = rand(10, 100000);

        try {
            DB::table('password_resets')->insert([
                // table password_resets in DB is used for record reset password cases
                'email' => $email,
                'token' => $token
            ]);

            // Mail send to User
            // send with template in Mail\ForgetMail
            Mail::to($email)->send(new ForgetMail($token));
            
            return response([
                'message' => 'Reset Password Mail Sent to your email'
            ], 200); 

        } catch(Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }

    }
}
