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

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
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

    Route::get('conflict', [ConflictController::class, 'list'])->name('admin@conflict');
    Route::get('conflict/cancel/{conflict}', [ConflictController::class, 'destroy'])->name('admin@conflict-cancel');
    Route::get(
        'conflict/close/{serviceAppointment}',
        [ConflictController::class, 'closeRegistration']
    )->name('admin@conflict-close');
    Route::get(
        'conflict/nextweek/{conflict}',
        [ConflictController::class, 'nextWeek']
    )->name('admin@conflict-nextweek');
});

Auth::routes();
Route::get('logout', [LoginController::class, 'logout']);