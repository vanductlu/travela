<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TourBookedController extends Controller
{
    public function index()
    {   
        $title = 'Tour đã đặt';
        return view('clients.tour-booked',compact('title'));
    }
}
