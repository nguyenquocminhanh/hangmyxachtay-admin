<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCart;
use App\Models\ProductList;
use App\Models\CartOrder;

class ProductCartController extends Controller
{
    public function AddToCart(Request $request) {
        // $request->id: get from url of API link {id}
        // input('email) : get from users' input
        // POST
        $email = $request->input('email');
        $size = $request->input('size');
        $color = $request->input('color');
        $quantity = $request->input('quantity');
        $product_code = $request->input('product_code');         // IMPORTANT!!! //
        $product_id = $request->input('product_id'); 

        // match product đc add vào với product trong ProductList bằng product_code
        $productDetails = ProductList::where('product_code', $product_code)->get();
        $price = $productDetails[0]['price'];
        $special_price = $productDetails[0]['special_price'];

        if ($special_price == null) {
            $total_price = $price * $quantity;
            $unit_price = $price;
        } else {
            $total_price = $special_price * $quantity;
            $unit_price = $special_price;
        }

        // same product, size, color in cart, just update quantity
        $sameProductInCart = ProductCart::where([
            ['product_code', '=', $product_code],
            ['size', '=', "Size: ".$size],
            ['color', '=', "Color: ".$color],
            ['email', '=', $email],
        ])->get();
        if (count($sameProductInCart) > 0) {        // exist same product in card -> update (combine)
            $newQuantity = $quantity + $sameProductInCart[0]['quantity'];
            $newTotalPrice = $total_price + $sameProductInCart[0]['total_price'];

            $sameProductInCart[0]->update([
                'quantity' => $newQuantity,
                'unit_price' => $unit_price,        // update price hiện tại
                'total_price' => $newTotalPrice,
            ]);
        } else {        // add new product
            ProductCart::insert([
                'email' => $email,
                'image' => $productDetails[0]['image'],
                'product_name' => $productDetails[0]['title'],
                'product_code' => $productDetails[0]['product_code'],
                'size' => "Size: ".$size,
                'color' => "Color: ".$color,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'total_price' => $total_price,
                'product_id' =>  $product_id
            ]);
        }

        // return list of products in cart
        $products = ProductCart::where('email', $email)->get();

        return $products;
    }   // end method

    public function CartCount(Request $request) {
        $email = $request->email;

        $products = ProductCart::where('email', $email)->get();
        $result = 0;
        foreach($products as $item) {
            $result = $result + $item['quantity'];
        }

        return $result;
    }

    public function CartList(Request $request) {
        $email = $request->email;

        $result = ProductCart::where('email', $email)->get();
        return $result;
    }

    public function RemoveCartList(Request $request) {
        $id = $request->id;
        $email = $request->email;

        ProductCart::where('id', $id)->delete();
        
        // return list of products in cart
        $products = ProductCart::where('email', $email)->get();

        return $products;
    }

    public function CartItemPlus(Request $request) {
        $id = $request->id;
        $email = $request->email;
        $quantity = $request->quantity;
        // unit_price
        $price = $request->price;

        $newQuantity = $quantity + 1;
        $total_price =  $newQuantity * $price;
        
       ProductCart::where('id', $id)->update([
            'quantity' => $newQuantity,
            'total_price' => $total_price
        ]);

        // return list of products in cart
        $products = ProductCart::where('email', $email)->get();

        return $products;
    }

    public function CartItemMinus(Request $request) {
        $id = $request->id;
        $email = $request->email;
        $quantity = $request->quantity;
        // unit_price
        $price = $request->price;

        $newQuantity = $quantity - 1;

        // delete Item when quantity equal 0
        if ($newQuantity == 0) {
            ProductCart::where('id', $id)->delete();
        } else {
            $total_price =  $newQuantity * $price;
        
            ProductCart::where('id', $id)->update([
                'quantity' => $newQuantity,
                'total_price' => $total_price
            ]);
        }

        // return list of products in cart
        $products = ProductCart::where('email', $email)->get();

        return $products;
    }

    public function CheckSoldOut(Request $request) {
        $product_code = $request->id;
        $product = ProductList::where([
            ['product_code', '=', $product_code],
        ])->get();
        if ($product[0]['soldout'] == 1) {          // soldout
            return 1;
        } else {                                    // not yet
            return 0;
        }
    }


    
    // copy toàn bộ item confirmed order trong product_cart vào trong cart_order + thông tin ng order
    // then, remove orders in product_cart
    public function CartOrder(Request $request) {
        // POST; input from user
        $city = $request->input('city');
        // keyword payment_method same as in DB
        $paymentMethod = $request->input('payment_method');
        $yourName = $request->input('name');
        $email = $request->input('email');
        $deliveryAddress = $request->input('delivery_address');            
        $invoiceNumber = $request->input('invoice_number');      
        $deliveryCharge = $request->input('delivery_charge');// IMPORTANT!!! //

        date_default_timezone_set("America/New_York");
        $request_time = date("h:i:sa");
        $request_date = date("d-m-Y");

        $cartList = ProductCart::where('email', $email)->get();

        // foreach LOOP check sold out
        foreach($cartList as $cartListItem) {
            $productDetails = ProductList::where('product_code', $cartListItem['product_code'])->get();
            $is_soldout = $productDetails[0]['soldout'];
            if ($is_soldout == 1) {
                return response([
                    'message' => 'Some Of Your Products Was Sold Out, Please ReFresh Cart Page To Get Updated'
                ], 401);
            }
        }

        // foreach LOOP
        foreach($cartList as $cartListItem) {
            $cartInsertDeleteResult = "";

            $resultInsert = CartOrder::insert([
                'invoice_number' => "Easy".$invoiceNumber,
                // copy
                'product_name' => $cartListItem['product_name'],
                'product_code' => $cartListItem['product_code'],
                'size' => $cartListItem['size'],
                'color' => $cartListItem['color'],
                'quantity' => $cartListItem['quantity'],
                'unit_price' => $cartListItem['unit_price'],
                'total_price' => $cartListItem['total_price'],

                // input field
                'email' => $email,
                'name' => $yourName,
                'payment_method' => $paymentMethod,
                'delivery_address' => $deliveryAddress,
                'city' => $city,
                'delivery_charge' => $deliveryCharge,

                'order_date' => $request_date,
                'order_time' => $request_time,
                'order_status' => "Pending",
            ]);

            // REMOVE items in product_cart
            if ($resultInsert == 1) {   // SUCCESSFULLY
                $resultDelete = ProductCart::where('id', $cartListItem['id'])->delete();
                if ($resultDelete == 1) {   // SUCCESSFULLY 
                    $cartInsertDeleteResult = 1;
                } else {
                    $cartInsertDeleteResult = 0;
                }
            }
        };

        return $cartInsertDeleteResult;
    }


    public function OrderListByUser(Request $request) {
        $email = $request->email;
        $result = CartOrder::where('email', $email)->orderBy('id', 'DESC')->get();
        return $result;
    }

    public function CancelOrder(Request $request) {
        $id = $request->id;
        $result = CartOrder::findOrFail($id)->update([
            'order_status' => 'Canceled'
        ]);
        return $result;
    }


    // BACK-END
    public function GetAllPending() {
        $orders = CartOrder::where('order_status', 'Pending')->orderBy('id', 'DESC')->get();

        return view('backend.order.pending_order', compact('orders'));
    }

    public function GetAllProcessing() {
        $orders = CartOrder::where('order_status', 'Processing')->orderBy('id', 'DESC')->get();

        return view('backend.order.processing_order', compact('orders'));
    }

    public function GetAllCompleted() {
        $orders = CartOrder::where('order_status', 'Completed')->orderBy('id', 'DESC')->get();

        return view('backend.order.completed_order', compact('orders'));
    }

    public function GetAllCanceled() {
        $orders = CartOrder::where('order_status', 'Canceled')->orderBy('id', 'DESC')->get();

        return view('backend.order.canceled_order', compact('orders'));
    }


    public function GetOrderDetails($id) {
        $order = CartOrder::findOrFail($id);


        return view('backend.order.order_details', compact('order'));
    }


    public function PendingToProcessing($id) {
        CartOrder::findOrFail($id)->update([
            'order_status' => 'Processing'
        ]);

        $notification = array(
            'message' => 'Order Processing Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('processing.order')->with($notification);
    }

    public function ProcessingToCompleted($id) {
        CartOrder::findOrFail($id)->update([
            'order_status' => 'Completed'
        ]);

        $notification = array(
            'message' => 'Order Completed Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('completed.order')->with($notification);
    }

    public function CanceledToDelete($id) {
        CartOrder::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Order Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('canceled.order')->with($notification);
    }
}
