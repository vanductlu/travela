<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'tbl_coupons';
    protected $primaryKey = 'couponId';
    public $timestamps = true;

    public function getCouponByCode($code)
    {
        return DB::table($this->table)
            ->where('code', strtoupper($code))
            ->first();
    }

    public function isValidCoupon($coupon)
    {
        if (!$coupon || $coupon->status !== 'active') {
            return false;
        }

        $now = Carbon::now();

        if ($coupon->start_date && Carbon::parse($coupon->start_date)->greaterThan($now)) {
            return false;
        }

        if ($coupon->end_date && Carbon::parse($coupon->end_date)->lessThan($now)) {
            return false;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($coupon, $orderTotal)
    {
        if ($orderTotal < $coupon->min_order_value) {
            return 0;
        }

        $discount = 0;

        if ($coupon->discount_type === 'percent') {
            $discount = ($orderTotal * $coupon->discount_value) / 100;
            
            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                $discount = $coupon->max_discount;
            }
        } else {
            $discount = $coupon->discount_value;
        }

        return min($discount, $orderTotal);
    }

    public function incrementUsage($couponId)
    {
        return DB::table($this->table)
            ->where('couponId', $couponId)
            ->increment('used_count');
    }

    public function decrementUsage($couponId)
    {
        return DB::table($this->table)
            ->where('couponId', $couponId)
            ->where('used_count', '>', 0)
            ->decrement('used_count');
    }

    public function getErrorMessage($coupon)
    {
        if (!$coupon) {
            return 'Mã giảm giá không tồn tại!';
        }

        if ($coupon->status !== 'active') {
            return 'Mã giảm giá đã bị vô hiệu hóa!';
        }

        $now = Carbon::now();

        if ($coupon->start_date && Carbon::parse($coupon->start_date)->greaterThan($now)) {
            return 'Mã giảm giá chưa có hiệu lực! Hiệu lực từ ' . Carbon::parse($coupon->start_date)->format('d/m/Y H:i');
        }

        if ($coupon->end_date && Carbon::parse($coupon->end_date)->lessThan($now)) {
            return 'Mã giảm giá đã hết hạn vào ' . Carbon::parse($coupon->end_date)->format('d/m/Y H:i');
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return 'Mã giảm giá đã hết lượt sử dụng!';
        }

        return 'Mã giảm giá không hợp lệ!';
    }

    public function getActiveCoupons()
    {
        $now = Carbon::now();
        
        return DB::table($this->table)
            ->where('status', 'active')
            ->where(function($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->get();
    }
}