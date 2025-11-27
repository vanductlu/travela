<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;

    public function __construct($customerName)
    {
        $this->customerName = $customerName;
    }

    public function build()
    {
        return $this->subject('Cảm ơn bạn đã liên hệ với RaveLo')
                    ->view('clients.mail.contact-auto-reply');
    }
}