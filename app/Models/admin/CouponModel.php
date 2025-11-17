<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponModel extends Model
{
    protected $table = "tbl_coupons";
    protected $primaryKey = "couponId";
    protected $fillable = [
        "code", "discount_type", "discount_value", 
        "min_order_value", "max_discount", 
        "usage_limit", "used_count",
        "start_date", "end_date",
        "status", "description"
    ];
}
