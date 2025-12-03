<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Tours;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class MyTourController extends Controller
{
    private $tours;

    public function __construct()
    {
        parent::__construct();
        $this->tours = new Tours();
    }

    public function index()
    {
        $title = 'Tours đã đặt';
        $userId = $this->getUserId();
        
        $myTours = $this->user->getMyTours($userId);
        $userId = $this->getUserId();
        if ($userId) {
            try {
                $apiUrl = 'http://127.0.0.1:5555/api/user-recommendations';
                $response = Http::get($apiUrl, [
                    'user_id' => $userId
                ]);

                if ($response->successful()) {
                    $tourIds = $response->json('recommended_tours');
                    $tourIds = array_slice($tourIds, 0, 2);
                } else {
                    $tourIds = [];
                }
            } catch (\Exception $e) {
                $tourIds = [];
                Log::error('Lỗi khi gọi API liên quan: ' . $e->getMessage());
            }
            $toursPopular = $this->tours->toursRecommendation($tourIds);
            // dd($toursPopular);
        }else {
            $toursPopular = $this->tours->toursPopular(6);
        }

        return view('clients.my-tours', compact('title', 'myTours','toursPopular'));
    }
}
    
