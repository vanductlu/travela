<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(5);
        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $categories = Blog::select('category')->distinct()->pluck('category');
        $title = 'Bài viết du lịch';

        return view('clients.blog', compact('blogs', 'recent','categories', 'title'));
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $blog->increment('views');

        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $title = $blog->title;

        return view('clients.blog-details', compact('blog', 'recent', 'title'));
    }
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('blog');
        }
        $blogs = Blog::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('excerpt', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->orWhere('category', 'LIKE', "%{$query}%")
                    ->orWhere('author', 'LIKE', "%{$query}%")
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);

        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $categories = Blog::select('category')->distinct()->pluck('category');
        $title = 'Kết quả tìm kiếm: ' . $query;

        return view('clients.blog-search', compact('blogs', 'recent', 'query', 'categories', 'title'));
    }

    public function category($category)
    {
        $blogs = Blog::where('category', $category)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $categories = Blog::select('category')->distinct()->pluck('category');
        $title = 'Danh mục: ' . $category;

        return view('clients.blog', compact('blogs', 'recent', 'categories', 'title'));
    }
    public function comment(Request $request, $id)
    {
    if (!$request->session()->has('username')) {
        return redirect()->route('login', ['redirect' => url()->previous()])
                         ->with('error', 'Vui lòng đăng nhập để bình luận.');
    }
    $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    DB::table('tbl_comments')->insert([
        'blog_id' => $id,
        'name' => session('username'),
        'content' => $request->content,
        'parent_id' => $request->parent_id,
        'created_at' => now(),
    ]);

    return back()->with('success', 'Bình luận đã được gửi!');
    }

}
