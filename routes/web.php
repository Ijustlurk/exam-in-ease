<?php

use App\Http\Controllers\Admin\ManageClassesController;
use App\Http\Controllers\Admin\ManageSubjectController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramChair\ExamStatisticsController;
use App\Http\Controllers\ProgramChair\ManageApprovalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Instructor\ExamController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\DashboardController;



// route for the landing page 
Route::get('/', function () {
    return view('welcome');
})->name('welcom');


Route::get('/documents', function () {
    return view('teacher.documents');
})->name('teacher.documents');

Route::get('/exams', function () {
    return view('student.exams');
})->name('student.exams');

Route::get('/results', function () {
    return view('student.results');
})->name('student.results');

Route::get('/available-exams', function () {
    return view('student.available-exams');
})->name('student.available-exams');

Route::get('/auth', function () {
    return view('student.authentication');
})->name('student.authentication');

Route::get('/startexam', function () {
    return view('student.start-exam');
})->name('student.start-exam');

Route::get('/duringexam', function () {
    return view('student.during-exam');
})->name('student.during-exam');

Route::get('/duringexam2', function () {
    return view('student.during-exam2');
})->name('student.during-exam2');


Route::get('/new-exam', function () {
    return view('instructor.new-exam');
})->name('instructor.new-exam');


Route::get('/finished', function () {
    return view('teacher.partial.finishe');
})->name('teacher.partial.finishe');




// This route will handle the logic for redirecting users based on their role.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// admin routes here 
Route::
        namespace('App\Http\Controllers\Admin')
    ->prefix('admin')
    ->name('admin.')
    ->middleware('can:admin-access')
    ->group(function () {

        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');

        // Create/Edit Exam
        Route::get('/exams/create/{examId?}', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
        Route::post('/exams/{id}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');

        // Download Exam
        Route::get('/exams/{id}/download', [ExamController::class, 'download'])->name('exams.download');

        // Questions
        Route::post('/exams/{examId}/questions', [ExamController::class, 'addQuestion'])->name('exams.questions.add');
        Route::put('/exams/{examId}/questions/{itemId}', [ExamController::class, 'updateQuestion'])->name('exams.questions.update');
        Route::delete('/exams/{examId}/questions/{itemId}', [ExamController::class, 'deleteQuestion'])->name('exams.questions.delete');
        Route::post('/exams/{examId}/questions/{itemId}/duplicate', [ExamController::class, 'duplicateQuestion'])->name('exams.questions.duplicate');
        Route::post('/exams/{examId}/questions/reorder', [ExamController::class, 'reorderQuestions'])->name('exams.questions.reorder');

        // Sections
        Route::put('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'updateSection'])->name('exams.sections.update');


    });


Route::
        namespace('App\Http\Controllers\ProgramChair')->prefix('programchair')->name('programchair.')->middleware('can:programchair-access')->group(function () {

            // add routes here for ProgramChair
            Route::get('/manage-approval', [ManageApprovalController::class, 'index'])->name('manage-approval.index');
            Route::get('/manage-approval/show', [ManageApprovalController::class, 'show'])->name('manage-approval.show');
            Route::get('/exams/{id}', [MonitoringController::class, 'show'])->name('exams.show');
            Route::get('/exam-statistics', [ExamStatisticsController::class, 'index'])->name('exam-statistics.index');

        });
// Instructor Routes
Route::
        namespace('App\Http\Controllers\Instructor')
    ->prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth', 'can:instructor-access'])
    ->group(function () {
        // Exam Dashboard
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');

        // Create/Edit Exam
        Route::get('/exams/create/{examId?}', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
        Route::post('/exams/{id}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');

        // Questions
        Route::post('/exams/{examId}/questions', [ExamController::class, 'addQuestion'])->name('exams.questions.add');
        Route::put('/exams/{examId}/questions/{itemId}', [ExamController::class, 'updateQuestion'])->name('exams.questions.update');
        Route::delete('/exams/{examId}/questions/{itemId}', [ExamController::class, 'deleteQuestion'])->name('exams.questions.delete');
        Route::post('/exams/{examId}/questions/{itemId}/duplicate', [ExamController::class, 'duplicateQuestion'])->name('exams.questions.duplicate');
        Route::post('/exams/{examId}/questions/reorder', [ExamController::class, 'reorderQuestions'])->name('exams.questions.reorder');

        // Sections
        Route::put('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'updateSection'])->name('exams.sections.update');

    });


// Correct order for routes with wildcards
// Route::get('/exams/{exam}/statistics', [ExamController::class, 'statistics'])->name('exams.statistics');

// Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');


Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::get('/users/import', [UserController::class, 'import'])->name('users.import');


require __DIR__ . '/auth.php';