<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\clients\HomeController;
use App\Http\Controllers\clients\AboutController;
use App\Http\Controllers\clients\BookingController;
use App\Http\Controllers\clients\ContactController;
use App\Http\Controllers\clients\DestinationController;
use App\Http\Controllers\clients\LoginController;
use App\Http\Controllers\clients\MyTourController;
use App\Http\Controllers\clients\SearchController;
use App\Http\Controllers\clients\TourBookedController;
use App\Http\Controllers\clients\TourDetailController;
use App\Http\Controllers\clients\ToursController;
use App\Http\Controllers\clients\TravelGuidesController;
use App\Http\Controllers\clients\UserProfileController;
use App\Http\Controllers\clients\BlogController;
use App\Http\Controllers\clients\BlogDetailsController;
use App\Http\Controllers\clients\TourGridController;
use App\Http\Controllers\clients\TourListController;
use App\Http\Controllers\clients\LoginGoogleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('create-contact');
Route::get('/destination', [DestinationController::class, 'index'])->name('destination');
Route::get('/my-tour', [MyTourController::class, 'index'])->name('my-tour');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/tour-booked', [TourBookedController::class, 'index'])->name('tour-booked');
Route::get('/tour-detail/{id?}', [TourDetailController::class, 'index'])->name('tour-detail');
Route::get('/travel-guides', [TravelGuidesController::class, 'index'])->name('travel-guides');
Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user-profile');
Route::post('/user-profile', [UserProfileController::class, 'update'])->name('update-user-profile');
Route::post('/createBooking', [BookingController::class, 'store'])->name('create-booking');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');    
Route::get('/blog-details', [BlogDetailsController::class, 'index'])->name('blog-details');
Route::get('/tour-grid', [TourGridController::class, 'index'])->name('tour-grid');
Route::get('/tour-list', [TourListController::class, 'index'])->name('tour-list');

//Handle Get tours , filter Tours
Route::get('/tours', [ToursController::class, 'index'])->name('tours');
Route::get('/filter-tours', [ToursController::class, 'filterTours'])->name('filter-tours');

//Handle Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('user-login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('activate-account/{token}', [LoginController::class, 'activateAccount'])->name('activate.account');

//Login with google
Route::get('auth/google', [LoginGoogleController::class, 'redirectToGoogle'])->name('login-google');
Route::get('auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);