<?php

use App\Http\Controllers\Admin\ConflictController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientRegistrationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/daftar', [PatientRegistrationController::class, 'index'])->name('patient-registration');
Route::post('/daftar', [PatientRegistrationController::class, 'store'])->name('patient-registration:store');

Route::group(['prefix' => 'admin', 'as' => 'admin@', 'middleware' => 'auth'], function() {
    Route::get('new-service', [ServiceController::class, 'create'])->name('new-service');

    Route::group(['prefix' => 'patient-', 'as' => 'patient-'], function() {
        Route::get('list', [PatientController::class, 'list'])->name('list');
        Route::get('done/{patientAppointment}', [PatientController::class, 'done'])->name('done');
        Route::get('cancel/{patientAppointment}', [PatientController::class, 'cancel'])->name('cancel');
        Route::get('reschedule/{patientAppointment}', [PatientController::class, 'reschedule'])->name('reschedule');
        Route::put('reschedule', [PatientController::class, 'update'])->name('reschedule:put');
    });

    Route::group(['prefix' => 'doctor-list'], function() {
        Route::get('/', [DoctorController::class, 'list'])->name('doctor-list');
        Route::delete('/{doctorService}', [DoctorController::class, 'delete'])->name('delete-service');
    });

    Route::group(['prefix' => 'conflict', 'as' => 'conflict'], function() {
        Route::get('/', [ConflictController::class, 'list']);
        Route::get('cancel/{conflict}', [ConflictController::class, 'destroy'])->name('cancel');
        Route::get('close/{serviceAppointment}',[ConflictController::class, 'closeRegistration'])->name('close');
        Route::get('nextweek/{conflict}',[ConflictController::class, 'nextWeek'])->name('nextweek');
    });
});

Auth::routes();
Route::get('logout', [LoginController::class, 'logout']);
