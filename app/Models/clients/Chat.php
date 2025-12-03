<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{

    protected $table = 'tbl_chat';
    protected $primaryKey = 'chatId';

    public $timestamps = false;

    protected $fillable = ['userId', 'role', 'messages', 'createdDate'];
}
