<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ProductList;
use App\Models\CartOrder;

class ProductReviewController extends Controller
{
    public function ReviewList(Request $request) {
        $productCode = $request->product_code;
        $result = ProductReview::where('product_code', $productCode)->orderBy('id', 'DESC')->limit(6)->get();
        return $result;
    } // end method

    public function PostReview(Request $request) {
        // POST
        $productName = $request->input('product_name');
        $productCode = $request->input('product_code');
        $userName = $request->input('reviewer_name');
        $userPhoto = $request->input('reviewer_photo');
        $userRating = $request->input('reviewer_rating');
        $userComments = $request->input('reviewer_comments');
        // để set order đã review
        $orderId = $request->input('id');

        date_default_timezone_set("America/New_York");
        $review_date = date("d-m-Y"); 

        $result = ProductReview::insert([
            'product_name' => $productName,
            'product_code' => $productCode,
            'reviewer_name' => $userName,
            'reviewer_photo' => $userPhoto,
            'reviewer_rating' => $userRating,
            'reviewer_comments' => $userComments,
            'review_date' => $review_date
        ]);

        // Calculate Review Stars of Product and update ProductList
        $productReviews = ProductReview::where('product_code', $productCode)->get();
        $numberOfReviews = ProductReview::where('product_code', $productCode)->count();

        $stars = 0;
        foreach ($productReviews as $review) {
            $stars = $stars + $review->reviewer_rating;
        }
        $averageRating = round(($stars / $numberOfReviews), 1);

        // update product rate
        $productList = ProductList::where('product_code', $productCode)->get();
        $newNumberOfRate = intval($productList[0]['number_rate']) + 1;

        ProductList::where('product_code', $productCode)->update([
            'star' => $averageRating,
            'number_rate' => $newNumberOfRate,
        ]);

        // update productCart already reviewed
        CartOrder::find($orderId)->update([
            'is_reviewed' => 1
        ]);


        return $result;
    }




    // Back-end
    public function GetAllReview() {
        $review = ProductReview::latest()->paginate(10);
        return view('backend.review.review_all', compact('review'));
    }

    public function DeleteReview($id) {
        ProductReview::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Review Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
