<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Tours;
use App\Models\clients\Booking;
use App\Models\clients\Checkout;

class BookingController extends Controller
{
    private $tour;
    private $booking;
    private $checkout;
    public function __construct()
    {   
        parent::__construct();
        $this->tour = new Tours();
        $this->booking = new Booking();
        $this->checkout = new Checkout();

    }
    public function index($id)
    {   
        $title = 'Đặt Tour';
        $tour = $this->tour->getTourDetail($id);
        return view('clients.booking',compact('title','tour'));
    }
    public function createBooking(Request $req)
    {   
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
        /**
         * Xử lý booking và checkout
         */
        $dataBooking = [
            'tourId' => $tourId,
            'userId' => $userId,
            'address' => $address,
            'fullName' => $fullName,
            'email' => $email,
            'numAdults' => $numAdults,
            'numChildren' => $numChildren,
            'phoneNumber' => $tel,
            'totalPrice' => $totalPrice
        ];
        $bookingId = $this->booking->createBooking($dataBooking);

        $dataCheckout = [
            'bookingId' => $bookingId,
            'paymentMethod' => $paymentMethod,
            'amount' => $totalPrice,
            'paymentStatus' => 'n',
        ];
        $checkout = $this->checkout->createCheckout($dataCheckout);
        
        if (empty($bookingId) && !$checkout) {
            toastr()->error('Đặt tour thất bại! Vui lòng thử lại.');
            return redirect()->back(); // Quay lại trang hiện tại nếu có lỗi
        }
         /**
            * Update quantity mới cho tour đó, trừ số lượng
            */
        $tour = $this->tour->getTourDetail($tourId);
        $dataUpdate = [
            'quantity' => $tour->quantity - ($numAdults + $numChildren)
        ];
        $updateQuantity = $this->tour->updateTours($tourId,$dataUpdate);
        
        /*********************************** */
        toastr()->success('Đặt tour thành công!');
        return redirect()->route('tours');
    }
}
