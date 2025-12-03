<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Team;
class TravelGuidesManagement extends Controller
{
    public function index()
    {
        $teams = Team::all();
        $title = 'Quản lý Hướng dẫn viên';
        return view('admin.travelguides', compact('teams','title'));
    }
    public function create()
    {
        return view('admin.travelguides');
    }
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
        return redirect()->route('admin.travelguides')->with('success', 'Hướng dẫn viên đã được thêm!');
    }
    public function edit(Team $team)
    {
        return view('admin.travelguides', compact('team'));
    }
    public function update(Request $request, Team $team)
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

        $team->update($data);
        return redirect()->route('admin.travelguides')->with('success', 'Hướng dẫn viên đã được cập nhật!');
    }
    public function delete(Team $team)
    {
        $team->delete();
        return redirect()->route('admin.travelguides')->with('success', 'Hướng dẫn viên đã bị xóa!');
    }
    public function activate(Team $team)
    {
        $team->update(['status' => 'active']);
        return redirect()->route('admin.travelguides')->with('success', 'Hướng dẫn viên đã được kích hoạt!');
    }
}
