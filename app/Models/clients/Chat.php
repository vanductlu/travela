<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    // Ánh xạ tới bảng tbl_chat và khóa chính là id
    protected $table = 'tbl_chat';
    protected $primaryKey = 'chatId';

    // Vô hiệu hoá created_at, updated_at mặc định
    public $timestamps = false;

    // Các cột có thể gán
    protected $fillable = ['userId', 'role', 'messages', 'createdDate'];
}
