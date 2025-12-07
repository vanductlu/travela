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
use App\Http\Controllers\clients\ChatController;
use App\Http\Controllers\clients\PasswordResetController;
use App\Http\Controllers\clients\AiTranslateController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\admin\CouponManagementController;
use App\Http\Controllers\admin\TravelGuidesManagement;
use App\Http\Controllers\admin\LoginAdminController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\AdminManagementController;
use App\Http\Controllers\admin\UserManagementController;
use App\Http\Controllers\admin\ToursManagementController;
use App\Http\Controllers\admin\BookingManagementController;
use App\Http\Controllers\admin\ContactManagementController;
use App\Http\Controllers\admin\BlogManagementController;
use App\Http\Controllers\admin\CommentManagementController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::get('/contact', [ContactController::class, 'index'])->name('contact')->middleware('checkLoginClient');
Route::post('/contact', [ContactController::class, 'createContact'])->name('create-contact');
Route::get('/destination', [DestinationController::class, 'index'])->name('destination');
Route::get('/my-tour', [MyTourController::class, 'index'])->name('my-tour');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/tour-booked', [TourBookedController::class, 'index'])->name('tour-booked');
Route::get('/tour-detail/{id?}', [TourDetailController::class, 'index'])->name('tour-detail');
Route::get('/travel-guides', [TravelGuidesController::class, 'index'])->name('travel-guides')->middleware('checkLoginClient');
Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user-profile');
Route::post('/user-profile', [UserProfileController::class, 'update'])->name('update-user-profile');
Route::post('/createBooking', [BookingController::class, 'store'])->name('create-booking');

Route::get('/blog', [BlogController::class, 'index'])->name('blog')->middleware('checkLoginClient');  
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search')->middleware('checkLoginClient');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category')->middleware('checkLoginClient');
Route::middleware(['web'])->group(function () {
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog-details');
});
Route::post('/blog/{id}/comment', [BlogController::class, 'comment'])->name('blog.comment');

Route::get('/tours', [ToursController::class, 'index'])->name('tours');
Route::get('/filter-tours', [ToursController::class, 'filterTours'])->name('filter-tours');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('user-login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('activate-account/{token}', [LoginController::class, 'activateAccount'])->name('activate.account');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.send');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

Route::get('auth/google', [LoginGoogleController::class, 'redirectToGoogle'])->name('login-google');
Route::get('auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);


Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user-profile')->middleware('checkLoginClient');
Route::post('/user-profile', [UserProfileController::class, 'update'])->name('update-user-profile');
Route::post('/change-password-profile', [UserProfileController::class, 'changePassword'])->name('change-password');
Route::post('/change-avatar-profile', [UserProfileController::class, 'changeAvatar'])->name('change-avatar');

Route::post('/booking/{id?}', [BookingController::class, 'index'])->name('booking')->middleware('checkLoginClient');
Route::get('/booking/{id?}', [BookingController::class, 'index'])->name('booking')->middleware('checkLoginClient');
Route::post('/create-booking', [BookingController::class, 'createBooking'])->name('create-booking');
Route::get('/booking', [BookingController::class, 'handlePaymentMomoCallback'])->name('handlePaymentMomoCallback');

Route::get('/my-tours', [MyTourController::class, 'index'])->name('my-tours')->middleware('checkLoginClient');

Route::get('/tour-booked', [TourBookedController::class, 'index'])->name('tour-booked')->middleware('checkLoginClient');
Route::post('/cancel-booking', [TourBookedController::class, 'cancelBooking'])->name('cancel-booking');
Route::post('/reviews', [TourDetailController::class, 'reviews'])->name('reviews')->middleware('checkLoginClient');
Route::post('/checkBooking', [BookingController::class, 'checkBooking'])->name('checkBooking')->middleware('checkLoginClient');
Route::post('/tour/apply-coupon', [TourBookedController::class, 'applyCoupon'])->name('tour.apply.coupon')->middleware('checkLoginClient');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/create-contact', [ContactController::class, 'createContact'])->name('create-contact');

Route::post('/create-momo-payment', [BookingController::class, 'createMomoPayment'])->name('createMomoPayment');
Route::get('/booking/momo/callback', [BookingController::class, 'handlePaymentMomoCallback'])->name('booking.momo.callback');

Route::get('/search', [SearchController::class, 'index'])->name(name: 'search');
Route::get('/search-voice-text', [SearchController::class, 'searchTours'])->name('search-voice-text');
Route::get('/api/search-suggestions', [SearchController::class, 'getSuggestions'])->name('api.search.suggestions');

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

Route::post('/ai/translate', [AiTranslateController::class, 'translate'])->name('ai.translate');

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginAdminController::class, 'index'])->name('admin.login');
    Route::post('/login-account', [LoginAdminController::class, 'loginAdmin'])->name('admin.login-account');
    Route::get('/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');

});
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin', [AdminManagementController::class, 'index'])->name('admin.admin');
    Route::post('/update-admin', [AdminManagementController::class, 'updateAdmin'])->name('admin.update-admin');
    Route::post('/update-avatar', [AdminManagementController::class, 'updateAvatar'])->name('admin.update-avatar');

    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::post('/active-user', [UserManagementController::class, 'activeUser'])->name('admin.active-user');
    Route::post('/status-user', [UserManagementController::class, 'changeStatus'])->name('admin.status-user');
    Route::post('/block-user', [UserManagementController::class, 'blockUser'])->name('admin.block-user');
    Route::post('/unblock-user', [UserManagementController::class, 'unblockUser'])->name('admin.unblock-user');
    Route::delete('/delete-user/{id}', [UserManagementController::class, 'deleteUser'])->name('admin.delete-user');


    Route::get('/tours', [ToursManagementController::class, 'index'])->name('admin.tours');

    Route::get('/page-add-tours', [ToursManagementController::class, 'pageAddTours'])->name('admin.page-add-tours');
    Route::post('/add-tours', [ToursManagementController::class, 'addTours'])->name('admin.add-tours');
    Route::post('/add-images-tours', [ToursManagementController::class, 'addImagesTours'])->name('admin.add-images-tours');
    Route::get('/add-timeline', [ToursManagementController::class, 'addTimeline'])->name('admin.add-timeline');
    Route::post('/add-timeline', [ToursManagementController::class, 'addTimeline'])->name('admin.add-timeline');
    Route::get('/check-before-delete-tour', [ToursManagementController::class, 'checkBeforeDelete'])->name('admin.check-before-delete-tour');
    Route::post('/delete-tour', [ToursManagementController::class, 'deleteTour'])->name('admin.delete-tour');

    Route::get('/tour-edit', [ToursManagementController::class, 'getTourEdit'])->name('admin.tour-edit');
    Route::post('/edit-tour', [ToursManagementController::class, 'updateTour'])->name('admin.edit-tour');
    Route::post('/add-images-tours', [ToursManagementController::class, 'addImagesTours'])->name('admin.add-images-tours');
    
    Route::get('/booking', [BookingManagementController::class, 'index'])->name('admin.booking');
    Route::post('/confirm-booking', [BookingManagementController::class, 'confirmBooking'])->name('admin.confirm-booking');
    Route::get('/booking-detail/{id?}', [BookingManagementController::class, 'showDetail'])->name('admin.booking-detail');
    Route::post('/finish-booking', [BookingManagementController::class, 'finishBooking'])->name('admin.finish-booking');
    Route::post('/received-money', [BookingManagementController::class, 'receiviedMoney'])->name('admin.received');

    
    Route::post('/coupon/{id}/toggle-status', [CouponManagementController::class, 'toggleStatus'])->name('admin.coupon.toggle-status');
    Route::get('/coupon', [CouponManagementController::class, 'index'])->name('admin.coupon.index');
    Route::get('/coupon/create', [CouponManagementController::class, 'create'])->name('admin.coupon.create');
    Route::post('/coupon', [CouponManagementController::class, 'store'])->name('admin.coupon.store');
    Route::get('/coupon/{id}', [CouponManagementController::class, 'show'])->name('admin.coupon.show');
    Route::get('/coupon/{id}/edit', [CouponManagementController::class, 'edit'])->name('admin.coupon.edit');
    Route::put('/coupon/{id}', [CouponManagementController::class, 'update'])->name('admin.coupon.update');
    Route::delete('/coupon/{id}', [CouponManagementController::class, 'destroy'])->name('admin.coupon.destroy');
   
    
    Route::post('/send-pdf', [BookingManagementController::class, 'sendPdf'])->name('admin.send.pdf');

    
    Route::get('/contact', [ContactManagementController::class, 'index'])->name('admin.contact');
    Route::post('/reply-contact', [ContactManagementController::class, 'replyContact'])->name('admin.reply-contact');
    
    Route::get('/blog', [BlogManagementController::class, 'index'])->name('admin.blog');       
    Route::get('/blog/create', [BlogManagementController::class, 'create'])->name('admin.blog.create'); 
    Route::post('/blog/store', [BlogManagementController::class, 'store'])->name('admin.blog.store');   
    Route::get('/blog/{id}/edit', [BlogManagementController::class, 'edit'])->name('admin.blog.edit');  
    Route::put('/blog/{id}/update', [BlogManagementController::class, 'update'])->name('admin.blog.update'); 
    Route::delete('/blog/{id}/delete', [BlogManagementController::class, 'destroy'])->name('admin.blog.delete'); 
    Route::get('/comments', [CommentManagementController::class, 'index'])->name('admin.comments');
    Route::delete('/comments/{id}', [CommentManagementController::class, 'destroy'])->name('admin.comments.delete');
    Route::delete('/comments/blog/{blogId}', [CommentManagementController::class, 'deleteByBlog'])->name('admin.comments.deleteByBlog');

});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('travelguides', [TravelGuidesManagement::class, 'index'])->name('travelguides');
    Route::post('team/store', [TravelGuidesManagement::class, 'store'])->name('team.store');
    Route::post('team/update/{team}', [TravelGuidesManagement::class, 'update'])->name('team.update');
    Route::get('team/delete/{team}', [TravelGuidesManagement::class, 'delete'])->name('team.delete');
    Route::get('team/activate/{team}', [TravelGuidesManagement::class, 'activate'])->name('team.activate');
});

