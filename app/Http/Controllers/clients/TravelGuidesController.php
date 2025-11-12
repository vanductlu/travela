<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Team;
class TravelGuidesController extends Controller
{
    public function index()
    {
        $title = 'Hướng dẫn viên';
        $guides = Team::all(); // lấy tất cả hướng dẫn viên
        return view('clients.travel-guides', compact('title','guides'));
    }

    // Lưu hướng dẫn viên mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
        ]);

        Team::create($data);

        return redirect()->back()->with('success','Hướng dẫn viên đã được thêm!');
    }
}
