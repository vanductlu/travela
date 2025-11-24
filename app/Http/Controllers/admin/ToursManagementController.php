<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\ToursModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ToursManagementController extends Controller
{
    private $tours;

    public function __construct()
    {   
        $this->tours = new ToursModel();
    }

    public function index()
    {
        $title = 'Quản lý Tours';
        $tours = $this->tours->getAllTours();
        return view('admin.tours', compact('title', 'tours'));
    }

    public function pageAddTours()
    {
        $title = 'Thêm Tours';
        return view('admin.add-tours', compact('title'));
    }

    public function addTours(Request $request)
    {
        $name = $request->input('name');
        $destination = $request->input('destination');
        $domain = $request->input('domain');
        $quantity = $request->input('number');
        $price_adult = $request->input('price_adult');
        $price_child = $request->input('price_child');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $description = $request->input('description');

        // Chuyển start_date và end_date từ định dạng d/m/Y sang Y-m-d
        $startDate = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');

        // Tính số ngày giữa start_date và end_date
        $days = Carbon::createFromFormat('Y-m-d', $startDate)->diffInDays(Carbon::createFromFormat('Y-m-d', $endDate));

        // Tính số đêm: số ngày - 1
        $nights = $days - 1;

        // Định dạng thời gian theo kiểu "X ngày Y đêm"
        $time = "{$days} ngày {$nights} đêm";

        $dataTours = [
            'title' => $name,
            'time' => $time,
            'description' => $description,
            'quantity' => $quantity,
            'priceAdult' => $price_adult,
            'priceChild' => $price_child,
            'destination' => $destination,
            'domain' => $domain,
            'availability' => 0,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $createTour = $this->tours->createTours($dataTours);

        return response()->json([
            'success' => true,
            'message' => 'Tour added successfully!',
            'tourId' => $createTour
        ]);
    }

public function addImagesTours(Request $request)
{
    try {
        $image = $request->file('image');
        $tourId = $request->tourId;

        if (!$tourId) {
            return response()->json(['success' => false, 'message' => 'Tour ID is required'], 400);
        }

        if (!$image || !$image->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid file upload'], 400);
        }

        // Tạo tên file mới
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName) . '_' . time() . '.' . $extension;

        // Thư mục lưu ảnh
        $destinationPath = public_path('clients/assets/images/gallery-tours/');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // LƯU FILE GỐC — KHÔNG DÙNG GD
        $image->move($destinationPath, $filename);

        // Lưu DB
        $dataUpload = [
            'tourId' => $tourId,
            'imageUrl' => $filename,
            'description' => $originalName
        ];

        $uploadImage = $this->tours->uploadImages($dataUpload);

        if ($uploadImage) {
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'filename' => $filename,
                    'tourId' => $tourId
                ]
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Failed to save image data'], 500);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


public function addTimeline(Request $request)
{
    $tourId = $request->tourId;

    // Kiểm tra ID
    if (!$tourId) {
        return response()->json([
            'success' => false,
            'message' => 'Thiếu Tour ID'
        ], 400);
    }

    // Kiểm tra ảnh
    $hasImages = $this->tours->hasImages($tourId);
    if (!$hasImages) {
        return response()->json([
            'success' => false,
            'message' => 'Vui lòng upload ít nhất 1 ảnh cho tour!'
        ], 400);
    }

    // Lấy dữ liệu timeline (JSON)
    $timelineJson = $request->timeline;
    $timelineArray = json_decode($timelineJson, true);

    if (!$timelineArray || !is_array($timelineArray)) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu timeline không hợp lệ'
        ], 400);
    }

    // Lưu timeline vào database
    foreach ($timelineArray as $item) {
        $this->tours->addTimeLine([
            'tourId' => $tourId,
            'title' => $item['title'],
            'description' => $item['content']
        ]);
    }

    // Cập nhật trạng thái hoàn thành tour
    $this->tours->updateTour($tourId, ['availability' => 1]);

    return response()->json([
        'success' => true,
        'message' => 'Thêm timeline thành công!'
    ]);
}


    public function getTourEdit(Request $request)
    {
        $tourId = $request->tourId;
        $getTour = $this->tours->getTour($tourId);

        if (!$getTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour'
            ]);
        }

        // Kiểm tra ngày bắt đầu
        $startDate = Carbon::parse($getTour->startDate);
        $today = Carbon::now();

        if ($startDate->lessThanOrEqualTo($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chỉnh sửa vì tour đã hoặc đang diễn ra.',
            ]);
        }

        $getImages = $this->tours->getImages($tourId);
        $getTimeLine = $this->tours->getTimeLine($tourId);

        return response()->json([
            'success' => true,
            'tour' => $getTour,
            'images' => $getImages,
            'timeline' => $getTimeLine
        ]);
    }

public function uploadTempImagesTours(Request $request)
{
    try {
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'No image uploaded'], 400);
        }

        $image = $request->file('image');

        if (!$image->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid image upload'], 400);
        }

        // Lấy tên file
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName) . '_' . time() . '.' . $extension;

        // Thư mục lưu ảnh tạm
        $destinationPath = public_path('clients/assets/images/gallery-tours-temp/');

        // Nếu thư mục chưa tồn tại → tạo
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // LƯU FILE GỐC — KHÔNG XỬ LÝ, KHÔNG DÙNG GD
        $image->move($destinationPath, $filename);

        return response()->json([
            'success' => true,
            'message' => 'Temp image uploaded successfully',
            'file' => $filename
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


public function updateTour(Request $request)
{
    try {
        $tourId = $request->tourId;
        if (!$tourId) {
            return response()->json(['success' => false, 'message' => 'Missing tourId'], 400);
        }

        $name = $request->input('name');
        $description = $request->input('description');
        // bạn có thể validate fields ở đây

        $dataTours = [
            'title' => $name,
            'description' => $description,
            'quantity' => $request->input('number'),
            'priceAdult' => $request->input('price_adult'),
            'priceChild' => $request->input('price_child'),
            'destination' => $request->input('destination'),
            'domain' => $request->input('domain'),
        ];

        // XÓA DỮ LIỆU CŨ
        $this->tours->deleteData($tourId, 'tbl_timeline');
        $this->tours->deleteData($tourId, 'tbl_images');

        // CẬP NHẬT TOUR
        $updateTour = $this->tours->updateTour($tourId, $dataTours);

        // ADD images mới (JS gửi array filenames trực tiếp)
        $images = $request->input('images');
        if ($images && is_array($images)) {
            foreach ($images as $image) {
                $dataUpload = [
                    'tourId' => $tourId,
                    'imageUrl' => $image,
                    'description' => $name
                ];
                $this->tours->uploadImages($dataUpload);
            }
        }

        // ADD timelines
        $timelines = $request->input('timeline');
        if ($timelines && is_array($timelines)) {
            foreach ($timelines as $timeline) {
                $data = [
                    'tourId' => $tourId,
                    'title' => $timeline['title'],
                    'description' => $timeline['itinerary']
                ];
                $this->tours->addTimeLine($data);
            }
        }

        return response()->json(['success' => true, 'message' => 'Sửa thành công!']);
    } catch (\Exception $e) {
        \Log::error('updateTour error: '.$e->getMessage(), [
            'input' => $request->all()
        ]);
        return response()->json(['success' => false, 'message' => 'Lỗi server: '.$e->getMessage()], 500);
    }
}


    /**
 * ✅ Xóa tour - Hiển thị thông tin và xác nhận
 */
public function deleteTour(Request $request)
{
    $tourId = $request->tourId;

    // Lấy thông tin các bản ghi liên quan
    $related = $this->tours->getRelatedRecords($tourId);
    
    // Log để debug
    Log::info("Attempting to delete tour {$tourId}", $related);

    // Thực hiện xóa
    $result = $this->tours->deleteTour($tourId);
    
    if ($result['success']) {
        // Lấy danh sách tours mới sau khi xóa
        $tours = $this->tours->getAllTours();
        
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'details' => $result['details'] ?? null,
            'data' => view('admin.partials.list-tours', compact('tours'))->render()
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }
}

/**
 * ✅ API kiểm tra thông tin trước khi xóa
 */
public function checkBeforeDelete(Request $request)
{
    $tourId = $request->tourId;
    
    $tour = $this->tours->getTour($tourId);
    if (!$tour) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy tour'
        ], 404);
    }

    $related = $this->tours->getRelatedRecords($tourId);
    
    $warnings = [];
    if ($related['bookings'] > 0) {
        $warnings[] = "Tour có {$related['bookings']} booking";
    }
    if ($related['checkouts'] > 0) {
        $warnings[] = "{$related['checkouts']} thanh toán";
    }
    if ($related['reviews'] > 0) {
        $warnings[] = "{$related['reviews']} đánh giá";
    }
    
    return response()->json([
        'success' => true,
        'tour' => $tour,
        'related' => $related,
        'warnings' => $warnings,
        'canDelete' => true, // Luôn cho phép xóa
        'message' => count($warnings) > 0 
            ? 'Tour có dữ liệu liên quan. Tất cả sẽ bị xóa!' 
            : 'Tour có thể xóa an toàn.'
    ]);
}

    // ✅ THÊM: Kiểm tra tour có ảnh chưa
    public function checkTourImages(Request $request)
    {
        $tourId = $request->tourId;
         $count = DB::table('tbl_images')   // nhớ sửa tên bảng cho đúng
        ->where('tourId', $tourId)
        ->count();
        
        return response()->json([
        'success' => true,
        'hasImages' => $count > 0,
        'count' => $count
    ]);
    }
}