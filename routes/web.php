<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientRegistrationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;

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
Route::get('/daftar', [PatientRegistrationController::class, 'index'])->name('patient-registration');
Route::post('/daftar', [PatientRegistrationController::class, 'store'])->name('patient-registration:store');

Route::group(['prefix' => 'admin'], function () {
    Route::get('new-service', [ServiceController::class, 'create'])->name('admin@new-service');
    Route::get('patient-list', [PatientController::class, 'list'])->name('admin@patient-list');
    Route::get(
        'patient-reschedule/{patientAppointment}',
        [PatientController::class, 'reschedule']
    )->name('admin@patient-reschedule');
    Route::put(
        'patient-reschedule',
        [PatientController::class, 'update']
    )->name('admin@patient-reschedule:put');

    Route::get('doctor-list', [DoctorController::class, 'list'])->name('admin@doctor-list');
    Route::delete(
        'doctor-list/{doctorService}',
        [DoctorController::class, 'delete']
    )->name('admin@delete-service');
});

Auth::routes();
