<?php

use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Api\ClockInOutController as ApiClockInOutController;
use App\Http\Controllers\Api\LeaveController as ApiLeaveController;
use App\Http\Controllers\Api\DirectoryController as ApiDirectoryController;
use App\Http\Controllers\Api\TeamController as ApiTeamController;
use App\Http\Controllers\Api\AnnouncementController as ApiAnnouncementController;
use App\Http\Controllers\Api\RequestStatusController as ApiRequestStatusController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('leave')->group(function() {
    Route::get('/', [ApiLeaveController::class, 'index']);
    Route::get('/{id}', [ApiLeaveController::class, 'show']);
    Route::post('/', [ApiLeaveController::class, 'store']);
    Route::post('/{id}/edit', [ApiLeaveController::class, 'update']);
    Route::post('/{id}/delete', [ApiLeaveController::class, 'destroy']);
});

Route::prefix('clock-in-out')->group(function() {
    Route::get('/', [ApiClockInOutController::class, 'index']);
    Route::post('/', [ApiClockInOutController::class, 'store']);
});

Route::get('directory', [ApiDirectoryController::class, 'index']);
Route::get('team', [ApiTeamController::class, 'index']);
Route::get('announcements', [ApiAnnouncementController::class, 'index']);

Route::prefix('request-status')->group(function() {
    Route::get('/', [ApiRequestStatusController::class, 'loadRecords']);
    Route::post('/first-time', [ApiRequestStatusController::class, 'isFirstTime']);
    Route::post('/make-seen', [ApiRequestStatusController::class, 'makeSeen']);
    Route::post('/send-message', [ApiRequestStatusController::class, 'sendMessage']);
    Route::post('/preview-attachments', [ApiRequestStatusController::class, 'updatedAttachments']);
    Route::get('/download/{messageId}/{attachmentId}', [ApiRequestStatusController::class, 'download']);
});
