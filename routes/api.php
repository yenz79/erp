<?php

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

// POS
Route::apiResource('pos', App\Http\Controllers\POSController::class);
Route::get('pos/print/{id}', [App\Http\Controllers\POSController::class, 'printReceipt']);
Route::get('pos/export', [App\Http\Controllers\POSController::class, 'exportSales']);
Route::post('pos/import', [App\Http\Controllers\POSController::class, 'importSales']);
Route::post('pos/backup', [App\Http\Controllers\POSController::class, 'backupAuto']);
Route::post('pos/absensi-wajah', [App\Http\Controllers\POSController::class, 'storeFaceAttendance']);
Route::post('pos/notify/{id}', [App\Http\Controllers\POSController::class, 'notifySale']);

// Absensi
Route::apiResource('absensi', App\Http\Controllers\AbsensiController::class);

// Gudang (Stock Mutation)
Route::apiResource('gudang', App\Http\Controllers\GudangController::class);

// Role
Route::apiResource('role', App\Http\Controllers\RoleController::class);
Route::post('role/{id}/assign-user', [App\Http\Controllers\RoleController::class, 'assignUser']);

// Backup
Route::get('backup', [App\Http\Controllers\BackupController::class, 'index']);
Route::post('backup', [App\Http\Controllers\BackupController::class, 'store']);
Route::get('backup/{filename}', [App\Http\Controllers\BackupController::class, 'show']);
Route::delete('backup/{filename}', [App\Http\Controllers\BackupController::class, 'destroy']);

// Notification
Route::apiResource('notification', App\Http\Controllers\NotificationController::class);

// Dashboard
Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
