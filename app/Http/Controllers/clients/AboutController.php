<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Coupon;
class AboutController extends Controller
{
    public function index()
    {   
        $couponModel = new Coupon();
        $activeCoupons = $couponModel->getActiveCoupons();
        $title = 'Giới thiệu';
        return view('clients.about', compact('title', 'activeCoupons'));
    }
}
