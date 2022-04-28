<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourites;
use App\Models\ProductList;

class FavouriteController extends Controller
{
    public function AddFavourite(Request $request) {
        $product_code = $request->product_code;
        $email = $request->email;
        $product_id = $request->id;

        // match product đc add vào với product trong ProductList bằng product_code
        $productDetails = ProductList::where('product_code', $product_code)->get();

        Favourites::insert([
            'product_name' => $productDetails[0]['title'],
            'image' => $productDetails[0]['image'],
            'product_code' => $product_code,
            'email' => $email,
            'product_id' => $product_id
        ]);

        // return new Favourite Data
        $result = Favourites::where('email', $email)->get();
        return $result;
    }

    public function FavouriteList(Request $request) {
        $email = $request->email;
        // lấy tất cả favourite chỉ thuộc về email này thôi!!
        $result = Favourites::where('email', $email)->get();
        return $result;
    }

    public function RemoveFavourite(Request $request) {
        $product_code = $request->product_code;
        $email = $request->email;

        Favourites::where('product_code', $product_code)->where('email', $email)->delete();

        // return new Favourite Data
        $result = Favourites::where('email', $email)->get();
        return $result;
    }
}
