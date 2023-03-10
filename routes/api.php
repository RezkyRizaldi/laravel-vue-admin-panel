<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('clients', ClientController::class);
Route::patch('/users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.changeRole');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
Route::delete('/users', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
Route::apiResource('users', UserController::class)->except('show');
Route::get('/appointment-status', [AppointmentController::class, 'getStatusWithCount'])->name('appointments.status');
Route::apiResource('appointments', AppointmentController::class)->only(['index', 'store']);
