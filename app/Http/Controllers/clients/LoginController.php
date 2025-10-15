<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Login;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
class LoginController extends Controller
{
    private $login;

    public function __construct()
    {
        $this->login = new Login();
    }

    public function index()
    {
        $title = 'Đăng nhập';
        return view('clients.login', compact('title'));
    }

   
    public function register(Request $request)
    {
        $username_regis = $request->username_regis;
        $email = $request->email;
        $password_regis = $request->password_regis;

        $checkAccountExist = $this->login->checkUserExist($username_regis, $email);
        if ($checkAccountExist) {
            return response()->json([
                'success' => false,
                'message' => 'Tên người dùng hoặc email đã tồn tại!'
            ]);
        }

        $activation_token = Str::random(60); // Tạo token ngẫu nhiên
        // Nếu không tồn tại, thực hiện đăng ký
        $dataInsert = [
            'username'         => $username_regis,
            'email'            => $email,
            'password'         => md5($password_regis),
            'activation_token' => $activation_token
        ];

        $this->login->registerAcount($dataInsert);

        //Gửi email kích hoạt
        // $this->sendActivationEmail($email, $activation_token);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.'
        ]);
    }
}