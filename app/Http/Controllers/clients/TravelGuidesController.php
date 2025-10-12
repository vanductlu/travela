<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TravelGuidesController extends Controller
{
    public function index()
    {   
        $title = 'Hướng dẫn viên';
        return view('clients.travel-guides',compact('title'));
    }
}
