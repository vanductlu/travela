<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Tours;
use App\Models\clients\Booking;
use App\Models\clients\Checkout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\clients\Coupon;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    private $tour;
    private $booking;
    private $checkout;
    private $coupon;
    
    public function __construct()
    {   
        parent::__construct();
        $this->tour = new Tours();
        $this->booking = new Booking();
        $this->checkout = new Checkout();
        $this->coupon = new Coupon();
    }
    
    public function index($id)
    {   
        $title = 'Đặt Tour';
        $tour = $this->tour->getTourDetail($id);
        return view('clients.booking', compact('title', 'tour'));
    }

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'order_total' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
        }

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
                'description' => $coupon->description ?? null,
                'save_text' => 'Bạn tiết kiệm được ' . number_format($discount) . ' VNĐ',
                'max_discount' => $coupon->max_discount ?? null
            ]
        ]);
    }

    public function createBooking(Request $req)
    {   
        DB::beginTransaction();
        
        try {
            $address = $req->input('address');
            $email = $req->input('email');
            $fullName = $req->input('fullName');
            $numAdults = $req->input('numAdults');
            $numChildren = $req->input('numChildren');
            $paymentMethod = $req->input('payment_hidden');
            $tel = $req->input('tel');
            $totalPrice = $req->input('totalPrice');
            $tourId = $req->input('tourId');
            $userId = $this->getUserId();

            $couponCode = $req->input('couponCode');
            $discount = 0;
            $originalPrice = $req->input('originalPrice') ?? $totalPrice;

            // Xử lý coupon nếu có
            if (!empty($couponCode)) {
                $coupon = $this->coupon->getCouponByCode($couponCode);
                
                if ($coupon && $this->coupon->isValidCoupon($coupon)) {
                    $discount = $this->coupon->calculateDiscount($coupon, $originalPrice);
                    $totalPrice = max(0, $originalPrice - $discount);
                    
                    // Tăng số lần sử dụng coupon
                    $this->coupon->incrementUsage($coupon->couponId);
                } else {
                    // Coupon không hợp lệ, đặt lại về null
                    $couponCode = null;
                    $discount = 0;
                }
            }

            // Tạo booking
            $dataBooking = [
                'tourId' => $tourId,
                'userId' => $userId,
                'address' => $address,
                'fullName' => $fullName,
                'email' => $email,
                'numAdults' => $numAdults,
                'numChildren' => $numChildren,
                'phoneNumber' => $tel,
                'originalPrice' => $originalPrice,
                'discount' => $discount,
                'couponCode' => $couponCode,
                'totalPrice' => $totalPrice,
                'bookingStatus' => 'b'
            ];
            
            $bookingId = $this->booking->createBooking($dataBooking);

            if (empty($bookingId)) {
                throw new \Exception('Không thể tạo booking');
            }

            // Tạo checkout
            $dataCheckout = [
                'bookingId' => $bookingId,
                'paymentMethod' => $paymentMethod,
                'amount' => $totalPrice,
                'paymentStatus' => 'n',
            ];
            
            $checkout = $this->checkout->createCheckout($dataCheckout);
            
            if (!$checkout) {
                throw new \Exception('Không thể tạo checkout');
            }
            
            // Cập nhật số lượng tour
            $tour = $this->tour->getTourDetail($tourId);
            $newQuantity = $tour->quantity - ($numAdults + $numChildren);
            
            if ($newQuantity < 0) {
                throw new \Exception('Số lượng tour không đủ');
            }
            
            $dataUpdate = ['quantity' => $newQuantity];
            $updateQuantity = $this->tour->updateTours($tourId, $dataUpdate);
            
            if (!$updateQuantity) {
                throw new \Exception('Không thể cập nhật số lượng tour');
            }
            
            DB::commit();
            
            toastr()->success('Đặt tour thành công!');
            return redirect()->route('tour-booked', [
                'bookingId' => $bookingId,
                'checkoutId' => $checkout
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            toastr()->error('Đặt tour thất bại! ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    public function createMomoPayment(Request $request)
    {
        // Lưu toàn bộ thông tin booking vào session
        session()->put('momo_booking_data', [
            'tourId' => $request->tourId,
            'fullName' => $request->fullName,
            'email' => $request->email,
            'tel' => $request->tel,
            'address' => $request->address,
            'numAdults' => $request->numAdults,
            'numChildren' => $request->numChildren,
            'totalPrice' => $request->totalPrice,
            'originalPrice' => $request->originalPrice,
            'couponCode' => $request->couponCode,
            'payment_hidden' => $request->payment_hidden
        ]);
        
        try {
            $amount = $request->totalPrice ?? 10000;
    
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = "MOMOBKUN20180529";
            $accessKey = "klm05TvNBzhg7h7j";
            $secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
    
            $orderInfo = "Thanh toán đơn hàng tour";
            $requestId = time();
            $orderId = time();
            $extraData = "";
            $redirectUrl = route('booking.momo.callback');
            $ipnUrl = route('booking.momo.callback');
            $requestType = 'payWithATM';
    
            $rawHash = "accessKey=" . $accessKey . 
                       "&amount=" . $amount . 
                       "&extraData=" . $extraData . 
                       "&ipnUrl=" . $ipnUrl . 
                       "&orderId=" . $orderId . 
                       "&orderInfo=" . $orderInfo . 
                       "&partnerCode=" . $partnerCode . 
                       "&redirectUrl=" . $redirectUrl . 
                       "&requestId=" . $requestId . 
                       "&requestType=" . $requestType;
    
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
    
            $data = [
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                'storeId' => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            ];
    
            $response = Http::post($endpoint, $data);
    
            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['payUrl'])) {
                    return response()->json(['payUrl' => $body['payUrl']]);
                } else {
                    return response()->json(['error' => 'Invalid response from MoMo', 'details' => $body], 400);
                }
            } else {
                return response()->json(['error' => 'Lỗi kết nối với MoMo', 'details' => $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Đã xảy ra lỗi', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function handlePaymentMomoCallback(Request $request)
    {
        $resultCode = $request->input('resultCode');
        $transIdMomo = $request->query('transId');
        
        $bookingData = session()->get('momo_booking_data');
        
        if (!$bookingData) {
            toastr()->error('Phiên làm việc đã hết hạn');
            return redirect()->route('home');
        }
        
        if ($resultCode == '0') {
            // Thanh toán thành công, tạo booking
            $req = new Request($bookingData);
            $req->merge(['transIdMomo' => $transIdMomo]);
            
            session()->forget('momo_booking_data');
            
            return $this->createBooking($req);
        } else {
            // Thanh toán thất bại
            session()->forget('momo_booking_data');
            
            $tourId = $bookingData['tourId'];
            $tour = $this->tour->getTourDetail($tourId);
            $title = 'Thanh toán thất bại';
            
            toastr()->error('Thanh toán MoMo thất bại!');
            return view('clients.booking', compact('title', 'tour'));
        }
    }
    
    public function checkBooking(Request $req)
    {
        $tourId = $req->tourId;
        $userId = $this->getUserId();
        $check = $this->booking->checkBooking($tourId, $userId);
        
        if (!$check) {
            return response()->json(['success' => false]);
        }
        
        return response()->json(['success' => true]);
    }
}