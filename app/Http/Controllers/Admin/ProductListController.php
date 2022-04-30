<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductList;
use App\Models\ProductDetails;
use App\Models\Category;
use App\Models\Subcategory;
use Image;


class ProductListController extends Controller
{
    public function ProductListByRemark(Request $request) {
        // remark from id of api
        $remark = $request->remark;
        $productlist = ProductList::where('remark', $remark)->get();
        return $productlist;

    } // end method

    public function ProductListByCategory(Request $request) {
        $category = $request->category;
        $productlist = ProductList::where('category', $category)->get();
        return $productlist;
    } // end method

    public function ProductListBySubategory(Request $request) {
        $category = $request->category;
        $subcategory = $request->subcategory;
        $productlist = ProductList::where('category', $category)->where('subcategory', $subcategory)->get();
        return $productlist;

    } // end method

    public function ProductBySearch(Request $request) {
        $key = $request->key;
        // key nằm anywhere trong title, trước hay sau cũng OK
        // **************************************************** //
        // orWhere: hoặc là khớp kia cũng OK
        $productList = ProductList::where('title', 'LIKE', "%{$key}%")->orWhere('brand', 'LIKE', "%{$key}%")->orWhere('product_code', 'LIKE', "%{$key}%")->get();
        return $productList;

    }  // end method

    public function SimilarProduct(Request $request) {
        $subcategory = $request->subcategory;
        $product_code = $request->product_code;
        $productlist = ProductList::where(
            ['subcategory', '=', $subcategory])->orderBy('id', 'desc')->limit(12)->get();
        return $productlist;
    } // end method




    //------------ BACK-END -------------//
    public function GetAllProduct() {
        $products = ProductList::latest()->orderBy('id', 'DESC')->paginate(10);
        return view('backend.product.product_all', compact('products'));
    }

    public function AddProduct() {
        $category = Category::orderBy('category_name', 'ASC')->get();
        $subcategory = Subcategory::orderBy('subcategory_name', 'ASC')->get();


        return view('backend.product.product_add', compact('category', 'subcategory'));
    }

    public function UploadProduct(Request $request) {
        $request->validate([
            'product_code' => 'required',
            ],[
                'product_code.required' => 'Input Product Code',
            ]);
        
        $image = $request->file('image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        // resize image
        $img = Image::make($image);
        $img->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource = $img->stream()->detach();
        $folder = 'images/product/'.hexdec(uniqid()).'/';

        $path = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $name_gen,
            // file
            $resource
        );
        $path = \Storage::disk('s3')->url($path);

        // Image Intervention Package
        // lưu vào trong máy tính
        // Image::make($image)->resize(400, 400)->save('upload/product/'.$name_gen);

        // $save_url = 'http://127.0.0.1:8000/upload/product/'.$name_gen;
        $soldout = 0;
        if ($request->soldout != null) {
            $soldout = $request->soldout;
        }

        // INSERT INTO PRODUCT LIST TABLE 
        $product_id = ProductList::insertGetId([    // insert and get the ID
            'title' => $request->title,
            'price' => $request->price,
            'special_price' => $request->special_price,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'remark' => $request->remark,
            'brand' => $request->brand,
            'star' => 0,
            'number_rate' => 0,
            'soldout' => $soldout,
            'product_code' => $request->product_code,
            'image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen
        ]);

        // INSERT INTO PRODUCT DETAILS TABLE 
        $image1 = $request->file('image_one');
        $name_gen1 = hexdec(uniqid()).'.'.$image1->getClientOriginalExtension();

        // resize image
        $img1 = Image::make($image1);
        $img1->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource1 = $img1->stream()->detach();
        // $folder = 'images/product/'.hexdec(uniqid()).'/';

        $path1 = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $name_gen1,
            // file
            $resource1
        );
        $path1 = \Storage::disk('s3')->url($path1);
        
        $image2 = $request->file('image_two');
        $name_gen2 = hexdec(uniqid()).'.'.$image2->getClientOriginalExtension();
        // resize image
        $img2 = Image::make($image2);
        $img2->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource2 = $img2->stream()->detach();
        // $folder = 'images/product/'.hexdec(uniqid()).'/';

        $path2 = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $name_gen2,
            // file
            $resource2
        );
        $path2 = \Storage::disk('s3')->url($path2);
        
        $image3 = $request->file('image_three');
        $name_gen3 = hexdec(uniqid()).'.'.$image3->getClientOriginalExtension();
         // resize image
        $img3 = Image::make($image3);
        $img3->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource3 = $img3->stream()->detach();
        // $folder = 'images/product/'.hexdec(uniqid()).'/';

        $path3 = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $name_gen3,
            // file
            $resource3
        );
        $path3 = \Storage::disk('s3')->url($path3);

        $image4 = $request->file('image_four');
        $name_gen4 = hexdec(uniqid()).'.'.$image4->getClientOriginalExtension();
        // resize image
        $img4 = Image::make($image4);
        $img4->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resource4 = $img4->stream()->detach();
        // $folder = 'images/product/'.hexdec(uniqid()).'/';

        $path4 = \Storage::disk('s3')->put(
            // location and file name to save
            $folder . $name_gen4,
            // file
            $resource4
        );
        $path4 = \Storage::disk('s3')->url($path4);

        ProductDetails::insert([
            'product_id' => $product_id,
            'image_one' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen1,
            'image_two' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen2,
            'image_three' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen3,
            'image_four' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen4,
            'short_description' => $request->short_description,
            'color' => $request->color,
            'size' => $request->size,
            'long_description' => $request->long_description
        ]);

        // notification
        $notification = array(
            'message' => 'Product Uploaded Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.product')->with($notification);
    }

    public function EditProduct($id) {
        $category = Category::orderBy('category_name', "ASC")->get();
        $subcategory = Subcategory::orderBy('subcategory_name', 'ASC')->get();

        $product = ProductList::findOrFail($id);
        $details = ProductDetails::where('product_id', $id)->get();

        return view('backend.product.product_edit', compact('category', 'subcategory', 'product', 'details'));
    }

    public function UpdateProduct(Request $request) {
        // hidden id
        $product_id = $request->id;
        $soldout = $request->soldout != 1 ? 0 : 1;

        $request->validate([
            'product_code' => 'required'
            ],[
                'product_code.required' => 'Input Product Code'
            ]);

        $folder = 'images/product/'.hexdec(uniqid()).'/';

        // xét xem có bỏ hình mới vô ko!!!
        if ($request->file('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            // resize image
            $img = Image::make($image);
            $img->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource = $img->stream()->detach();

            $path = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $name_gen,
                // file
                $resource
            );
            $path = \Storage::disk('s3')->url($path);

            // INSERT INTO PRODUCT LIST TABLE 
            // find theo ID
            ProductList::findOrFail($product_id)->update([    // hidden ID
                'title' => $request->title,
                'price' => $request->price,
                'special_price' => $request->special_price,
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'remark' => $request->remark,
                'brand' => $request->brand,
                'product_code' => $request->product_code,
                'soldout' => $soldout,
                'image' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen
            ]);
        } else {
            // INSERT INTO PRODUCT LIST TABLE 
            // find theo ID
            ProductList::findOrFail($product_id)->update([    // hidden ID
                'title' => $request->title,
                'price' => $request->price,
                'special_price' => $request->special_price,
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'remark' => $request->remark,
                'brand' => $request->brand,
                'product_code' => $request->product_code,
                'soldout' => $soldout,
                // 'image' => $save_url
            ]);
        }

        // INSERT INTO PRODUCT DETAILS TABLE 
        if ($request->file('image_one')) {
            $image1 = $request->file('image_one');
            $name_gen1 = hexdec(uniqid()).'.'.$image1->getClientOriginalExtension();

            // resize image
            $img1 = Image::make($image1);
            $img1->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource1 = $img1->stream()->detach();

            $path1 = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $name_gen1,
                // file
                $resource1
            );
            $path1 = \Storage::disk('s3')->url($path1);
        }

        if ($request->file('image_two')) {
            $image2 = $request->file('image_two');
            $name_gen2 = hexdec(uniqid()).'.'.$image2->getClientOriginalExtension();
            // resize image
            $img2 = Image::make($image2);
            $img2->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource2 = $img2->stream()->detach();

            $path2 = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $name_gen2,
                // file
                $resource2
            );
            $path2 = \Storage::disk('s3')->url($path2);
        }
        
        if ($request->file('image_three')) {
            $image3 = $request->file('image_three');
            $name_gen3 = hexdec(uniqid()).'.'.$image3->getClientOriginalExtension();
            // resize image
            $img3 = Image::make($image3);
            $img3->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource3 = $img3->stream()->detach();

            $path3 = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $name_gen3,
                // file
                $resource3
            );
            $path3 = \Storage::disk('s3')->url($path3);
        }

        if ($request->file('image_four')) {
            $image4 = $request->file('image_four');
            $name_gen4 = hexdec(uniqid()).'.'.$image4->getClientOriginalExtension();
            // resize image
            $img4 = Image::make($image4);
            $img4->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resource4 = $img4->stream()->detach();

            $path4 = \Storage::disk('s3')->put(
                // location and file name to save
                $folder . $name_gen4,
                // file
                $resource4
            );
            $path4 = \Storage::disk('s3')->url($path4);
        }

        // ONLY DIFFERENT BETWEEN ADD AND UPDATE!!!!!!! <--------
        // find theo cột giá trị
        if ($request->file('image_one') || $request->file('image_two') || $request->file('image_three') || $request->file('image_four')) {
            ProductDetails::where('product_id', $product_id)->update([
                // 'product_id' => $product_id,
                'image_one' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen1,
                'image_two' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen2,
                'image_three' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen3,
                'image_four' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$folder.$name_gen4,
                'short_description' => $request->short_description,
                'color' => $request->color,
                'size' => $request->size,
                'long_description' => $request->long_description
            ]);
        } else {
            ProductDetails::where('product_id', $product_id)->update([
                // 'product_id' => $product_id,
                // 'image_one' => $save_url1,
                // 'image_two' => $save_url2,
                // 'image_three' => $save_url3,
                // 'image_four' => $save_url4,
                'short_description' => $request->short_description,
                'color' => $request->color,
                'size' => $request->size,
                'long_description' => $request->long_description
            ]);
        }

        // notification
        $notification = array(
            'message' => 'Product Updated Successfully',
            'alert-type' => 'success'
        );

        // same page    
        return redirect()->route('all.product')->with($notification);
    }

    public function DeleteProduct($id) {
        ProductList::findOrFail($id)->delete();
        ProductDetails::where('product_id', $id)->delete();

        $notification = array(
           'message' => 'Product Deleted Successfully',
           'alert-type' => 'success'
       );

       return redirect()->back()->with($notification);
    }


}
