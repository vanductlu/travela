<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Login;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\clients\User;
use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    private $login;
    protected $user;
    public function __construct()
    {
        $this->login = new Login();
        $this->user = new User();
    }
    protected function redirectTo()
    {
    if (request()->has('redirect')) {
        return request()->get('redirect');
    }

    return '/';
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

        $activation_token = Str::random(60);
        $dataInsert = [
            'username'         => $username_regis,
            'email'            => $email,
            'password'         => md5($request->password_regis),
            'activation_token' => $activation_token
        ];

        $this->login->registerAcount($dataInsert);

        $this->sendActivationEmail($email, $activation_token);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.'
        ]);
    }

    public function sendActivationEmail($email, $token)
    {
        $activation_link = route('activate.account', ['token' => $token]);

        Mail::send('clients.mail.emails_activation', ['link' => $activation_link], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Kích hoạt tài khoản của bạn');
        });
    }
    public function activateAccount($token)
    {
        $user = $this->login->getUserByToken($token);
        if ($user) {
            $this->login->activateUserAccount($token);

            return redirect('/login')->with('message', 'Tài khoản của bạn đã được kích hoạt!');
        } else {
            return redirect('/login')->with('error', 'Mã kích hoạt không hợp lệ!');
        }
    }
       public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $data_login = [
            'username' => $username,
            'password' => md5($password)
        ];

        $user_login = $this->login->login($data_login);
        $userId = $this->user->getUserId($username);
        $user = $this->user->getUser($userId);

        if ($user_login != null) {
            $request->session()->put('username', $username);
            $request->session()->put('avatar', $user->avatar);
            toastr()->success("Đăng nhập thành công!",'Thông báo');
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirectUrl' => route('home'),
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin tài khoản không chính xác!',
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('username');
        $request->session()->forget('avatar');
        $request->session()->forget('userId');
        toastr()->success("Đăng xuất thành công!",'Thông báo');
        return redirect()->route('home');
    } 
}