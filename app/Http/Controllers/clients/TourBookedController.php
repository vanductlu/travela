<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Booking;
use App\Models\clients\Tours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\clients\Coupon;
use Illuminate\Support\Facades\DB;

class TourBookedController extends Controller
{
    private $tour;
    private $booking;
    private $coupon;
    
    public function __construct()
    {
        parent::__construct();
        $this->tour = new Tours();
        $this->booking = new Booking();
        $this->coupon = new Coupon();
    }
    
    public function index(Request $req)
    {
        $title = "Tour đã đặt";

        $bookingId = $req->input('bookingId');
        $checkoutId = $req->input('checkoutId');
        $tour_booked = $this->tour->tourBooked($bookingId, $checkoutId);
        $hide = '';
        
        if ($tour_booked && $tour_booked->startDate) {
            $today = Carbon::now();
            $startDate = Carbon::parse($tour_booked->startDate);
            $diffInDays = $startDate->diffInDays($today);
            $hide = $diffInDays < 7 ? 'hide' : '';
        } else {
            $hide = '';
        }

        return view("clients.tour-booked", compact('title', 'tour_booked', 'hide', 'bookingId'));
    }

    public function cancelBooking(Request $req)
    {
        DB::beginTransaction();
        
        try {
            $tourId = $req->tourId;
            $bookingId = $req->bookingId;
            $returnQuantity = $req->quantity__adults + $req->quantity__children;
            $booking = $this->booking->getBookingById($bookingId);
            
            if (!$booking) {
                throw new \Exception('Không tìm thấy booking!');
            }

            if ($booking->bookingStatus === 'c') {
                throw new \Exception('Booking đã được hủy trước đó!');
            }
            $tour = $this->tour->getTourDetail($tourId);
            
            if (!$tour) {
                throw new \Exception('Không tìm thấy tour!');
            }

            $newQuantity = $tour->quantity + $returnQuantity;
            if (!empty($booking->couponCode) && $booking->discount > 0) {
                $coupon = $this->coupon->getCouponByCode($booking->couponCode);
                
                if ($coupon && $coupon->used_count > 0) {
                    $this->coupon->decrementUsage($coupon->couponId);
                }
            }
            $updateTour = $this->tour->updateTours($tourId, ['quantity' => $newQuantity]);
            
            if (!$updateTour) {
                throw new \Exception('Không thể cập nhật số lượng tour!');
            }
            $cancelResult = $this->booking->cancelBooking($bookingId);
            
            if (!$cancelResult) {
                throw new \Exception('Không thể hủy booking!');
            }

            DB::commit();
            toastr()->success('Hủy tour thành công!', 'Thông báo');

        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra: ' . $e->getMessage(), 'Lỗi');
        }

        return redirect()->route('home');
    }

    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'coupon_code' => 'required|string',
                'order_total' => 'required|numeric|min:0'
            ]);

            $couponCode = strtoupper(trim($request->coupon_code));
            $orderTotal = $request->order_total;

            $coupon = $this->coupon->getCouponByCode($couponCode);

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không tồn tại!'
                ]);
            }

            if (!$this->coupon->isValidCoupon($coupon)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->coupon->getErrorMessage($coupon)
                ]);
            }

            if ($orderTotal < $coupon->min_order_value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng phải đạt tối thiểu ' . number_format($coupon->min_order_value) . 'đ để áp dụng mã này!'
                ]);
            }

            $discount = $this->coupon->calculateDiscount($coupon, $orderTotal);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'data' => [
                    'coupon_code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                    'discount_amount' => $discount,
                    'new_total' => max(0, $orderTotal - $discount),
                    'description' => $coupon->description ?? '',
                    'save_text' => 'Bạn tiết kiệm được ' . number_format($discount) . ' VNĐ',
                    'max_discount' => $coupon->max_discount ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}