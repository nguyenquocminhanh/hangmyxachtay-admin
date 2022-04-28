<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function AdminLogout() {
        Auth::logout();

        return Redirect()->route('login');
    }

    public function UserProfile() {
        $adminData = User::find(1);
        
        // compact truyền data qua trang đó
        return view('backend.admin.admin_profile', compact('adminData'));
    }

    public function UserProfileStore(Request $request) {
        $data = User::find(1);
        // value of name of input elemet
        $data->name = $request->name;
        $data->email = $request->email;

        if ($request->file('profile_photo_path')) {
            // file hình của input image
            $file = $request->file('profile_photo_path');
            // remove old picture
            @unlink(public_path('upload/admin_images/'.$data->profile_photo_path));
            // đặt tên cho input image
            $fileName = date('YmdHi').$file->getClientOriginalName();

            // lưu hình vào trong upload.admin_images
            // trong HTML code hình đc lấy từ trong folder này
            $file->move(public_path('upload/admin_images'), $fileName);

            // lưu tên file vào trong database
            $data['profile_photo_path'] = $fileName;
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('user.profile')->with($notification);
    }

    public function ChangePassword() {
        return view('backend.admin.change_password');
    }

    public function ChangePasswordUpdate(Request $request) {
        
        $validatedata = $request->validate([
            'oldpassword' => 'required',
            'password' => 'required|confirmed'
        ]);


        $hashedPassword = User::find(1)->password;
        if (Hash::check($request->oldpassword, $hashedPassword)) {
            $user = User::find(1);
            $user->password = Hash::make($request->password);
            $user->save();
            Auth::logout();

            return redirect()->route('admin.logout');
        } else {
            return redirect()->back();
        }
    }
}
