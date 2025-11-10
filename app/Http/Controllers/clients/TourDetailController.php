<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
class TourDetailController extends Controller
{   
    private $tours;
    public function __construct()
    {
        $this->tours = new Tours();
    }
    public function index($id=0)
    {   
        $title = 'Chi tiết tours';
        $userId = $this->getUserId();

        $tourDetail = $this->tours->getTourDetail($id);
        $getReviews = $this->tours->getReviews($id);
        $reviewStats = $this->tours->reviewStats($id);

        $avgStar = round($reviewStats->averageRating);
        $countReview = $reviewStats->reviewCount;

        $checkReviewExist = $this->tours->checkReviewExist($id, $userId);
        if (!$checkReviewExist) {
            $checkDisplay = '';
        } else {
            $checkDisplay = 'hide';
        }

        
        $relatedTours = DB::table('tbl_tours')
        ->where('tourId', '!=', $id)
        ->where('availability', 1)
        ->limit(3)
        ->pluck('tourId')
        ->toArray();
        $tourRecommendations = $this->tours->toursRecommendation($relatedTours);
        // dd($tourRecommendations);    
        // dd($avgStar);

         return view('clients.tour-detail', compact('title', 'tourDetail', 'getReviews', 'avgStar', 'countReview', 'checkDisplay','tourRecommendations'));
    }
    public function reviews(Request $req)
    {
        // dd($req);
        $userId = $this->getUserId();
        $tourId = $req->tourId;
        $message = $req->message;
        $star = $req->rating;

        $dataReview = [
            'tourId' => $tourId,
            'userId' => $userId,
            'comment' => $message,
            'rating' => $star
        ];

        $rating = $this->tours->createReviews($dataReview);
        if (!$rating) {
            return response()->json([
                'error' => true
            ], 500);
        }
        $tourDetail = $this->tours->getTourDetail($tourId);
        $getReviews = $this->tours->getReviews($tourId);
        $reviewStats = $this->tours->reviewStats($tourId);

        $avgStar = round($reviewStats->averageRating);
        $countReview = $reviewStats->reviewCount;

        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được gửi thành công!',
            'data' => view('clients.partials.reviews', compact('tourDetail', 'getReviews', 'avgStar', 'countReview'))->render()
        ], 200);
    }
}
