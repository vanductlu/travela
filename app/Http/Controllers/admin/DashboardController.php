<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\DashboardModel;
use Illuminate\Http\Request;
use App\Models\Admin\UserModel;
class DashboardController extends Controller
{

    private $dashboard;
    private $users;
    public function __construct()
    {
        $this->dashboard = new DashboardModel();
        $this->users = new UserModel();
    }
    public function index()
    {
        $title = 'Admin';

        $summary = $this->dashboard->getSummary();
        $valueTour = $this->dashboard->getValueDomain();
        $dataDomain = [
            'values' => [
                $valueTour['b'] ?? 0,
                $valueTour['t'] ?? 0,
                $valueTour['n'] ?? 0,
            ]
        ];

        $paymentStatus = $this->dashboard->getValuePayment();
        $summary['totalUsers'] = $this->users->getTotalUsers();
        $toursBooked = $this->dashboard->getMostTourBooked();
        $newBooking = $this->dashboard->getNewBooking();
        $revenue = $this->dashboard->getRevenuePerMonth();
        // dd($revenue);
        return view('admin.dashboard', compact('title', 'summary', 'dataDomain', 'paymentStatus','toursBooked','newBooking','revenue'));
    }
}
