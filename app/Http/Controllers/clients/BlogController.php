<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\clients\Blog;
class BlogController extends Controller
{
     // Trang danh sách bài viết
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(5);
        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $title = 'Bài viết du lịch';

        return view('clients.blog', compact('blogs', 'recent', 'title'));
    }

    // Trang chi tiết bài viết
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        // Tăng lượt xem
        $blog->increment('views');

        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $title = $blog->title;

        return view('clients.blog-details', compact('blog', 'recent', 'title'));
    }
}
