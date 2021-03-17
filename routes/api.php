<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DisplayOrderController;
use App\Http\Controllers\Api\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api.token'], function () {
    Route::post('/service', [ServiceController::class, 'store']);
    Route::post('/service/{id}', [ServiceController::class, 'update']);
    Route::delete('/conflict/{doctorWorktime}', [ServiceController::class, 'destroyConflict']);
    Route::post('/doctor/{doctorService}', [ApiController::class, 'doctor']);
    Route::post('/close/{doctorWorktime}', [ApiController::class, 'close']);

    Route::post('/reorderDoctorService/{service}', [DisplayOrderController::class, 'reorderDoctorService']);
    Route::post('/reorderService', [DisplayOrderController::class, 'reorderService']);
});
