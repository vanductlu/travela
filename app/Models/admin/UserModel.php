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
    public function getUserById($id)
    {
        return DB::table($this->table)
            ->where('userId', $id)
            ->first();
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
    public function blockUser($userId)
    {
        return DB::table($this->table)
            ->where('userId', $userId)
            ->update([
                'status' => 'b',
                'updatedDate' => now()
            ]);
    }
    public function unblockUser($userId)
    {
        return DB::table($this->table)
            ->where('userId', $userId)
            ->update([
                'status' => null,
                'updatedDate' => now()
            ]);
    }
    public function deleteUserCascade($userId)
    {
        DB::beginTransaction();

        try {
            $user = $this->getUserById($userId);
            
            if (!$user) {
                throw new \Exception('User không tồn tại!');
            }

            $bookingIds = DB::table('tbl_booking')
                ->where('userId', $userId)
                ->pluck('bookingId')
                ->toArray();

            $checkoutDeleted = 0;
            if (!empty($bookingIds)) {
                $checkoutDeleted = DB::table('tbl_checkout')
                    ->whereIn('bookingId', $bookingIds)
                    ->delete();
            }

            $bookingDeleted = DB::table('tbl_booking')
                ->where('userId', $userId)
                ->delete();

            $reviewsDeleted = DB::table('tbl_reviews')
                ->where('userId', $userId)
                ->delete();
            $chatDeleted = DB::table('tbl_chat')
                ->where('userId', $userId)
                ->delete();
            $commentsDeleted = 0;
            if ($user->fullName) {
                DB::table('tbl_comments')
                    ->where('name', $user->fullName)
                    ->whereNotNull('parent_id')
                    ->delete();
                $commentsDeleted = DB::table('tbl_comments')
                    ->where('name', $user->fullName)
                    ->delete();
            }

            $deleted = DB::table($this->table)
                ->where('userId', $userId)
                ->delete();

            if (!$deleted) {
                throw new \Exception('Không thể xóa user');
            }

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'checkouts_deleted' => $checkoutDeleted,
                    'bookings_deleted' => $bookingDeleted,
                    'reviews_deleted' => $reviewsDeleted,
                    'comments_deleted' => $commentsDeleted,
                    'chat_deleted' => $chatDeleted,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    public function getTotalUsers()
    {
        return DB::table($this->table)->count();
    }

    public function getTotalActiveUsers()
    {
        return DB::table($this->table)
            ->where('isActive', 'y')
            ->count();
    }

    public function getUsersThisMonth()
    {
        return DB::table($this->table)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();
    }

    public function getNewUsers($days = 7)
    {
        return DB::table($this->table)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

}
