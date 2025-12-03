<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Booking extends Model
{
    use HasFactory;

    protected $table = 'tbl_booking';
    protected $primaryKey = 'bookingId';

    public function createBooking($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function cancelBooking($bookingId){
        return DB::table($this->table)
        ->where('bookingId', $bookingId)
        ->update(['bookingStatus' => 'c']);
    }
    public function getBookingById($bookingId)
    {
        return DB::table($this->table)
            ->where('bookingId', $bookingId)
            ->first();
    }
    public function checkBooking($tourId, $userId)
    {
        return DB::table($this->table)
        ->where('tourId', $tourId)
        ->where('userId', $userId)
        ->where('bookingStatus', 'f')
        ->exists(); 
    }
    public function getBookingWithCoupon($bookingId)
    {
        return DB::table($this->table)
            ->where('bookingId', $bookingId)
            ->select('*')
            ->first();
    }

    public function getBookingWithTourDetails($bookingId, $checkoutId = null)
    {
        $query = DB::table($this->table . ' as b')
            ->join('tbl_tours as t', 'b.tourId', '=', 't.tourId')
            ->leftJoin('tbl_checkout as c', 'b.bookingId', '=', 'c.bookingId')
            ->select(
                'b.*',
                't.title',
                't.priceAdult',
                't.priceChild',
                't.time',
                't.description',
                't.destination',
                't.startDate',
                't.endDate',
                't.quantity as tourQuantity',
                'c.checkoutId',
                'c.paymentMethod',
                'c.paymentStatus',
                'c.amount as checkoutAmount'
            )
            ->where('b.bookingId', $bookingId);
            
        if ($checkoutId) {
            $query->where('c.checkoutId', $checkoutId);
        }
        
        return $query->first();
    }
}
