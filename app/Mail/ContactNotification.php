<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct($contactData)
    {
        $this->contactData = $contactData;
    }

    public function build()
{
    return $this->subject('Thông báo liên hệ mới từ website')
                ->view('clients.mail.contact-notification')
                ->with([
                    'contactData' => [
                        'fullName' => $this->contactData['fullName'],
                        'email' => $this->contactData['email'],
                        'phoneNumber' => $this->contactData['phoneNumber'],
                        'message' => $this->contactData['message'],
                    ]
                ]);
}
}