<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class CouponModel extends Model
{
     use HasFactory;

    protected $table = "tbl_coupons";
    protected $primaryKey = "couponId";
    
    protected $fillable = [
        "code", "discount_type", "discount_value", 
        "min_order_value", "max_discount", 
        "usage_limit", "used_count",
        "start_date", "end_date",
        "status", "description"
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function isValid($orderTotal = 0)
    {
        $errors = [];

        if ($this->status !== 'active') {
            $errors[] = 'Mã giảm giá đã bị vô hiệu hóa';
        }

        $now = Carbon::now();
        if ($now->lt($this->start_date)) {
            $errors[] = 'Mã giảm giá chưa có hiệu lực';
        }
        if ($now->gt($this->end_date)) {
            $errors[] = 'Mã giảm giá đã hết hạn';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            $errors[] = 'Mã giảm giá đã hết lượt sử dụng';
        }

        if ($this->min_order_value && $orderTotal < $this->min_order_value) {
            $errors[] = 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($this->min_order_value) . ' VNĐ';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function calculateDiscount($orderTotal)
    {
        $discount = 0;

        if ($this->discount_type === 'percent') {
            $discount = ($orderTotal * $this->discount_value) / 100;

            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->discount_value;
        }

        return min($discount, $orderTotal);
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function getDiscountText()
    {
        if ($this->discount_type === 'percent') {
            $text = 'Giảm ' . $this->discount_value . '%';
            if ($this->max_discount) {
                $text .= ' (tối đa ' . number_format($this->max_discount) . ' VNĐ)';
            }
        } else {
            $text = 'Giảm ' . number_format($this->discount_value) . ' VNĐ';
        }
        return $text;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }
}
