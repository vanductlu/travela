<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToursController extends Controller
{
    public function index()
    {   
        $title = 'Tours';
        return view('clients.tours',compact('title'));
    }
}
