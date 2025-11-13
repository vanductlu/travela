<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        $updated = DB::table($this->table)
            ->where('tourId', $tourId)
            ->update($data);

        return $updated;
    }

    // ✅ FIX: Sửa logic xóa tour - KHÔNG XÓA tbl_temp_images
    public function deleteTour($tourId)
    {
        try {
            DB::beginTransaction();

            // Xóa timeline
            DB::table('tbl_timeline')->where('tourId', $tourId)->delete();
            
            // Xóa images
            DB::table('tbl_images')->where('tourId', $tourId)->delete();

            // Xóa tour chính
            $deleteTour = DB::table($this->table)->where('tourId', $tourId)->delete();

            if ($deleteTour) {
                DB::commit();
                return ['success' => true, 'message' => 'Tour đã được xóa thành công.'];
            } else {
                DB::rollBack();
                return ['success' => false, 'message' => 'Không tìm thấy tour để xóa.'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Lỗi khi xóa tour: ' . $e->getMessage()];
        }
    }

    public function getTour($tourId)
    {
        return DB::table($this->table)->where('tourId', $tourId)->first();
    }

    // ✅ FIX: Lấy images dưới dạng mảng
    public function getImages($tourId)
    {
        return DB::table('tbl_images')
            ->where('tourId', $tourId)
            ->pluck('imageURL')
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

    // ✅ THÊM: Kiểm tra xem tour có images chưa
    public function hasImages($tourId)
    {
        return DB::table('tbl_images')->where('tourId', $tourId)->exists();
    }
}