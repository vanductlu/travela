<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'tbl_contact';
    
    protected $fillable = [
        'fullName',
        'phoneNumber',
        'email',
        'message',
        'isReply'
    ];
    
    public $timestamps = true;
}
