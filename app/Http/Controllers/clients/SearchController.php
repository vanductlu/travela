<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class SearchController extends Controller
{
    private $tours;

    public function __construct()
    {
        $this->tours = new Tours();
    }

    public function getSuggestions(Request $request)
    {
        $keyword = strtolower(trim($request->query('keyword', '')));

        if (empty($keyword) || strlen($keyword) < 2) {
            return response()->json(['suggestions' => []]);
        }

        try {
            $tours = DB::table('tbl_tours')
                ->select('title', 'destination')
                ->where('availability', 1)
                ->where(function($query) use ($keyword) {
                    $query->where('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('destination', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%");
                })
                ->limit(8)
                ->get();
            $suggestions = [];
            foreach ($tours as $tour) {
                if (stripos($tour->title, $keyword) !== false) {
                    $suggestions[] = $tour->title;
                }
                if (stripos($tour->destination, $keyword) !== false && 
                    !in_array($tour->destination, $suggestions)) {
                    $suggestions[] = "Tour " . $tour->destination;
                }
            }
            $suggestions = array_values(array_unique($suggestions));
            $suggestions = array_slice($suggestions, 0, 6);

            return response()->json([
                'suggestions' => $suggestions,
                'count' => count($suggestions)
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy gợi ý: ' . $e->getMessage());
            return response()->json(['suggestions' => []]);
        }
    }

    public function searchTours(Request $request)
    {
        $title = 'Tìm kiếm';
        $keyword = $request->input('keyword');

        if (empty($keyword)) {
            return redirect()->route('tours');
        }
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/search-tours';
            $response = Http::timeout(3)->get($apiUrl, [
                'keyword' => $keyword
            ]);

            if ($response->successful()) {
                $resultTours = $response->json('related_tours', []);
                $tourIds = array_column($resultTours, 'tourId');
            } else {
                $tourIds = [];
            }
        } catch (\Exception $e) {
            $tourIds = [];
            Log::error('Lỗi khi gọi API Python: ' . $e->getMessage());
        }

        if (!empty($tourIds)) {
            $tours = $this->tours->toursSearch($tourIds);
        } else {
            $dataSearch = ['keyword' => $keyword];
            $tours = $this->tours->searchTours($dataSearch);
        }

        return view('clients.search', compact('title', 'tours', 'keyword'));
    }
    public function index(Request $request)
    {
        $title = 'Tìm kiếm';
        $destinationMap = [
            'dn' => 'Đà Nẵng',
            'cd' => 'Côn Đảo',
            'hn' => 'Hà Nội',
            'hcm' => 'TP. Hồ Chí Minh',
            'hl' => 'Hạ Long',
            'nb' => 'Ninh Bình',
            'pq' => 'Phú Quốc',
            'dl' => 'Đà Lạt',
            'qt' => 'Quảng Trị',
            'kh' => 'Khánh Hòa',
            'ct' => 'Cần Thơ',
            'vt' => 'Vũng Tàu',
            'qn' => 'Quảng Ninh',
            'la' => 'Lào Cai',
            'bd' => 'Bình Định',
        ];

        $destination = $request->input('destination');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $formattedStartDate = $startDate ? Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d') : null;
        $formattedEndDate = $endDate ? Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d') : null;

        $destinationName = $destinationMap[$destination] ?? $destination;

        $dataSearch = [
            'destination' => $destinationName,
            'startDate' => $formattedStartDate,
            'endDate' => $formattedEndDate,
        ];

        $tours = $this->tours->searchTours($dataSearch);

        return view('clients.search', compact('title', 'tours'));
    }
}