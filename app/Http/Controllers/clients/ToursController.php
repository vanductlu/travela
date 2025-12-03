<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Tours;

class ToursController extends Controller
{   
    private $tours;
    public function __construct()
    {
        $this->tours = new Tours();
    }
    public function index(Request $request)
    {   
        $title = 'Tours';
        $tours = $this->tours->getAllTours();
        $domain = $this->tours->getDomain();
        $domainsCount = [
            'mien_bac' => optional($domain->firstWhere('domain', 'b'))->count,
            'mien_trung' => optional($domain->firstWhere('domain', 't'))->count,
            'mien_nam' => optional($domain->firstWhere('domain', 'n'))->count,
        ];
        if ($request->ajax()) {
            return response()->json([
                'tours' => view('clients.partials.filter-tours', compact('tours'))->render(),
            ]);
        }
        $toursPopular = $this->tours->toursPopular(2);

        return view('clients.tours', compact('title', 'tours', 'domainsCount','toursPopular'));
    }

    public function filterTours(Request $req)
    {

        $conditions = [];
        $sorting = [];

        if ($req->filled('minPrice') && $req->filled('maxPrice')) {
            $minPrice = $req->minPrice;
            $maxPrice = $req->maxPrice;
            $conditions[] = ['priceAdult', '>=', $minPrice];
            $conditions[] = ['priceAdult', '<=', $maxPrice];
        }

        if ($req->filled('domain')) {
            $domain = $req->domain;
            $conditions[] = ['domain', '=', $domain];
        }

        if ($req->filled('filter_star')) {
            $star = (int) $req->filter_star;
            $conditions[] = ['averageRating', '>=', $star];
        }

        if ($req->filled('duration')) {
            $duration = $req->duration;
            $time = [
                '3n2d' => '3 ngày 2 đêm',
                '4n3d' => '4 ngày 3 đêm',
                '5n4d' => '5 ngày 4 đêm'
            ];
            $conditions[] = ['time', '=', $time[$duration]];
        }

        if ($req->filled('sorting')) {
            $sortingOption = trim($req->sorting);

            if ($sortingOption == 'new') {
                $sorting = ['tourId', 'DESC'];
            } elseif ($sortingOption == 'old') {
                $sorting = ['tourId', 'ASC'];
            } elseif ($sortingOption == "hight-to-low") {
                $sorting = ['priceAdult', 'DESC'];
            } elseif ($sortingOption == "low-to-high") {
                $sorting = ['priceAdult', 'ASC'];
            }
        }

        $tours = $this->tours->filterTours($conditions, $sorting);
        if (!$tours instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $tours = new \Illuminate\Pagination\LengthAwarePaginator(
                $tours, 
                count($tours), 
                9, 
                1, 
                ['path' => url()->current()] 
            );
        }

        return view('clients.partials.filter-tours', compact('tours'));

    }
}
