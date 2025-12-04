<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class UserManagementController extends Controller
{

    private $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }
    public function index()
    {
        $title = 'Quản lý người dùng';
        $users = $this->users->getAllUsers();

        foreach ($users as $user) {
            if (!$user->fullName) {
                $user->fullName = "Unnamed";
            }
            if (!$user->avatar) {
                $user->avatar = 'unnamed.png';
            }
            $user->statusText = $this->getStatusText($user->status);
            $user->isActiveText = ($user->isActive == 'y') ? 'Đã kích hoạt' : 'Chưa kích hoạt';
        }
        return view('admin.users', compact('title', 'users'));
    }

    public function activeUser(Request $request)
    {
        $userId = $request->userId;
        $updateActive = $this->users->updateActive($userId);

        if ($updateActive) {
            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được kích hoạt thành công!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kích hoạt người dùng!'
            ], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        $userId = $request->userId;
        $status = $request->status;

        $dataUpdate = [
            'userId' => $userId,
            'status' => $status
        ];

        $changeStatus = $this->users->changeStatus($userId, $dataUpdate);
        $statusText = $this->getStatusText($status);
        if ($changeStatus) {
            return response()->json([
                'success' => true,
                'status' => $statusText,
                'message' => "Trạng thái người dùng đã được cập nhật thành công!"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Có lỗi xảy ra khi cập nhật trạng thái người dùng!"
            ], 500);
        }
    }
    public function blockUser(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer'
        ]);

        $userId = $request->userId;
        $user = $this->users->getUserById($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại!'
            ], 404);
        }
        if ($user->status === 'b') {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng này đã bị chặn rồi!'
            ], 400);
        }

        $blocked = $this->users->blockUser($userId);

        if ($blocked) {
            Log::info("User blocked", [
                'userId' => $userId,
                'username' => $user->username
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chặn người dùng thành công!',
                'status' => 'Đã chặn'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chặn người dùng!'
            ], 500);
        }
    }
    public function unblockUser(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer'
        ]);

        $userId = $request->userId;
        $user = $this->users->getUserById($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại!'
            ], 404);
        }

        if ($user->status !== 'b') {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng này không bị chặn!'
            ], 400);
        }

        $unblocked = $this->users->unblockUser($userId);

        if ($unblocked) {
            Log::info("User unblocked", [
                'userId' => $userId,
                'username' => $user->username
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục người dùng thành công!',
                'status' => 'Hoạt động'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khôi phục người dùng!'
            ], 500);
        }
    }
    public function deleteUser($id)
{
    $user = $this->users->getUserById($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Người dùng không tồn tại!'
        ], 404);
    }
    $result = $this->users->deleteUserCascade($id);

    if ($result['success']) {

        Log::info("User deleted successfully", [
            'userId' => $id,
            'username' => $user->username,
            'deleted_data' => $result['data']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng và dữ liệu liên quan thành công!',
            'data' => $result['data']
        ]);
    }

    Log::error("Failed to delete user", [
        'userId' => $id,
        'error' => $result['error']
    ]);

    return response()->json([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $result['error']
    ], 500);
}

    private function getStatusText($status)
    {
        switch ($status) {
            case null:
                return 'Hoạt động';
            case 'b':
                return 'Đã chặn';
            case 'd':
                return 'Đã xóa';
            default:
                return 'Không xác định';
        }
    }

}
