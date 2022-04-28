<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Image;

class CategoryController extends Controller
{
    public function AllCategory() {
        $categories = Category::all();

        $categoriesArray = [];

        // ********************** IMPORTANT ********************** //
        foreach ($categories as $category) {
            $subcategory = Subcategory::where('category_name', $category['category_name'])->get();

            $item = [
                'category_name' => $category['category_name'],
                'category_image' => $category['category_image'],
                'subcategories' => $subcategory
            ];

            array_push($categoriesArray, $item);
        }


        return $categoriesArray;
    }

    // backend
    public function GetAllCategory() {
        $category = Category::latest()->get();
        return view('backend.category.category_view', compact('category'));
    }

    public function AddCategory() {
        return view('backend.category.category_add');
    }

    public function UploadCategory(Request $request) {
        $request->validate([
            'category_name' => 'required',
            'category_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],[
                'category_name.required' => 'Input Your Category Name'
            ]);

        // $image = $request->file('category_image');
        // $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        // // Image Intervention Package
        // Image::make($image)->resize(128, 128)->save('upload/category/'.$name_gen);

        // $save_url = 'http://127.0.0.1:8000/upload/category/'.$name_gen;

        
        $file = $request->file('category_image');
        // set name file
        $imageName = $file->getClientOriginalName();
        // resize image
        $img = Image::make($file);
        $img->resize(128, 128, function ($constraint) {
            $constraint->aspectRatio();
        });

        //detach method is the key! Hours to find it... :/
        $resource = $img->stream()->detach();
        $folder = 'images/category/';

        $path = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $imageName,
            // file
            $resource
        );
        $path = \Storage::disk('s3')->url($path);
        
        Category::insert([
            'category_name' => $request->category_name,
            'category_image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$imageName
        ]);

        // notification
        $notification = array(
            'message' => 'Category Uploaded Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.category')->with($notification);

    }

    //----------------------------------------------------------------//

    public function EditCategory($id) {
        $category = Category::findOrFail($id);
        return view('backend.category.category_edit', compact('category'));
    }

    public function UpdateCategory(Request $request) {
        // từ input hidden
        $category_id = $request->id;

        if ($request->file('category_image')) {         // admin có chọn hình mới
            // DELETE hình cũ tron AWS S3
            $category = Category::findOrFail($category_id);
            $image_link = $category['category_image'];
            // old location in AWS S3
            $location = str_replace('https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/', '', $image_link);
            // delete in aws3 bucket
            $path = \Storage::disk('s3')->delete($location);

            // ---------------------------------------//

            // y như upload ở trên!!! ADD hình mới vào DB và AWS S3
            $file = $request->file('category_image');
            // set new file_name
            $image_name = $file->getClientOriginalName();
            // resize
            $img = Image::make($file);
            $img->resize(128, 128, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource = $img->stream()->detach();
            $folder = 'images/category/';
            
            // save to AWS S3
            $path = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $image_name,
                // file
                $resource
            );
            $path = \Storage::disk('s3')->url($path);
            
            // KHÁC CHỖ NÀY !!! UPDATE, SAVE hình mới vào DB
            Category::findOrFail($category_id)->update([
                'category_name' => $request->category_name,
                'category_image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$image_name,
            ]);
    
            // notification
            $notification = array(
                'message' => 'Category Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.category')->with($notification);
        } else {
            Category::findOrFail($category_id)->update([
                'category_name' => $request->category_name
            ]);
    
            // notification
            $notification = array(
                'message' => 'Category Updated Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.category')->with($notification);
        }
    }

    public function DeleteCategory($id) {
        $category = Category::findOrFail($id);
        $image_link = $category['category_image'];
        $location = str_replace('https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/', '', $image_link);
        // delete in aws3 bucket
        $path = \Storage::disk('s3')->delete($location);

        // delete in database
        $category->delete();
        // notification
        $notification = array(
            'message' => 'Category Deleted Successfully',
                'alert-type' => 'success'
        );
  
        return redirect()->back()->with($notification);
    }


    // Subcategory
    public function GetAllSubcategory() {
        $subcategory = Subcategory::latest()->get();
        return view('backend.subcategory.subcategory_view', compact('subcategory'));
    }

    public function AddSubcategory() {
        $category = Category::latest()->get();
        return view('backend.subcategory.subcategory_add', compact('category'));
    }

    public function UploadSubcategory(Request $request) {
        $request->validate([
            'subcategory_name' => 'required'
            ],[
                'subcategory_name.required' => 'Input Your Subcategory Name'
            ]);

        Subcategory::insert([
            'category_name' => $request->category_name,
            'subcategory_name' => $request->subcategory_name,
        ]);

        // notification
        $notification = array(
            'message' => 'Subcategory Uploaded Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.subcategory')->with($notification);
    }

    // edit subcategory
    public function EditSubcategory($id) {
        $category = Category::orderBy('category_name', "ASC")->get();

        $subcategory = Subcategory::findOrFail($id);
        return view('backend.subcategory.subcategory_edit', compact('category', 'subcategory'));
    }

    public function UpdateSubcategory(Request $request) {
        $subcategory_id = $request->id;

        Subcategory::findOrFail($subcategory_id)->update([
            'category_name' => $request->category_name,
            'subcategory_name' => $request->subcategory_name,
        ]);

        // notification
        $notification = array(
            'message' => 'Subcategory Updated Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.subcategory')->with($notification);
    }

    public function DeleteSubcategory($id) {
        Subcategory::findOrFail($id)->delete();
        $notification = array(
            'message' => 'SubCategory Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
