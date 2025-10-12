<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyTourController extends Controller
{
    private $tours;
    public function index()
    {   
        $title = 'Tour của tôi';
        return view('clients.mytour',compact('title'));
    }
}
    
