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

    public function deleteTour($tourId)
    {
        try {
            DB::beginTransaction();

            $bookingIds = DB::table('tbl_booking')
                ->where('tourId', $tourId)
                ->pluck('bookingId')
                ->toArray();

            Log::info("Deleting tour {$tourId}, found " . count($bookingIds) . " bookings");

            if (!empty($bookingIds)) {
                $deletedCheckout = DB::table('tbl_checkout')
                    ->whereIn('bookingId', $bookingIds)
                    ->delete();
                Log::info("Deleted {$deletedCheckout} checkout records");
            }
            $deletedReviews = DB::table('tbl_reviews')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedReviews} reviews");

            $deletedBookings = DB::table('tbl_booking')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedBookings} bookings");

            $deletedTimeline = DB::table('tbl_timeline')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedTimeline} timeline records");

            $deletedImages = DB::table('tbl_images')
                ->where('tourId', $tourId)
                ->delete();
            Log::info("Deleted {$deletedImages} images");

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

    public function hasImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->exists();
    }

    public function countImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->count();
    }

    public function hasBookings($tourId)
    {
        return DB::table('tbl_booking')
            ->where('tourId', $tourId)
            ->exists();
    }

    public function countBookings($tourId)
    {
        return DB::table('tbl_booking')
            ->where('tourId', $tourId)
            ->count();
    }

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