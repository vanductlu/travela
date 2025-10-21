<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Tours extends Model
{
    use HasFactory;

    protected $table = 'tbl_tours';
    //lấy tất cả tour
    public function getAllTours()
    {
        $allTours = DB::table($this->table)
            ->get();

        foreach($allTours as $tour){
            $tour ->images = DB::table('tbl_images')
                            ->where('tourId', $tour->tourId)
                            ->pluck('imageUrl');
        }
        return $allTours;
    }
    //lấy chi tiết tour
    public function getTourDetail($id)
    {
        $getTourDetail = DB::table($this->table)
            ->where('tourId', $id)
            ->first();

        if ($getTourDetail) {
            // Lấy danh sách hình ảnh thuộc về tour
            $getTourDetail->images = DB::table('tbl_images')
                ->where('tourId', $getTourDetail->tourId)
                ->limit(5)
                ->pluck('imageUrl');

            // Lấy danh sách timeline thuộc về tour
            $getTourDetail->timeline = DB::table('tbl_timeline')
                ->where('tourId', $getTourDetail->tourId)
                ->get();
        }
        return $getTourDetail;
    }

    //Lấy khu vực đến Bắc _ Trung _ Nam
    function getDomain()
    {
        return DB::table($this->table)
            ->select('domain', DB::raw('COUNT(*) as count'))
            ->whereIn('domain', ['b', 't', 'n'])
            ->groupBy('domain')
            ->get();
    }

    //Filter tours
    public function filterTours($filter = [], $sorting = null, $perPage = 6)
    {
        DB::enableQueryLog();
        $getTours = DB::table($this->table);

        // Áp dụng bộ lọc nếu có
        if (!empty($filter))
        {
            $getTours = $getTours->where($filter);
        }

        if (!empty($sorting) && isset($sorting[0]) && isset($sorting[1]))
        {
            $getTours = $getTours->orderBy($sorting[0], $sorting[1]);
        }
        // Thực hiện truy vấn để ghi log
        $tours = $getTours->get();

        //In ra câu lệnh SQL đã ghi lại 

        $queryLog = DB::getQueryLog();

        foreach ($tours as $tour)
        {
            $tour->images = DB::table('tbl_images')
                ->where('tourId', $tour->tourId)
                ->pluck('imageUrl');
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
}
