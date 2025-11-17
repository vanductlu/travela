<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ToursModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_tours';

    public function getAllTours()
    {
        return DB::table($this->table)
            ->orderBy('tourId', 'DESC')
            ->get();
    }

    public function createTours($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function uploadImages($data)
    {
        return DB::table('tbl_images')->insert($data);
    }

    public function uploadTempImages($data)
    {
        return DB::table('tbl_temp_images')->insert($data);
    }

    public function addTimeLine($data)
    {
        return DB::table('tbl_timeline')->insert($data);
    }

    public function updateTour($tourId, $data)
    {
        return DB::table($this->table)
            ->where('tourId', $tourId)
            ->update($data);
    }

    /**
     * ✅ XÓA HẲN TOUR - Xóa theo đúng thứ tự foreign key
     * Thứ tự xóa: checkout → reviews → booking → timeline → images → tour
     */
    public function deleteTour($tourId)
    {
        try {
            DB::beginTransaction();

            // Bước 1: Lấy tất cả bookingId liên quan đến tour
            $bookingIds = DB::table('tbl_booking')
                ->where('tourId', $tourId)
                ->pluck('bookingId')
                ->toArray();

            Log::info("Deleting tour {$tourId}, found " . count($bookingIds) . " bookings");

            // Bước 2: Xóa checkout (phụ thuộc vào booking)
            if (!empty($bookingIds)) {
                $deletedCheckout = DB::table('tbl_checkout')
                    ->whereIn('bookingId', $bookingIds)
                    ->delete();
                Log::info("Deleted {$deletedCheckout} checkout records");
            }

            // Bước 3: Xóa reviews (phụ thuộc vào tour)
            $deletedReviews = DB::table('tbl_reviews')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedReviews} reviews");

            // Bước 4: Xóa booking (phụ thuộc vào tour)
            $deletedBookings = DB::table('tbl_booking')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedBookings} bookings");

            // Bước 5: Xóa timeline
            $deletedTimeline = DB::table('tbl_timeline')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedTimeline} timeline records");

            // Bước 6: Xóa images
            $deletedImages = DB::table('tbl_images')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedImages} images");

            // Bước 7: Xóa tour
            $deletedTour = DB::table($this->table)
                ->where('tourId', $tourId)
                ->delete();

            if ($deletedTour) {
                DB::commit();
                Log::info("Successfully deleted tour {$tourId}");
                return [
                    'success' => true, 
                    'message' => 'Tour đã được xóa hoàn toàn.',
                    'details' => [
                        'bookings' => $deletedBookings,
                        'checkouts' => $deletedCheckout ?? 0,
                        'reviews' => $deletedReviews,
                        'timeline' => $deletedTimeline,
                        'images' => $deletedImages
                    ]
                ];
            }

            DB::rollBack();
            return ['success' => false, 'message' => 'Không tìm thấy tour để xóa.'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete tour error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false, 
                'message' => 'Lỗi khi xóa tour: ' . $e->getMessage()
            ];
        }
    }

    public function getTour($tourId)
    {
        return DB::table($this->table)->where('tourId', $tourId)->first();
    }

    public function getImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->get();
    }

    /**
     * ✅ Lấy danh sách ảnh dưới dạng mảng URL
     */
    public function getImageUrls($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->pluck('imageUrl')
            ->toArray();
    }

    public function getTimeLine($tourId)
    {
        return DB::table('tbl_timeline')->where('tourId', $tourId)->get();
    }

    public function deleteData($tourId, $tbl)
    {
        return DB::table($tbl)->where('tourId', $tourId)->delete();
    }

    /**
     * ✅ Kiểm tra tour có ảnh chưa
     */
    public function hasImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->exists();
    }

    /**
     * ✅ Đếm số lượng ảnh của tour
     */
    public function countImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->count();
    }

    /**
     * ✅ Kiểm tra tour có booking không
     */
    public function hasBookings($tourId)
    {
        return DB::table('tbl_booking')
            ->where('tourId', $tourId)
            ->exists();
    }

    /**
     * ✅ Đếm số booking của tour
     */
    public function countBookings($tourId)
    {
        return DB::table('tbl_booking')
            ->where('tourId', $tourId)
            ->count();
    }

    /**
     * ✅ Lấy thông tin chi tiết về các bản ghi liên quan
     */
    public function getRelatedRecords($tourId)
    {
        $bookings = DB::table('tbl_booking')->where('tourId', $tourId)->count();
        $reviews = DB::table('tbl_reviews')->where('tourId', $tourId)->count();
        $images = DB::table('tbl_images')->where('tourId', $tourId)->count();
        $timeline = DB::table('tbl_timeline')->where('tourId', $tourId)->count();
        
        $bookingIds = DB::table('tbl_booking')->where('tourId', $tourId)->pluck('bookingId');
        $checkouts = DB::table('tbl_checkout')->whereIn('bookingId', $bookingIds)->count();

        return [
            'bookings' => $bookings,
            'checkouts' => $checkouts,
            'reviews' => $reviews,
            'images' => $images,
            'timeline' => $timeline
        ];
    }
}