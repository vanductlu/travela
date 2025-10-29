<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'tbl_blog';
    protected $primaryKey = 'blogId';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'excerpt',
        'content',
        'category',
        'author',
        'views',
    ];
}
