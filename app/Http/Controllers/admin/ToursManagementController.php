<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\ToursModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
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

    // ✅ FIX: Thêm logging và kiểm tra kỹ hơn
    public function addImagesTours(Request $request)
    {
        try {
            $image = $request->file('image');
            $tourId = $request->tourId;

            // Kiểm tra tourId
            if (!$tourId) {
                Log::error('addImagesTours: tourId is missing');
                return response()->json(['success' => false, 'message' => 'Tour ID is required'], 400);
            }

            // Kiểm tra xem file có hợp lệ không
            if (!$image || !$image->isValid()) {
                Log::error('addImagesTours: Invalid file upload', ['tourId' => $tourId]);
                return response()->json(['success' => false, 'message' => 'Invalid file upload'], 400);
            }

            // Lấy tên gốc của file
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();

            // Tạo tên file mới
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName) . '_' . time() . '.' . $extension;

            // Resize hình ảnh
            $resizedImage = Image::make($image)->resize(400, 350);

            // Đường dẫn lưu file
            $destinationPath = public_path('clients/assets/images/gallery-tours/');
            
            // ✅ Tạo thư mục nếu chưa tồn tại
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Lưu ảnh
            $resizedImage->save($destinationPath . $filename);

            // Log để debug
            Log::info('Image saved successfully', [
                'tourId' => $tourId,
                'filename' => $filename,
                'path' => $destinationPath . $filename
            ]);

            // Tạo dữ liệu để lưu vào database
            $dataUpload = [
                'tourId' => $tourId,
                'imageURL' => $filename,
                'description' => $originalName
            ];

            // Lưu vào database
            $uploadImage = $this->tours->uploadImages($dataUpload);

            // Log kết quả insert
            Log::info('Database insert result', [
                'success' => $uploadImage ? 'yes' : 'no',
                'data' => $dataUpload
            ]);

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

            return response()->json(['success' => false, 'message' => 'Failed to save image data to database'], 500);
            
        } catch (\Exception $e) {
            Log::error('addImagesTours error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ✅ FIX: Kiểm tra có ảnh trước khi hoàn tất tour
    public function addTimeline(Request $request)
    {
        $tourId = $request->tourId;

        // ✅ Kiểm tra xem tour đã có ảnh chưa
        $hasImages = $this->tours->hasImages($tourId);
        
        if (!$hasImages) {
            toastr()->error('Vui lòng upload ít nhất 1 ảnh cho tour!');
            return redirect()->back();
        }

        // Tạo một mảng chứa các timeline
        $timelines = [];

        // Lặp qua tất cả các keys trong request
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^day-(\d+)$/', $key, $matches)) {
                $dayNumber = $matches[1];

                $itineraryKey = "itinerary-{$dayNumber}";
                if ($request->has($itineraryKey)) {
                    $timelines[] = [
                        'tourId' => $tourId,
                        'title' => $value,
                        'description' => $request->input($itineraryKey),
                    ];
                }
            }
        }

        // Lưu timeline
        foreach ($timelines as $timeline) {
            $this->tours->addTimeLine($timeline);
        }

        // Cập nhật availability
        $dataUpdate = ['availability' => 1];
        $updateAvailability = $this->tours->updateTour($tourId, $dataUpdate);

        toastr()->success('Thêm tour thành công!');
        return redirect()->route('admin.page-add-tours');
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
            $image = $request->file('image');
            $tourId = $request->tourId;

            if (!$image->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid file upload'], 400);
            }

            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName) . '_' . time() . '.' . $extension;

            $resizedImage = Image::make($image)->resize(400, 350);

            // ✅ Lưu vào cùng thư mục với ảnh chính
            $destinationPath = public_path('clients/assets/images/gallery-tours/');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $resizedImage->save($destinationPath . $filename);

            $dataUpload = [
                'tourId' => $tourId,
                'imageTempURL' => $filename,
            ];

            $uploadImage = $this->tours->uploadTempImages($dataUpload);

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
            Log::error('uploadTempImagesTours error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateTour(Request $request)
    {
        $tourId = $request->tourId;
        $name = $request->input('name');
        $destination = $request->input('destination');
        $domain = $request->input('domain');
        $quantity = $request->input('number');
        $price_adult = $request->input('price_adult');
        $price_child = $request->input('price_child');
        $description = $request->input('description');

        $dataTours = [
            'title' => $name,
            'description' => $description,
            'quantity' => $quantity,
            'priceAdult' => $price_adult,
            'priceChild' => $price_child,
            'destination' => $destination,
            'domain' => $domain,
        ];

        // Xóa dữ liệu cũ
        $this->tours->deleteData($tourId, 'tbl_timeline');
        $this->tours->deleteData($tourId, 'tbl_images');

        // Cập nhật tour
        $updateTour = $this->tours->updateTour($tourId, $dataTours);

        // Thêm images mới
        $images = $request->input('images');
        if ($images && is_array($images)) {
            foreach ($images as $image) {
                $dataUpload = [
                    'tourId' => $tourId,
                    'imageURL' => $image,
                    'description' => $name
                ];
                $this->tours->uploadImages($dataUpload);
            }
        }

        // Thêm timeline mới
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

        return response()->json([
            'success' => true,
            'message' => 'Sửa thành công!',
        ]);
    }

    public function deleteTour(Request $request)
    {
        $tourId = $request->tourId;

        $result = $this->tours->deleteTour($tourId);
        
        if ($result['success']) {
            $tours = $this->tours->getAllTours();
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => view('admin.partials.list-tours', compact('tours'))->render()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }

    // ✅ THÊM: Kiểm tra tour có ảnh chưa
    public function checkTourImages(Request $request)
    {
        $tourId = $request->tourId;
        $hasImages = $this->tours->hasImages($tourId);
        
        return response()->json([
            'success' => true,
            'hasImages' => $hasImages,
            'count' => $hasImages ? $this->tours->getImages($tourId)->count() : 0
        ]);
    }
}