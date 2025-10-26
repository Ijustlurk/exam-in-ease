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
Route::get('/health', [HealthController::class, 'check']);

// Public authentication routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Student classes
    Route::get('/classes', [ExamController::class, 'getClasses']);

    // Exams
    Route::get('/exams', [ExamController::class, 'index']);
    Route::get('/exams/completed', [ExamController::class, 'completedExams']);
    Route::get('/exams/{examId}', [ExamController::class, 'show']);
    Route::post('/exams/{examId}/verify-otp', [ExamController::class, 'verifyOtp']);

    // Exam attempts
    Route::post('/exam-attempts', [ExamController::class, 'startAttempt']);
    Route::post('/exam-attempts/{attemptId}/submit', [ExamController::class, 'submitAttempt']);
    Route::get('/exam-attempts/{attemptId}/results', [ExamController::class, 'getResults']);
});
