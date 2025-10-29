<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\BlogModel;
use Illuminate\Support\Str;
class BlogManagementController extends Controller
{
    protected $blogModel;
    public function __construct()
    {
        $this->blogModel = new BlogModel();
    }
    public function index()
    {
        $title = "Quản lý bài viết";
        $blogs = $this->blogModel->getAllBlogs();
        return view('admin.blog', compact('title', 'blogs'));
    }

    public function create()
    {
        $title = "Thêm bài viết mới";
        $blogs = $this->blogModel->getAllBlogs();
        return view('admin.blog', compact('title', 'blogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('clients/assets/images/blog'), $imageName);
        }

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'image' => $imageName,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'category' => $request->category,
            'author' => 'Admin',
            'views' => 0,
        ];

        $this->blogModel->addBlog($data);
        return redirect()->route('admin.blog')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit($id)
    {
        $title = "Sửa bài viết";
        $blog = $this->blogModel->getBlogById($id);
        $blogs = $this->blogModel->getAllBlogs();
        if (!$blog) {
            return redirect()->route('admin.blog')->with('error', 'Bài viết không tồn tại.');
        }
        return view('admin.blog', compact('title', 'blog', 'blogs'));
    }

    public function update(Request $request, $id)
    {
        $blog = $this->blogModel->getBlogById($id);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'category' => $request->category,
        ];

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('clients/assets/images/blog'), $imageName);
            $data['image'] = $imageName;
        }

        $this->blogModel->updateBlog($id, $data);
        return redirect()->route('admin.blog')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy($id)
    {
        $this->blogModel->deleteBlog($id);
        return redirect()->route('admin.blog')->with('success', 'Xóa bài viết thành công!');
    }
}
