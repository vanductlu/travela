<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\CouponModel;
use Illuminate\Support\Facades\Validator;
class CouponManagementController extends Controller
{
    public function index()
    {   
        $title = 'Mã giảm giá';
        $coupons = CouponModel::orderBy("couponId", "desc")->get();
        return view("admin.coupon.index", compact("coupons", "title"));
    }

    public function create()
    {
        return view("admin.coupon.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "code" => "required|unique:tbl_coupons,code",
            "discount_type" => "required",
            "discount_value" => "required|numeric",
            "start_date" => "required",
            "end_date" => "required",
        ]);

        CouponModel::create($request->all());

        return redirect()->route("admin.coupon.index")->with("success", "Thêm coupon thành công");
    }

    public function edit($id)
    {
        $coupon = CouponModel::findOrFail($id);
        return view("admin.coupon.edit", compact("coupon"));
    }

    public function update(Request $request, $id)
    {
        $coupon = CouponModel::findOrFail($id);

        $request->validate([
            "code" => "required|unique:tbl_coupons,code," . $id . ",couponId",
        ]);

        $coupon->update($request->all());

        return redirect()->route("admin.coupon.index")->with("success", "Cập nhật coupon thành công");
    }

    public function destroy($id)
    {
        $coupon = CouponModel::findOrFail($id);
        $coupon->delete();

        return response()->json(['success' => true]);
    }
}
