<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/generate-tasks', [TaskController::class, 'generateTasks']);
    Route::post('/create-task', [TaskController::class, 'store']);
    Route::delete('/delete-task/{id}', [TaskController::class, 'destroy']);
    Route::put('/update-task/{id}', [TaskController::class, 'update']);
    Route::delete('/delete-goal/{id}', [TaskController::class, 'destroyGoal']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::put('/update-goal/{id}', [TaskController::class, 'updateGoal']);

    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/download-stats', [TaskController::class, 'downloadStatistics']);
    Route::get('/statistics', [TaskController::class, 'getStatistics']);

    Route::post('/make-public/{id}', [TaskController::class, 'makePublic']);
    Route::post('/revoke-share/{id}', [TaskController::class, 'revokeShare']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->middleware('auth:sanctum');

    Route::post('/follow/{userId}', [FollowController::class, 'follow']);
    Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow']);
    Route::get('/follows', [FollowController::class, 'getFollowsAndFollowers'])->middleware('auth:sanctum');

    Route::get('/user/{userId}', [FollowController::class, 'userProfile']);
});
Route::get('/public-note/{token}', [TaskController::class, 'public']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);






