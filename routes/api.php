<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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

Route::post('new_register', [RegisterController::class, 'newRegister']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('verifyOTP', [RegisterController::class, 'verifyOTP']);
Route::get('cities', [RegisterController::class, 'fetchCities']);
Route::post('submitCity', [RegisterController::class, 'submitCity']);
Route::post('updateUserProfile', [RegisterController::class, 'updateUserProfile']);
Route::post('dldetails', [RegisterController::class, 'UploadDrivingLicence']);
Route::post('rcdetails', [RegisterController::class, 'uploadRCDetails']);
Route::post('searchride', [RegisterController::class, 'searchRide']);
Route::post('requestride', [RegisterController::class, 'requestRide']);
Route::post('walletbalance', [RegisterController::class, 'userWalletBalance']);
Route::post('ridebookinghistory', [RegisterController::class, 'userRideBookingHistory']);
Route::post('ridedetails', [RegisterController::class, 'fetchRideDetails']);
Route::post('hirevehicle', [RegisterController::class, 'hireVehicle']);
Route::post('uservehicleregistration', [RegisterController::class, 'userVehicleRegistration']);
Route::post('getuserprofile', [RegisterController::class, 'getUserProfile']);
Route::post('pickuppoint', [RegisterController::class, 'pickupPoint']);
Route::post('fetchuserpromocode', [RegisterController::class, 'fetchUserPromoCode']);
Route::post('cancelride', [RegisterController::class, 'cancelRide']);
Route::post('userdefaultpaymentmethod', [RegisterController::class, 'userDefaultPaymentMethod']);
Route::post('ratedriver', [RegisterController::class, 'rateDriver']);
Route::post('validatetransaction', [RegisterController::class, 'validateTransaction']);
Route::post('tipdriver', [RegisterController::class, 'tipDriver']);
Route::post('addwalletamount', [RegisterController::class, 'addWalletAmount']);
Route::post('userrefund', [RegisterController::class, 'userRefund']);
Route::post('hirevehiclepayment', [RegisterController::class, 'hireVehiclePayment']);
Route::post('sendsmsbyapi', [RegisterController::class, 'sendSMSByAPI']);
Route::post('googlelogin', [RegisterController::class, 'googleLogin']);
Route::post('getreviewlist', [RegisterController::class, 'getReviewList']);
Route::post('documentdetails', [RegisterController::class, 'documentDetails']);
Route::post('vehicledetails', [RegisterController::class, 'vehicleDetails']);
Route::post('vehiclelist', [RegisterController::class, 'vehicleList']);
Route::post('documentlist', [RegisterController::class, 'documentList']);
Route::post('ridebookinghistorylist', [RegisterController::class, 'rideBookingHistoryList']);
Route::post('pushnotification', [RegisterController::class, 'pushNotification']);
Route::post('fetchnotification', [RegisterController::class, 'fetchNotification']);
Route::post('paymenthistory', [RegisterController::class, 'paymentHistory']);
Route::post('loginstatus', [RegisterController::class, 'loginStatus']);
Route::post('fetchloginstatus', [RegisterController::class, 'fetchLoginStatus']);
Route::post('updateboardingstatus', [RegisterController::class, 'updateBoardingStatus']);
Route::post('userlastbookingdetails', [RegisterController::class, 'userLastBookingDetails']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

