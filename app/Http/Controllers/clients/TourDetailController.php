<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use Illuminate\Http\Request;

class TourDetailController extends Controller
{   
    private $tours;
    public function __construct()
    {
        $this->tours = new Tours();
    }
    public function index($id=0)
    {   
        $title = 'Chi tiết tour';
        
        $tourDetail = $this->tours->getTourDetail($id);
        return view('clients.tour-detail',compact('title','tourDetail'));
    }
}
