<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Contact;
use App\Mail\ContactNotification;
use App\Mail\ContactAutoReply;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
class ContactController extends Controller
{

    public function index()
    {
        $title = 'Liên hệ';
        return view('clients.contact', compact('title'));
    }

    public function createContact(Request $req)
    {
        $validated = $req->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'message.required' => 'Vui lòng nhập nội dung',
        ]);

        try {
            $contact = Contact::create([
                'fullName' => $req->name,
                'phoneNumber' => $req->phone_number,
                'email' => $req->email,
                'message' => $req->message,
                'isReply' => 'n'
            ]);
            try {
                Mail::to('nvd2k3@gmail.com')->send(
                    new ContactNotification([
                        'fullName' => $req->name,
                        'phoneNumber' => $req->phone_number,
                        'email' => $req->email,
                        'message' => $req->message
                    ])
                );

                Mail::to($req->email)->send(new ContactAutoReply($req->name));
                
                Log::info('Contact email sent successfully to: ' . $req->email);
                
            } catch (Exception $mailError) {
                Log::error('Mail sending failed: ' . $mailError->getMessage());
                Log::error('Mail error trace: ' . $mailError->getTraceAsString());
                toastr()->warning('Đã lưu thông tin liên hệ. Chúng tôi sẽ phản hồi sớm!');
                return redirect()->back();
            }

            toastr()->success('Gửi thành công! Vui lòng kiểm tra email để nhận xác nhận.');
            
        } catch (Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            toastr()->error('Có lỗi xảy ra: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
