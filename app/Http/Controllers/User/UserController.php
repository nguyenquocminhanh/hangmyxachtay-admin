<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Image;

class UserController extends Controller
{
    public function User() {
        return Auth::user();
    }

    public function UpdateProfileUser(Request $request) {
        $name = $request->input('name');
        // $email = $request->input('email');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $country = $request->input('country');
        // để xác minh user nào cần đc update
        $id = $request->input('id');


        $user = User::findOrFail($id);
        $user->update([
            'name' => $name,
            // 'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'country' => $country
        ]);

        return $user;
    }

    public function UploadProfileImage(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],[
                'image.required' => 'Upload Your Image'
            ]);

        $file = $request->file('image');
        $id = $request->input('id');

        $imageName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
        // // resize image
        $img = Image::make($file);
        $img->resize(250, 250, function ($constraint) {
            $constraint->aspectRatio();
        });

        $resource = $img->stream()->detach();
        $folder = 'images/user_portrait/';

        $path = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $imageName,
            // file
            $resource
        );
        $path = \Storage::disk('s3')->url($path);

        // update profile image link on database
        User::findOrFail($id)->update([
            'profile_photo_path' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$imageName,
        ]);


        return response([
            'message' =>  'Upload Profile Image Successfully',
        ], 200); // Success 200 code
    }
    
}
