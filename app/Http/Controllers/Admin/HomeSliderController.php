<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSlider;
use Image;

class HomeSliderController extends Controller
{
    public function AllSlider() {
        $result = HomeSlider::all();
        return $result;
    }

    public function GetAllSlider() {
        $slider = HomeSlider::latest()->get();
        return view('backend.slider.slider_view', compact('slider'));
    }

    public function AddSlider() {
        return view('backend.slider.slider_add');
    }

    public function uploadSlider(Request $request) {
        $request->validate([
            'slider_image' => 'required'
            ],[
                'slider_image.required' => 'Upload Slider Image'
            ]);

        $file = $request->file('slider_image');
        // set name file
        $imageName = $file->getClientOriginalName();
        // resize image
        $img = Image::make($file);
        $img->resize(1024, 450, function ($constraint) {
            $constraint->aspectRatio();
        });

        //detach method is the key! Hours to find it... :/
        $resource = $img->stream()->detach();
        $folder = 'images/slider/';

        $path = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $imageName,
            // file
            $resource
        );
        $path = \Storage::disk('s3')->url($path);


        // $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        // // Image Intervention Package
        // Image::make($image)->resize(1024, 450)->save('upload/slider/'.$name_gen);

        // $save_url = 'http://127.0.0.1:8000/upload/slider/'.$name_gen;


        HomeSlider::insert([
            'slider_image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$imageName
        ]);

        // notification
        $notification = array(
            'message' => 'Slider Uploaded Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.slider')->with($notification);
    }

    public function EditSlider($id) {
        $slider = HomeSlider::findOrFail($id);
        return view('backend.slider.slider_edit', compact('slider'));
    }

    public function UpdateSlider(Request $request) {
        // từ input hidden
        $slider_id = $request->id;

        // DELETE hình cũ tron AWS S3
        $slider = HomeSlider::findOrFail($slider_id);
        $image_link = $slider['slider_image'];
        // old location in AWS S3
        $location = str_replace('https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/', '', $image_link);
        // delete in aws3 bucket
        $path = \Storage::disk('s3')->delete($location);

        // ---------------------------------------//

        // y như upload ở trên!!! ADD hình mới vào DB và AWS S3
        $file = $request->file('slider_image');

        // set new file_name
        $image_name = $file->getClientOriginalName();
        // resize
        $img = Image::make($file);
        $img->resize(1024, 450, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource = $img->stream()->detach();
        $folder = 'images/slider/';
        
        // save to AWS S3
        $path = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $image_name,
            // file
            $resource
        );
        $path = \Storage::disk('s3')->url($path);

        // KHÁC CHỖ NÀY !!! UPDATE
        HomeSlider::findOrFail($slider_id)->update([
            'slider_image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$image_name
        ]);

        // notification
        $notification = array(
            'message' => 'Slider Updated With Image Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.slider')->with($notification);
   
      
    }

    public function DeleteSlider($id) {
        $slider = HomeSlider::findOrFail($id);
        $image_link = $slider['slider_image'];
        $location = str_replace('https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/', '', $image_link);
        // delete in aws3 bucket
        $path = \Storage::disk('s3')->delete($location);

        // delete in database
        $slider->delete();

        // notification
        $notification = array(
            'message' => 'Slider Deleted Successfully',
                'alert-type' => 'success'
        );
  
        return redirect()->back()->with($notification);
    }
}
