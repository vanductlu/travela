<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Tours extends Model
{
    use HasFactory;

    protected $table = 'tbl_tours';
    public function getAllTours($perPage = 9)
    {
        $allTours = DB::table($this->table)->where('availability', 1)->paginate($perPage);
        foreach($allTours as $tour){
            $tour ->images = DB::table('tbl_images')
                            ->where('tourId', $tour->tourId)
                            ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }

        return $allTours;
    }
    public function getTourDetail($id)
    {
        $getTourDetail = DB::table($this->table)
            ->where('tourId', $id)
            ->first();

        if ($getTourDetail) {
            $getTourDetail->images = DB::table('tbl_images')
                ->where('tourId', $getTourDetail->tourId)
                ->limit(5)
                ->pluck('imageUrl');

            $getTourDetail->timeline = DB::table('tbl_timeline')
                ->where('tourId', $getTourDetail->tourId)
                ->get();
        }
        return $getTourDetail;
    }

    function getDomain()
    {
        return DB::table($this->table)
            ->select('domain', DB::raw('COUNT(*) as count'))
            ->whereIn('domain', ['b', 't', 'n'])
            ->groupBy('domain')
            ->get();
    }

    public function filterTours($filters = [], $sorting = null, $perPage = null)
    {
        DB::enableQueryLog();

        $getTours = DB::table($this->table)
            ->leftJoin('tbl_reviews', 'tbl_tours.tourId', '=', 'tbl_reviews.tourId')
            ->select(
                'tbl_tours.tourId',
                'tbl_tours.title',
                'tbl_tours.description',
                'tbl_tours.priceAdult',
                'tbl_tours.priceChild',
                'tbl_tours.time',
                'tbl_tours.destination',
                'tbl_tours.quantity',
                DB::raw('AVG(tbl_reviews.rating) as averageRating')
            )
            ->groupBy(
                'tbl_tours.tourId',
                'tbl_tours.title',
                'tbl_tours.description',
                'tbl_tours.priceAdult',
                'tbl_tours.priceChild',
                'tbl_tours.time',
                'tbl_tours.destination',
                'tbl_tours.quantity'
            );
            $getTours = $getTours->where('availability', 1);

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if ($filter[0] !== 'averageRating') {
                    $getTours = $getTours->where($filter[0], $filter[1], $filter[2]);
                }
            }
        }

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if ($filter[0] === 'averageRating') {
                    $getTours = $getTours->having('averageRating', $filter[1], $filter[2]); 
                }
            }
        }

        if (!empty($sorting) && isset($sorting[0]) && isset($sorting[1])) {
            $getTours = $getTours->orderBy($sorting[0], $sorting[1]);
        }

        $tours = $getTours->get();

        $queryLog = DB::getQueryLog();

        foreach ($tours as $tour) {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }
        return $tours;
    }
    public function updateTours($tourId,$data)
    {
        $update = DB::table($this->table)
            ->where('tourId', $tourId)
            ->update($data);
        return $update;
    }
    public function tourBooked($bookingId, $checkoutId = null)
    {
        $query = DB::table('tbl_booking as b')
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
            'c.amount'
        )
        ->where('b.bookingId', $bookingId);
        
    if ($checkoutId) {
        $query->where('c.checkoutId', $checkoutId);
    }
    
    return $query->first();
    }
    public function createReviews($data)
    {
        return DB::table('tbl_reviews')->insert($data);
    }

    public function getReviews($id)
    {
        $getReviews = DB::table('tbl_reviews')
            ->join('tbl_users', 'tbl_users.userId', '=', 'tbl_reviews.userId')
            ->where('tourId', $id)
            ->orderBy('tbl_reviews.timestamp', 'desc')
            ->take(3)
            ->get();

        return $getReviews;
    }

    public function reviewStats($id)
    {
        $reviewStats = DB::table('tbl_reviews')
            ->where('tourId', $id)
            ->selectRaw('AVG(rating) as averageRating, COUNT(*) as reviewCount')
            ->first();

        return $reviewStats;
    }
    public function searchTours($data)
    {
        $tours = DB::table($this->table);


        if (!empty($data['destination'])) {
            $tours->where('destination', 'LIKE', '%' . $data['destination'] . '%');
        }

        if (!empty($data['startDate'])) {
            $tours->whereDate('startDate', '>=', $data['startDate']);
        }
        if (!empty($data['endDate'])) {
            $tours->whereDate('endDate', '<=', $data['endDate']);
        }

        if (!empty($data['keyword'])) {
            $tours->where(function ($query) use ($data) {
                $query->where('title', 'LIKE', '%' . $data['keyword'] . '%')
                    ->orWhere('description', 'LIKE', '%' . $data['keyword'] . '%')
                    ->orWhere('time', 'LIKE', '%' . $data['keyword'] . '%')
                    ->orWhere('destination', 'LIKE', '%' . $data['keyword'] . '%');
            });
        }

        $tours = $tours->where('availability', 1);
        $tours = $tours->limit(12)->get();

        foreach ($tours as $tour) {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }
        return $tours;
    }

    public function checkReviewExist($tourId, $userId)
    {
        return DB::table('tbl_reviews')
            ->where('tourId', $tourId)
            ->where('userId', $userId)
            ->exists();
    }
    public function toursPopular($quantity)
    {
        $toursPopular = DB::table('tbl_booking')
            ->select(
                'tbl_tours.tourId',
                'tbl_tours.title',
                'tbl_tours.description',
                'tbl_tours.priceAdult',
                'tbl_tours.priceChild',
                'tbl_tours.time',
                'tbl_tours.destination',
                'tbl_tours.quantity',
                DB::raw('COUNT(tbl_booking.tourId) as totalBookings')
            )
            ->join('tbl_tours', 'tbl_booking.tourId', '=', 'tbl_tours.tourId')
            ->where('tbl_booking.bookingStatus', 'f') 
            ->groupBy(
                'tbl_tours.tourId',
                'tbl_tours.title',
                'tbl_tours.description',
                'tbl_tours.priceAdult',
                'tbl_tours.priceChild',
                'tbl_tours.time',
                'tbl_tours.destination',
                'tbl_tours.quantity'
            )
            ->orderBy('totalBookings', 'DESC')
            ->take($quantity)
            ->get();


        foreach ($toursPopular as $tour) {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }
        return $toursPopular;
    }
    public function toursRecommendation($ids)
    {

        if (empty($ids)) {
            return collect();
        }

        $toursRecom = DB::table($this->table)
            ->where('availability', '1')
            ->whereIn('tourId', $ids)
            ->orderByRaw("FIELD(tourId, " . implode(',', array_map('intval', $ids)) . ")") 
            ->get();
        foreach ($toursRecom as $tour) {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }

        return $toursRecom;
    }
    public function toursSearch($ids)
    {

        if (empty($ids)) {
            return collect();
        }

        $tourSearch = DB::table($this->table)
            ->where('availability', '1')
            ->whereIn('tourId', $ids)
            ->orderByRaw("FIELD(tourId, " . implode(',', array_map('intval', $ids)) . ")") 
            ->get();
        foreach ($tourSearch as $tour) {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
            $tour->rating = $this->reviewStats($tour->tourId)->averageRating;
        }

        return $tourSearch;
    }
}
