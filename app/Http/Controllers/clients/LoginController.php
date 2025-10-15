<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
   
    public function index()
    {
        $title = 'Đăng nhập';
        return view('clients.login', compact('title'));
    }

   
    public function register()
    {
        
    }
}
