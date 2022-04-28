<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartOrder;
use App\Models\User;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function DashboardPage() {
        $numberOfOrders = CartOrder::count();

        $numberOfUsers = User::count();

        $numberOfMessages = Contact::count();

        $orders = CartOrder::orderBy('id', 'DESC')->paginate(10);

        $revenue = 0;

        foreach ($orders as $item) {
            $revenue = $revenue + $item->total_price;
        }


        return view('admin.index', compact('numberOfOrders', 'numberOfUsers', 'numberOfMessages', 'revenue', 'orders'));
    }
}
