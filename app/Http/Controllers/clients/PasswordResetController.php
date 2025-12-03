<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class PasswordResetController extends Controller
{
    protected $table = 'tbl_users';
    public function showForgotForm()
    {
        $title = "Quên mật khẩu";
        return view('clients.auth.forgot', compact('title'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = DB::table($this->table)->where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email không tồn tại trong hệ thống!');
        }

        $token = Str::random(60);
        $expire = Carbon::now()->addMinutes(30);

        DB::table($this->table)
            ->where('email', $request->email)
            ->update([
                'reset_token' => $token,
                'reset_token_expire' => $expire
            ]);

        $reset_link = route('password.reset.form', ['token' => $token]);
        Mail::send(
            'clients.mail.reset_password',
            ['link' => $reset_link],
            function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Đặt lại mật khẩu');
            }
        );

        return back()->with('message', 'Đã gửi email đặt lại mật khẩu!');
    }

    public function showResetForm($token)
    {   
        $title = "Đặt lại mật khẩu";
    
        $check = DB::table($this->table)
            ->where('reset_token', $token)
            ->where('reset_token_expire', '>', Carbon::now())
            ->first();

        if (!$check) {
            return redirect('/forgot-password')->with('error', 'Token không hợp lệ hoặc đã hết hạn!');
        }

        return view('clients.auth.reset', compact('token', 'title'));
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'username' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.required' => 'Vui lòng nhập username',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ]);

        $tokenCheck = DB::table($this->table)
            ->where('reset_token', $request->token)
            ->where('reset_token_expire', '>', Carbon::now())
            ->first();

        if (!$tokenCheck) {
            return redirect('/forgot-password')->with('error', 'Token không hợp lệ hoặc đã hết hạn!');
        }
        $user = DB::table($this->table)
            ->where('reset_token', $request->token)
            ->where('username', $request->username)
            ->first();

        if (!$user) {
            return back()->with('error', 'Username không khớp với tài khoản yêu cầu đặt lại mật khẩu!')
                        ->withInput();
        }

        DB::table($this->table)
            ->where('userId', $user->userId)
            ->update([
                'password' => md5($request->password),
                'reset_token' => null,
                'reset_token_expire' => null,
            ]);

        return redirect('/login')->with('message', 'Mật khẩu đã được đặt lại thành công!');
    }
}
