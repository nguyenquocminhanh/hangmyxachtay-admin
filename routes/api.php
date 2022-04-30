<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// import Controllers
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SiteInfoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\ProductListController;
use App\Http\Controllers\Admin\ProductDetailsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ProductCartController;
use App\Http\Controllers\Admin\FavouriteController;

// User Controllers
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ForgetController;
use App\Http\Controllers\User\ResetController;
use App\Http\Controllers\User\UserController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Get Visitor
Route::get('/getvisitor', [VisitorController::class, 'GetVisitorDetails']);

// Contact Page Route
Route::post('/postcontact', [ContactController::class, 'PostContactDetails']);

// SiteInfo Page Route
Route::get('/allsiteinfo', [SiteInfoController::class, 'AllSiteInfo']);

// All Category Route
Route::get('/allcategory', [CategoryController::class, 'AllCategory']);

// ProductList Route
// by remark : FEATURED / NEW/ COLLECTION
Route::get('/productlistbyremark/{remark}', [ProductListController::class, 'ProductListByRemark']);
// by category
Route::get('/productlistbycategory/{category}', [ProductListController::class, 'ProductListByCategory']);
// by subcategory
Route::get('/productlistbysubcategory/{category}/{subcategory}', [ProductListController::class, 'ProductListBySubategory']);

// Search Route
Route::get('/search/{key}', [ProductListController::class, 'ProductBySearch']);

// Similar Route
Route::get('/similar/{subcategory}/{product_code}', [ProductListController::class, 'SimilarProduct']);

// ProductDetails Route
Route::get('/productdetails/{id}', [ProductDetailsController::class, 'ProductDetails']);

// Home Slide Route
Route::get('/allslider', [HomeSliderController::class, 'AllSlider']);

// Notification Route
Route::get('/notification', [NotificationController::class, 'NotificationHistory']);

// ProductReview Route
Route::get('/reviewlist/{product_code}', [ProductReviewController::class, 'ReviewList']);
// Post ProductReview Route
Route::post('/postreview', [ProductReviewController::class, 'PostReview']);

// ProductCart Route
Route::post('/addtocart', [ProductCartController::class, 'AddToCart']);
// Cart Count
Route::get('/cartcount/{email}', [ProductCartController::class, 'CartCount']);
// Cart List 
Route::get('/cartlist/{email}', [ProductCartController::class, 'CartList']);
// Remove Cart List Item
Route::get('/removecartlist/{id}/{email}', [ProductCartController::class, 'RemoveCartList']);
// Increase Cart List Item
Route::get('/cartitemplus/{id}/{quantity}/{price}/{email}', [ProductCartController::class, 'CartItemPlus']);
// Decrease Cart List Item
Route::get('/cartitemminus/{id}/{quantity}/{price}/{email}', [ProductCartController::class, 'CartItemMinus']);
// Check Soldout
Route::get('/checksoldout/{id}', [ProductCartController::class, 'CheckSoldOut']);

// Cart Order Route
Route::post('/cartorder', [ProductCartController::class, 'CartOrder']);
// Cart Order Route
Route::get('/orderlistbyuser/{email}', [ProductCartController::class, 'OrderListByUser']);
// Cart Order Route
Route::get('/orderlistbyuser/{email}', [ProductCartController::class, 'OrderListByUser']);
// Cancel Order Route
Route::get('/cancelorder/{id}', [ProductCartController::class, 'CancelOrder']);



// Favourite Route
Route::get('/addfavourite/{product_code}/{email}/{id}', [FavouriteController::class, 'AddFavourite']);
// Favourite List
Route::get('/favouritelist/{email}', [FavouriteController::class, 'FavouriteList']);
// Remove Favorite Product
Route::get('/removefavourite/{product_code}/{email}', [FavouriteController::class, 'RemoveFavourite']);


////////////////////// User Login, Register API Start //////////////////////////
Route::post('/login', [AuthController::class, 'Login']);

Route::post('/register', [AuthController::class, 'Register']);

////////////////////// User Login, Register API End ////////////////////////////


////////////////////// User Forget Password, Reset Password API Start //////////////////////////
// Forget Password Routes
Route::post('/forgetpassword', [ForgetController::class, 'ForgetPassword']);

// Reset Password Routes
Route::post('/resetpassword', [ResetController::class, 'ResetPassword']);

// Change Password Routes
Route::post('/changepassword', [ResetController::class, 'ChangePassword']);

////////////////////// User Forget Password, Reset Password API End //////////////////////////

// Current User Routes
// middleware is to check if user login or not, if yes, get user info
Route::get('/user', [UserController::class, 'User'])->middleware('auth:api');
Route::post('/user/updateprofile', [UserController::class, 'UpdateProfileUser']);


// Profile Image Upload
Route::post('/user/uploadimage', [UserController::class, 'UploadProfileImage']);