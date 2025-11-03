<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\HealthController;

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

// Health check - no authentication required
Route::get('/health', [HealthController::class, 'check'])->middleware('throttle:60,1');

// Public authentication routes - strict limit for security
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Authentication - moderate limits
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:30,1');
    Route::get('/me', [AuthController::class, 'me'])->middleware('throttle:60,1');
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('throttle:60,1');

    // Student classes - read operations
    Route::get('/classes', [ExamController::class, 'getClasses'])->middleware('throttle:60,1');

    // Exams - read operations
    Route::get('/exams', [ExamController::class, 'index'])->middleware('throttle:60,1');
    Route::get('/exams/completed', [ExamController::class, 'completedExams'])->middleware('throttle:60,1');
    Route::get('/exams/{examId}', [ExamController::class, 'show'])->middleware('throttle:60,1');
    Route::post('/exams/{examId}/verify-otp', [ExamController::class, 'verifyOtp'])->middleware('throttle:20,1');

    // Exam attempts - critical operations
    Route::post('/exam-attempts', [ExamController::class, 'startAttempt'])->middleware('throttle:20,1');
    Route::post('/exam-attempts/{attemptId}/submit', [ExamController::class, 'submitAttempt'])->middleware('throttle:10,1');
    Route::get('/exam-attempts/{attemptId}/results', [ExamController::class, 'getResults'])->middleware('throttle:60,1');
});
