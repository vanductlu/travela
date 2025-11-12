<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'tbl_team'; // bảng team
    protected $fillable = [
        'name',
        'designation',
        'image',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'status',
    ];
}
