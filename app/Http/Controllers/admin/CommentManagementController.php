<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CommentManagementController extends Controller
{
     public function index()
    {
        $title = "Quản lý bình luận";

        $comments = DB::table('tbl_comments as c')
            ->leftJoin('tbl_blog as b', 'b.blogId', '=', 'c.blog_id')
            ->select('c.*', 'b.title as blog_title')
            ->orderBy('c.created_at', 'desc')
            ->get();

        return view('admin.comments', compact('title', 'comments'));
    }

    public function destroy($id)
    {
        DB::table('tbl_comments')->where('id', $id)->delete();
        return redirect()->route('admin.comments')->with('success', 'Đã xóa bình luận thành công!');
    }
    public function deleteByBlog($blogId)
    {
        DB::table('tbl_comments')->where('blog_id', $blogId)->delete();
        return redirect()->route('admin.comments')->with('success', 'Đã xóa tất cả bình luận trong bài viết!');
    }
}
