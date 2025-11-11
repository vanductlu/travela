<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private $tours;

    public function __construct()
    {
        $this->tours = new Tours();
    }

    /**
     * API gợi ý tìm kiếm từ database
     */
    public function getSuggestions(Request $request)
    {
        $keyword = strtolower(trim($request->query('keyword', '')));

        // Nếu không có keyword hoặc quá ngắn
        if (empty($keyword) || strlen($keyword) < 2) {
            return response()->json(['suggestions' => []]);
        }

        try {
            // Tìm kiếm trong database - tìm cả title, destination và description
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

            // Tạo danh sách gợi ý unique
            $suggestions = [];
            foreach ($tours as $tour) {
                // Ưu tiên title
                if (stripos($tour->title, $keyword) !== false) {
                    $suggestions[] = $tour->title;
                }
                // Thêm destination nếu match
                if (stripos($tour->destination, $keyword) !== false && 
                    !in_array($tour->destination, $suggestions)) {
                    $suggestions[] = "Tour " . $tour->destination;
                }
            }

            // Loại bỏ duplicate và giới hạn
            $suggestions = array_values(array_unique($suggestions));
            $suggestions = array_slice($suggestions, 0, 6);

            return response()->json([
                'suggestions' => $suggestions,
                'count' => count($suggestions)
            ]);

        } catch (\Exception $e) {
            \Log::error('Lỗi khi lấy gợi ý: ' . $e->getMessage());
            return response()->json(['suggestions' => []]);
        }
    }

    /**
     * Tìm kiếm tour theo keyword
     */
    public function searchTours(Request $request)
    {
        $title = 'Tìm kiếm';
        $keyword = $request->input('keyword');

        // Nếu không có keyword, redirect về trang tours
        if (empty($keyword)) {
            return redirect()->route('tours');
        }

        // Gọi API Python để tìm kiếm nâng cao (nếu cần)
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/search-tours';
            $response = Http::timeout(3)->get($apiUrl, [
                'keyword' => $keyword
            ]);

            if ($response->successful()) {
                $resultTours = $response->json('related_tours', []);
            } else {
                $resultTours = [];
            }
        } catch (\Exception $e) {
            $resultTours = [];
            \Log::error('Lỗi khi gọi API Python: ' . $e->getMessage());
        }

        // Lấy tours từ database
        if (!empty($resultTours)) {
            $tours = $this->tours->toursSearch($resultTours);
        } else {
            $dataSearch = ['keyword' => $keyword];
            $tours = $this->tours->searchTours($dataSearch);
        }

        return view('clients.search', compact('title', 'tours', 'keyword'));
    }

    /**
     * Tìm kiếm tour theo destination và date
     */
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

        // Chuyển đổi định dạng ngày tháng
        $formattedStartDate = $startDate ? Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d') : null;
        $formattedEndDate = $endDate ? Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d') : null;

        // Chuyển đổi giá trị sang tên chi tiết nếu có trong mảng
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