<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index()
    {   
        $title = 'Hồ sơ cá nhân';
        return view('clients.userprofile',compact('title'));
    }
}
