<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\CouponModel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class CouponManagementController extends Controller
{
     public function index()
    {   
        $title = 'Quản lý mã giảm giá';
        $coupons = CouponModel::orderBy("couponId", "desc")->get();
        
        // Thống kê - FIX: Đảm bảo tính toán đúng
        $now = Carbon::now();
        $stats = [
            'total' => $coupons->count(),
            'active' => $coupons->where('status', 'active')
                              ->filter(function($c) use ($now) {
                                  return Carbon::parse($c->end_date)->gte($now);
                              })
                              ->count(),
            'expired' => $coupons->filter(function($c) use ($now) {
                return Carbon::parse($c->end_date)->lt($now);
            })->count(),
            'used' => $coupons->sum('used_count')
        ];
        
        return view("admin.coupon.index", compact("coupons", "title", "stats"));
    }

    public function create()
    {
        $title = 'Tạo mã giảm giá mới';
        return view("admin.coupon.create", compact("title"));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "code" => "required|string|max:50|unique:tbl_coupons,code",
            "discount_type" => "required|in:percent,fixed",
            "discount_value" => "required|numeric|min:0",
            "min_order_value" => "nullable|numeric|min:0",
            "max_discount" => "nullable|numeric|min:0",
            "usage_limit" => "nullable|integer|min:1",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
            "status" => "required|in:active,inactive",
            "description" => "nullable|string"
        ], [
            "code.required" => "Mã giảm giá không được để trống",
            "code.unique" => "Mã giảm giá đã tồn tại",
            "discount_type.required" => "Loại giảm giá không được để trống",
            "discount_value.required" => "Giá trị giảm không được để trống",
            "discount_value.min" => "Giá trị giảm phải lớn hơn 0",
            "start_date.required" => "Ngày bắt đầu không được để trống",
            "end_date.required" => "Ngày kết thúc không được để trống",
            "end_date.after_or_equal" => "Ngày kết thúc phải sau hoặc bằng ngày bắt đầu"
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate thêm cho percent
        if ($request->discount_type === 'percent' && $request->discount_value > 100) {
            return redirect()->back()
                ->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        CouponModel::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->max_discount,
            'usage_limit' => $request->usage_limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'description' => $request->description,
            'used_count' => 0
        ]);

        return redirect()->route("admin.coupon.index")
            ->with("success", "Thêm mã giảm giá thành công");
    }

    public function edit($id)
    {
        $title = 'Chỉnh sửa mã giảm giá';
        $coupon = CouponModel::findOrFail($id);
        return view("admin.coupon.edit", compact("coupon", "title"));
    }

    public function update(Request $request, $id)
    {
        $coupon = CouponModel::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            "code" => "required|string|max:50|unique:tbl_coupons,code," . $id . ",couponId",
            "discount_type" => "required|in:percent,fixed",
            "discount_value" => "required|numeric|min:0",
            "min_order_value" => "nullable|numeric|min:0",
            "max_discount" => "nullable|numeric|min:0",
            "usage_limit" => "nullable|integer|min:1",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
            "status" => "required|in:active,inactive",
            "description" => "nullable|string"
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->discount_type === 'percent' && $request->discount_value > 100) {
            return redirect()->back()
                ->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->max_discount,
            'usage_limit' => $request->usage_limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'description' => $request->description
        ]);

        return redirect()->route("admin.coupon.index")
            ->with("success", "Cập nhật mã giảm giá thành công");
    }

    public function destroy($id)
    {
        try {
            $coupon = CouponModel::findOrFail($id);
            
            // Kiểm tra xem coupon đã được sử dụng chưa
            if ($coupon->used_count > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Không thể xóa mã giảm giá đã được sử dụng'
                ], 400);
            }
            
            $coupon->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa mã giảm giá thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Toggle status
    public function toggleStatus($id)
    {
        try {
            $coupon = CouponModel::findOrFail($id);
            $coupon->status = $coupon->status === 'active' ? 'inactive' : 'active';
            $coupon->save();

            return response()->json([
                'success' => true,
                'status' => $coupon->status,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    // View chi tiết
    public function show($id)
    {
        $title = 'Chi tiết mã giảm giá';
        $coupon = CouponModel::findOrFail($id);
        
        // Lấy danh sách booking đã sử dụng coupon này (nếu có quan hệ)
        // $bookings = $coupon->bookings()->latest()->paginate(10);
        
        return view("admin.coupon.show", compact("coupon", "title"));
    }
}
