<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\ProductListController;
use App\Http\Controllers\Admin\ProductDetailsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\SiteInfoController;
use App\Http\Controllers\Admin\ProductCartController;
use App\Http\Controllers\Admin\VisitorController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('admin.index');
// })->name('dashboard');
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [DashboardController::class, 'DashboardPage'])->name('dashboard');

// Admin Logout Routes
// name('admin.logout') same with name of href link in html tag href="{{route('admin.logout')}}"
Route::get('/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');


Route::prefix('admin')->group(function() {
    Route::get('/user/profile', [AdminController::class, 'UserProfile'])->name('user.profile');

    Route::post('/user/profile/store',[AdminController::class, 'UserProfileStore'])->name('user.profile.store');

    // change password
    Route::get('/change/password',[AdminController::class, 'ChangePassword'])->name('change.password');

    Route::post('/change/password/update',[AdminController::class, 'ChangePasswordUpdate'])->name('change.password.update');
});


// category
Route::prefix('category')->group(function() {
    Route::get('/all', [CategoryController::class, 'GetAllCategory'])->name('all.category');

    // just return view page
    Route::get('/add',[CategoryController::class, 'AddCategory'])->name('add.category');
    // add & upload
    Route::post('/upload',[CategoryController::class, 'UploadCategory'])->name('upload.category');

    // just return edit page
    Route::get('/edit/{id}',[CategoryController::class, 'EditCategory'])->name('edit.category');
    // update
    Route::post('/update',[CategoryController::class, 'UpdateCategory'])->name('update.category');

    // delete
    Route::get('/delete/{id}',[CategoryController::class, 'DeleteCategory'])->name('delete.category');
});


// subcategory
Route::prefix('subcategory')->group(function() {
    Route::get('/all', [CategoryController::class, 'GetAllSubcategory'])->name('all.subcategory');

    // just return view page
    Route::get('/add',[CategoryController::class, 'AddSubcategory'])->name('add.subcategory');
    // add & upload
    Route::post('/upload',[CategoryController::class, 'UploadSubcategory'])->name('upload.subcategory');

    // just return edit page
    Route::get('/edit/{id}',[CategoryController::class, 'EditSubcategory'])->name('edit.subcategory');
    // update
    Route::post('/update',[CategoryController::class, 'UpdateSubcategory'])->name('update.subcategory');

    // delete
    Route::get('/delete/{id}',[CategoryController::class, 'DeleteSubcategory'])->name('delete.subcategory');
});


// Slider
Route::prefix('slider')->group(function() {
    Route::get('/all', [HomeSliderController::class, 'GetAllSlider'])->name('all.slider');

    // just return view page
    Route::get('/add',[HomeSliderController::class, 'AddSlider'])->name('add.slider');
    // add & upload
    Route::post('/upload',[HomeSliderController::class, 'UploadSlider'])->name('upload.slider');

    // just return edit page
    Route::get('/edit/{id}',[HomeSliderController::class, 'EditSlider'])->name('edit.slider');
    // update
    Route::post('/update',[HomeSliderController::class, 'UpdateSlider'])->name('update.slider');

    // delete
    Route::get('/delete/{id}',[HomeSliderController::class, 'DeleteSlider'])->name('delete.slider');
});

// Product
Route::prefix('product')->group(function() {
    Route::get('/all', [ProductListController::class, 'GetAllProduct'])->name('all.product');

    // just return view page
    Route::get('/add',[ProductListController::class, 'AddProduct'])->name('add.product');
    // add & upload
    Route::post('/upload',[ProductListController::class, 'UploadProduct'])->name('upload.product');

    // just return edit page
    Route::get('/edit/{id}',[ProductListController::class, 'EditProduct'])->name('edit.product');
    // update
    Route::post('/update',[ProductListController::class, 'UpdateProduct'])->name('update.product');

    // delete
    Route::get('/delete/{id}',[ProductListController::class, 'DeleteProduct'])->name('delete.product');
});


// Contact Message
Route::get('/all/message', [ContactController::class, 'GetAllMessage'])->name('contact.message');
// delete
Route::get('/delete/message/{id}', [ContactController::class, 'DeleteMessage'])->name('delete.message');


// Product Review
Route::get('/all/review', [ProductReviewController::class, 'GetAllReview'])->name('all.review');
// delete
Route::get('/delete/review/{id}', [ProductReviewController::class, 'DeleteReview'])->name('delete.review');


// Site Info 
Route::get('/get/siteinfo', [SiteInfoController::class, 'GetSiteInfo'])->name('get.siteinfo');
// update
Route::post('/update/siteinfo',[SiteInfoController::class, 'UpdateSiteInfo'])->name('update.siteinfo');


// Orders
Route::prefix('order')->group(function() {
    Route::get('/pending', [ProductCartController::class, 'GetAllPending'])->name('pending.order');
    Route::get('/processing', [ProductCartController::class, 'GetAllProcessing'])->name('processing.order');
    Route::get('/completed', [ProductCartController::class, 'GetAllCompleted'])->name('completed.order');
    Route::get('/canceled', [ProductCartController::class, 'GetAllCanceled'])->name('canceled.order');

    // details
    Route::get('/details/{id}', [ProductCartController::class, 'GetOrderDetails'])->name('order.details');

    // pending to processing
    Route::get('/status/processing/{id}', [ProductCartController::class, 'PendingToProcessing'])->name('pending.processing');
    // processing to completed
    Route::get('/status/completed/{id}', [ProductCartController::class, 'ProcessingToCompleted'])->name('processing.completed');
    // canceled to delete
    Route::get('/status/delete/{id}', [ProductCartController::class, 'CanceledToDelete'])->name('canceled.delete');
});


// Calendar
Route::get('/calendar', function () {
    return view('backend.calendar.calendar_view');
})->name('calendar');



// User
Route::get('/all/user', [VisitorController::class, 'GetAllUser'])->name('all.user');

