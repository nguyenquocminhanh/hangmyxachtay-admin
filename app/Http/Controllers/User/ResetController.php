<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Http\Requests\ResetRequest;
use DB;
use Illuminate\Support\Facades\Hash;

class ResetController extends Controller
{
    public function ResetPassword(ResetRequest $request) {

        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);

        // return type bool
        // check match 
        $emailcheck = DB::table('password_resets')->where('email', $email)->first();
        $pincheck = DB::table('password_resets')->where('token', $token)->first();

        if (!$emailcheck) {
            return response([
                'message' => "Email Not Found"
            ], 401);
        }
        if (!$pincheck) {
            return response([
                'message' => "Pin Code Invalid"
            ], 401);
        }

        // update new password
        DB::table('users')->where('email', $email)->update(['password' => $password]);

        // delete all record related to this email in table password_resets
        DB::table('password_resets')->where('email', $email)->delete();

        return response([
            'message' => 'Password Changed Successfully'
        ], 200);

    }

    public function ChangePassword(Request $request) {
        $email = $request->input('email');
        $oldPassword = $request->input('oldpassword');
        $password = Hash::make($request->input('password'));
    
        // check oldpassword match
        $user = User::where('email', $email)->get();
        $hashedPassword = $user[0]['password'];

        if (Hash::check($oldPassword, $hashedPassword)) {
            // update new password
            DB::table('users')->where('email', $email)->update(['password' => $password]);
            return response([
                'message' => 'Password Changed Successfully'
            ], 200);
        } else {
            return response([
                'message' => "Your Current Password Is Invalid"
            ], 401);
        }
    }

}
