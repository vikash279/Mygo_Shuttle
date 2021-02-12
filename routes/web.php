<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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

// Route::get('/login', function () {
//     return view('login');
// });

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

    $cleardebugbar = Artisan::call('debugbar:clear');
    echo "Debug Bar cleared<br>";
});

Route::get('/login',[UserController::class, 'index']);
Route::post('/login',[UserController::class, 'login']);
Route::get('/dashboard',[UserController::class, 'dashBoard'])->name('dashboard');
Route::get('/drivers',[UserController::class, 'driverDetails'])->name('drivers');
Route::get('/riders',[UserController::class, 'riderDetails'])->name('riders');
Route::get('/vehicles',[UserController::class, 'vehiclesDetails'])->name('vehicles');
Route::get('/driverbook',[UserController::class, 'driverBook'])->name('driverbook');
Route::get('/riderbooking',[UserController::class, 'riderBooking'])->name('riderbooking');
Route::get('/shuttlelist',[UserController::class, 'shuttleList'])->name('shuttlelist');
Route::get('/driverdata/{id}',[UserController::class, 'driverData']);
Route::get('/riderdata/{id}',[UserController::class, 'riderData']);
Route::get('/driverdldata/{id}',[UserController::class, 'driverDLData']);
Route::get('/riderrefunddata/{id}',[UserController::class, 'riderRefundData']);
Route::get('/driverwalletbalance/{id}',[UserController::class, 'driverWalletBalance']);
Route::get('/vehicledetails/{id}',[UserController::class, 'vehicleDetails']);
Route::get('/vehiclercdetails/{id}',[UserController::class, 'vehicleRCDetails']);
Route::get('/drivervehicles/{id}',[UserController::class, 'driverVehicles']);
Route::get('/vehiclebookings/{id}',[UserController::class, 'vehicleBookings']);
Route::get('/bookingdetails/{id}',[UserController::class, 'bookingDetails']);
Route::get('/riderbookingdetails/{id}',[UserController::class, 'riderBookingDetails']);
Route::get('/bookingridedetails/{id}',[UserController::class, 'bookingRideDetails']);
Route::get('/setshuttleroutes/{id}',[UserController::class, 'setShuttleRoutes']);
Route::get('/fetchshuttleroutes/{id}',[UserController::class, 'fetchShuttleRoutes']);
Route::post('/saveshuttleroutes',[UserController::class, 'saveShuttleRoutes']);
