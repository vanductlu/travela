<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Tours;

class ToursController extends Controller
{   
    private $tours;
    public function __construct()
    {
        $this->tours = new Tours();
    }
    public function index()
    {   
        $title = 'Tours';
        $tours = $this->tours->getAllTours();
        return view('clients.tours',compact('title','tours'));
    }
}
