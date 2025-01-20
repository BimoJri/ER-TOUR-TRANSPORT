<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\clientCarController;
use App\Http\Controllers\adminDashboardController;
use App\Http\Controllers\usersController;
use App\Http\Controllers\addNewAdminController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\AdminAuth\LoginController;
use App\Http\Controllers\carSearchController;
use App\Models\User;
use App\Models\Car;
use App\Models\Reservation;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ArticleController;

// ------------------- Guest Routes --------------------------------------- //
Route::get('/', function () {
    $cars = Car::take(6)->where('status', '=', 'available')->get();
    return view('home', compact('cars'));
})->name('home');

Route::get('/cars', [clientCarController::class, 'index'])->name('cars');
Route::get('/cars/search', [carSearchController::class, 'search'])->name('carSearch');

Route::get('location', function () {
    return view('location');
})->name('location');

Route::get('contact_us', function () {
    return view('contact_us');
})->name('contact_us');

Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::redirect('/admin', 'admin/login');

Route::get('/privacy_policy', function () {
    return view('Privacy_Policy');
})->name('privacy_policy');

Route::get('/terms_conditions', function () {
    return view('Terms_Conditions');
})->name('terms_conditions');

// ------------------- Public Article Routes (No Login Required) ---------------- //
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

// ------------------- Article Routes (Login Required) ------------------------- //
Route::middleware('auth')->group(function () {
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
});

// ------------------- Admin Routes ------------------------------------------- //
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', adminDashboardController::class)->name('adminDashboard');
    Route::resource('articles', ArticleController::class)->except(['index', 'show']);
    Route::resource('cars', CarController::class);

    Route::get('/users', function () {
        $admins = User::where('role', 'admin')->get();
        $clients = User::where('role', 'client')->paginate(5);
        return view('admin.users', compact('admins', 'clients'));
    })->name('users');

    Route::get('/updatePayment/{reservation}', [ReservationController::class, 'editPayment'])->name('editPayment');
    Route::put('/updatePayment/{reservation}', [ReservationController::class, 'updatePayment'])->name('updatePayment');

    Route::get('/updateReservation/{reservation}', [ReservationController::class, 'editStatus'])->name('editStatus');
    Route::put('/updateReservation/{reservation}', [ReservationController::class, 'updateStatus'])->name('updateStatus');

    Route::get('/addAdmin', [usersController::class, 'create'])->name('addAdmin');
    Route::post('/addAdmin', [addNewAdminController::class, 'register'])->name('addNewAdmin');

    Route::get('/userDetails/{user}', [usersController::class, 'show'])->name('userDetails');
});

// ------------------- Client Routes --------------------------------------- //
Route::get('/reservations/{car}', [ReservationController::class, 'create'])
    ->name('car.reservation')->middleware('auth', 'restrictAdminAccess');

Route::post('/reservations/{car}', [ReservationController::class, 'store'])
    ->name('car.reservationStore')->middleware('auth', 'restrictAdminAccess');

Route::get('/reservations', function () {
    $reservations = Reservation::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
    return view('clientReservations', compact('reservations'));
})->name('clientReservation')->middleware('auth', 'restrictAdminAccess');

Route::get('invoice/{reservation}', [invoiceController::class, 'invoice'])
    ->name('invoice')->middleware('auth', 'restrictAdminAccess');

// ------------------- Payment Routes --------------------------------------- //
Route::get('/payment', [PaymentController::class, 'index']);
Route::post('/payment/checkout', [PaymentController::class, 'checkout']);

// ------------------- Test Role Route --------------------------------------- //
Route::get('/test-role', function () {
    $user = auth()->user();

    if (!$user) {
        return "User not logged in.";
    }

    return $user->isAdmin() ? "You are an admin!" : "You are not an admin.";
});

// ------------------- Auth Routes ------------------------------------------- //
Auth::routes();
