<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class BlogController extends Controller
{
     // Trang danh sách bài viết
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(5);
        $recent = Blog::orderBy('created_at', 'desc')->take(3)->get();
        $categories = Blog::select('category')->distinct()->pluck('category');
        $title = 'Bài viết du lịch';

        return view('clients.blog', compact('blogs', 'recent','categories', 'title'));
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
    // Tìm kiếm blog
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('blog');
        }

        // Tìm kiếm trong title, excerpt, content, category, author
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

    // Lọc theo danh mục
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
    public function like($id)
    {
    $blog = Blog::findOrFail($id);
    $blog->increment('likes');
    return response()->json(['likes' => $blog->likes]);
    }

    public function comment(Request $request, $id)
    {
    // Kiểm tra user trong session
    if (!$request->session()->has('username')) {
        // Chuyển hướng đến trang login và lưu lại URL hiện tại để redirect sau khi đăng nhập
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
        'parent_id' => $request->parent_id, // nếu có parent_id
        'created_at' => now(),
    ]);

    return back()->with('success', 'Bình luận đã được gửi!');
    }

}
