<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// import Model
use App\Models\Visitor;
use App\Models\User;

class VisitorController extends Controller
{
    public function GetVisitorDetails() {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        date_default_timezone_set("America/New_York");
        $visit_time = date("h:i:sa");
        $visit_date = date("d-m-Y");

        $result = Visitor::insert([
            'ip_address' => $ip_address,
            'visit_time' => $visit_time,
            'visit_date' => $visit_date
        ]);

        return $result;
    } // end method

    public function GetAllUser() {
        $user = User::orderBy('id', 'ASC')->paginate(10);
        return view('backend.user.user_all', compact('user'));
    }
} 
