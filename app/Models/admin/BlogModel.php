<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class BlogModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_blog';
    protected $primaryKey = 'blogId';
    public $timestamps = true;
    protected $fillable = [
        'title', 'slug', 'image', 'excerpt', 'content', 'category', 'author', 'views'
    ];

    protected static function booted()
    {
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function getAllBlogs()
    {
        return DB::table($this->table)->orderBy('created_at', 'desc')->get();
    }

    public function getBlogById($id)
    {
        return DB::table($this->table)->where('blogId', $id)->first();
    }

    public function addBlog($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateBlog($id, $data)
    {
        return DB::table($this->table)->where('blogId', $id)->update($data);
    }

    public function deleteBlog($id)
    {
        return DB::table($this->table)->where('blogId', $id)->delete();
    }
}
