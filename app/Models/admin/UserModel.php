<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_users';

    public function getAllUsers()
    {
        return DB::table($this->table)->get();
    }

    public function updateActive($id)
    {
        return DB::table($this->table)
            ->where('userId', $id) 
            ->update(['isActive' => 'y']); 
    }

    public function changeStatus($id, $data){
        return DB::table($this->table)
            ->where('userId', $id) 
            ->update($data); 
    }
    /**
     * Đếm tổng số người dùng đã đăng ký
     */
    public function getTotalUsers()
    {
        return DB::table($this->table)->count();
    }

    /**
     * Đếm số người dùng đã kích hoạt
     */
    public function getTotalActiveUsers()
    {
        return DB::table($this->table)
            ->where('isActive', 'y')
            ->count();
    }

    /**
     * Đếm số người dùng đăng ký theo tháng hiện tại
     */
    public function getUsersThisMonth()
    {
        return DB::table($this->table)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();
    }

    /**
     * Đếm số người dùng mới trong N ngày gần đây
     */
    public function getNewUsers($days = 7)
    {
        return DB::table($this->table)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

}
