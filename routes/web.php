<?php

use App\Http\Controllers\Admin\ManageClassesController;
use App\Http\Controllers\Admin\ManageSubjectController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Instructor\ExamStatisticsController;
use App\Http\Controllers\ProgramChair\ManageApprovalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExamStatisticsController as AdminExamStatisticsController;
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



// admin routes here 
Route::
        namespace('App\Http\Controllers\Admin')->prefix('admin')->name('admin.')->middleware('can:admin-access')->group(function () {
            // User Management - rate limited
            Route::get('/users', [UserController::class, 'index'])->middleware('throttle:60,1')->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->middleware('throttle:20,1')->name('users.store');
            Route::get('/users/{userId}/edit', [UserController::class, 'edit'])->middleware('throttle:60,1')->name('users.edit');
            Route::put('/users/{userId}', [UserController::class, 'update'])->middleware('throttle:30,1')->name('users.update');
            Route::delete('/users/{userId}', [UserController::class, 'destroy'])->middleware('throttle:10,1')->name('users.destroy');
            Route::get('/users/template/{role}', [UserController::class, 'downloadTemplate'])->middleware('throttle:10,1')->name('users.download-template');
            Route::post('/users/import', [UserController::class, 'import'])->middleware('throttle:5,1')->name('users.import');

            // Monitoring - rate limited
            Route::get('admin/{exam}', [MonitoringController::class, 'show'])->middleware('throttle:60,1')->name('monitor.show');


            Route::controller(ManageClassesController::class)->group(function () {
                // Basic CRUD - rate limited
                Route::get('/manage-classes', 'index')->middleware('throttle:60,1')->name('manage-classes.index');
                Route::post('/manage-classes', 'store')->middleware('throttle:20,1')->name('manage-classes.store');
                Route::get('/manage-classes/{id}', 'show')->middleware('throttle:60,1')->name('manage-classes.show');
                Route::put('/manage-classes/{id}', 'update')->middleware('throttle:30,1')->name('manage-classes.update');
                Route::delete('/manage-classes/{id}', 'destroy')->middleware('throttle:10,1')->name('manage-classes.destroy');

                // Archive/Unarchive - rate limited
                Route::post('/manage-classes/{id}/archive', 'archive')->middleware('throttle:20,1')->name('manage-classes.archive');
                Route::post('/manage-classes/{id}/unarchive', 'unarchive')->middleware('throttle:20,1')->name('manage-classes.unarchive');

                // Student Management - rate limited
                Route::get('/manage-classes/{id}/students', 'manageStudents')->middleware('throttle:60,1')->name('manage-classes.students');
                Route::get('/manage-classes/{id}/available-students', 'getAvailableStudents')->middleware('throttle:60,1')->name('manage-classes.available-students');
                Route::get('/manage-classes/{id}/class-members', 'getClassMembers')->middleware('throttle:60,1')->name('manage-classes.class-members');
                Route::post('/manage-classes/{id}/add-students', 'addStudents')->middleware('throttle:30,1')->name('manage-classes.add-students');
                Route::delete('/manage-classes/{classId}/remove-student/{studentId}', 'removeStudent')->middleware('throttle:30,1')->name('manage-classes.remove-student');

                // Copy Students - rate limited
                Route::get('/manage-classes/{id}/other-classes', 'getOtherClasses')->middleware('throttle:60,1')->name('manage-classes.other-classes');
                Route::post('/manage-classes/{id}/copy-students/{sourceClassId}', 'copyStudentsFromClass')->middleware('throttle:20,1')->name('manage-classes.copy-students');
            });

            // Subject Management - rate limited
            Route::get('/manage-subject', [ManageSubjectController::class, 'index'])->middleware('throttle:60,1')
                ->name('manage-subject.index');
            Route::post('/manage-subject', [ManageSubjectController::class, 'store'])->middleware('throttle:20,1')
                ->name('manage-subject.store');
            Route::get('/manage-subject/{id}', [ManageSubjectController::class, 'show'])->middleware('throttle:60,1')
                ->name('manage-subject.show');
            Route::put('/manage-subject/{id}', [ManageSubjectController::class, 'update'])->middleware('throttle:30,1')
                ->name('manage-subject.update');
            Route::delete('/manage-subject/{id}', [ManageSubjectController::class, 'destroy'])->middleware('throttle:10,1')
                ->name('manage-subject.destroy');

  

            // Exams - rate limited
            Route::get('/exams', [AdminExamStatisticsController::class, 'index'])->middleware('throttle:60,1')
                ->name('exams.index');
            Route::get('/exams/{id}/show', [AdminExamStatisticsController::class, 'show'])->middleware('throttle:60,1')
                ->name('exams.show');
            Route::get('/exams/{id}/stats', [AdminExamStatisticsController::class, 'stats'])->middleware('throttle:60,1')
                ->name('exams.stats');



        });




Route::
        namespace('App\Http\Controllers\ProgramChair')->prefix('programchair')->name('programchair.')->middleware('can:programchair-access')->group(function () {
            Route::prefix('manage-approval')->name('manage-approval.')->group(function () {

                // List all exams for approval - rate limited
                Route::get('/programchair', [ManageApprovalController::class, 'index'])->middleware('throttle:60,1')
                    ->name('index');
                // Details route must come before the show route to avoid {exam} catching "details" - rate limited
                Route::get('/programchair/{exam}/details', [ManageApprovalController::class, 'getDetails'])->middleware('throttle:60,1')
                    ->name('details');
                Route::get('/programchair/{exam}', [ManageApprovalController::class, 'show'])->middleware('throttle:60,1')
                    ->name('show');
                Route::post('/{exam}/approve', [ManageApprovalController::class, 'approve'])->middleware('throttle:20,1')
                    ->name('approve');
                Route::post('/programchair/{exam}/revise', [ManageApprovalController::class, 'revise'])->middleware('throttle:20,1')
                    ->name('revise');
                Route::post('/{exam}/rescind', [ManageApprovalController::class, 'rescind'])->middleware('throttle:20,1')
                    ->name('rescind');
            });

        });

// Instructor Routes
// Add these routes to your existing instructor routes group

Route::
        namespace('App\Http\Controllers\Instructor')
    ->prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth', 'can:instructor-access'])
    ->group(function () {
        // Exam Dashboard - rate limited
        Route::get('/exams', [ExamController::class, 'index'])->middleware('throttle:60,1')->name('exams.index');
        Route::delete('/exams/{examId}', [ExamController::class, 'destroy'])->middleware('throttle:10,1')->name('exams.destroy');
        Route::get('/exams/{id}', [ExamController::class, 'show'])->middleware('throttle:60,1')->name('exams.show');

        // Create/Edit Exam - rate limited
        Route::get('/exams/create/{examId?}', [ExamController::class, 'create'])->middleware('throttle:60,1')->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->middleware('throttle:20,1')->name('exams.store');
        Route::put('/exams/{id}', [ExamController::class, 'update'])->middleware('throttle:30,1')->name('exams.update');
        Route::post('/exams/{id}/duplicate', [ExamController::class, 'duplicate'])->middleware('throttle:10,1')->name('exams.duplicate');

        // Questions - rate limited
        Route::get('/exams/{examId}/questions/{itemId}', [ExamController::class, 'getQuestion'])->middleware('throttle:60,1')->name('exams.questions.get');
        Route::post('/exams/{examId}/questions/instant', [ExamController::class, 'createQuestionInstantly'])->middleware('throttle:30,1')->name('exams.questions.instant');
        Route::post('/exams/{examId}/questions', [ExamController::class, 'addQuestion'])->middleware('throttle:30,1')->name('exams.questions.add');
        Route::put('/exams/{examId}/questions/{itemId}', [ExamController::class, 'updateQuestion'])->middleware('throttle:30,1')->name('exams.questions.update');
        Route::delete('/exams/{examId}/questions/{itemId}', [ExamController::class, 'deleteQuestion'])->middleware('throttle:20,1')->name('exams.questions.delete');
        Route::post('/exams/{examId}/questions/{itemId}/duplicate', [ExamController::class, 'duplicateQuestion'])->middleware('throttle:20,1')->name('exams.questions.duplicate');
        Route::post('/exams/{examId}/questions/reorder', [ExamController::class, 'reorderQuestions'])->middleware('throttle:30,1')->name('exams.questions.reorder');
        Route::post('/exams/{examId}/questions/reorder-drag', [ExamController::class, 'reorderQuestionsByDrag'])->middleware('throttle:30,1')->name('exams.questions.reorder.drag');

        // Sections - rate limited
        Route::post('/exams/{examId}/sections', [ExamController::class, 'addSection'])->middleware('throttle:30,1')->name('exams.sections.add');
        Route::put('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'updateSection'])->middleware('throttle:30,1')->name('exams.sections.update');
        Route::delete('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'deleteSection'])->middleware('throttle:20,1')->name('exams.sections.delete');
        Route::post('/exams/{examId}/sections/{sectionId}/duplicate', [ExamController::class, 'duplicateSection'])->middleware('throttle:20,1')->name('exams.sections.duplicate');
        Route::post('/exams/{examId}/sections/reorder', [ExamController::class, 'reorderSections'])->middleware('throttle:30,1')->name('exams.sections.reorder');
        
        // Preview and Download - rate limited
        Route::get('/exams/{examId}/preview', [ExamController::class, 'preview'])->middleware('throttle:20,1')->name('exams.preview');
        Route::get('/exams/{examId}/download/{format}', [ExamController::class, 'download'])->middleware('throttle:10,1')->name('exams.download');
        
        // Comments - rate limited
        Route::get('/exams/{examId}/questions/{itemId}/comments', [ExamController::class, 'getComments'])->middleware('throttle:60,1')->name('exams.comments.get');
        Route::post('/exams/{examId}/questions/{itemId}/comments', [ExamController::class, 'addComment'])->middleware('throttle:30,1')->name('exams.comments.add');
        Route::delete('/comments/{commentId}', [ExamController::class, 'deleteComment'])->middleware('throttle:20,1')->name('comments.delete');
        Route::put('/comments/{commentId}/resolve', [ExamController::class, 'toggleResolveComment'])->middleware('throttle:30,1')->name('comments.resolve');
        
        // API Routes - rate limited
        Route::get('/api/exams/{id}/details', [ExamController::class, 'getExamDetails'])->middleware('throttle:60,1')->name('exams.details');
        Route::get('/api/teachers/search', [ExamController::class, 'searchTeachers'])->middleware('throttle:60,1')->name('teachers.search');
        Route::post('/exams/{examId}/collaborators', [ExamController::class, 'addCollaborators'])->middleware('throttle:20,1')->name('exams.collaborators.add');
        Route::delete('/exams/{examId}/collaborators/{teacherId}', [ExamController::class, 'removeCollaborator'])->middleware('throttle:20,1')->name('exams.collaborators.remove');
        Route::get('/exams/{examId}/collaborators', [ExamController::class, 'getCollaborators'])->middleware('throttle:60,1')->name('exams.collaborators.get');

        // Notifications - rate limited
        Route::get('/notifications', [\App\Http\Controllers\Instructor\NotificationController::class, 'index'])->middleware('throttle:60,1')->name('notifications.index');
        Route::get('/notifications/{id}', [\App\Http\Controllers\Instructor\NotificationController::class, 'show'])->middleware('throttle:60,1')->name('notifications.show');
        Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAsRead'])->middleware('throttle:60,1')->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAllAsRead'])->middleware('throttle:30,1')->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Instructor\NotificationController::class, 'destroy'])->middleware('throttle:20,1')->name('notifications.destroy');
        Route::get('/notifications/unread/count', [\App\Http\Controllers\Instructor\NotificationController::class, 'getUnreadCount'])->middleware('throttle:60,1')->name('notifications.unread-count');

        // Get classes by subject (for the create exam modal) - rate limited
        Route::get('/api/classes', [ExamController::class, 'getClasses'])->middleware('throttle:60,1')->name('classes.get');

        // Exam Statistics
        Route::get('/exams-statistics', [ExamStatisticsController::class, 'index'])->name('exam-statistics.index');
        Route::get('/exams-statistics/{id}', [ExamStatisticsController::class, 'show'])->name('exam-statistics.show');
        Route::get('/exams-statistics/{id}/filter', [ExamStatisticsController::class, 'getFilteredStats'])
            ->middleware('throttle:60,1')
            ->name('exam-statistics.filter');
        Route::get('/exams-statistics/{id}/questions', [ExamStatisticsController::class, 'getQuestionStats'])
            ->middleware('throttle:60,1')
            ->name('exam-statistics.questions');
        Route::get('/exams-statistics/{id}/individual', [ExamStatisticsController::class, 'getIndividualStats'])
            ->middleware('throttle:60,1')
            ->name('exam-statistics.individual');
        Route::get('/exams-statistics/{id}/score-distribution', [ExamStatisticsController::class, 'getScoreDistribution'])
            ->middleware('throttle:60,1')
            ->name('exam-statistics.score-distribution');
        Route::post('/exams-statistics/{id}/answer/{answerId}/override', [ExamStatisticsController::class, 'overrideAnswer'])
            ->middleware('throttle:30,1')
            ->name('exam-statistics.override-answer');
        Route::delete('/exams-statistics/{id}/attempt/{attemptId}', [ExamStatisticsController::class, 'deleteAttempt'])
            ->middleware('throttle:20,1')
            ->name('exam-statistics.delete-attempt');
        Route::get('/exams-statistics/{id}/download-scores', [ExamStatisticsController::class, 'downloadScores'])
            ->middleware('throttle:10,1')
            ->name('exam-statistics.download-scores');
        Route::get('/exams-statistics/{id}/download', [ExamStatisticsController::class, 'downloadExcel'])
            ->middleware('throttle:10,1')
            ->name('exam-statistics.download');
        Route::get('/instructor/exam-statistics/{exam}', [App\Http\Controllers\Instructor\ExamStatisticsController::class, 'show'])->name('instructor.exam-statistics.show');

    });


// Correct order for routes with wildcards
// Route::get('/exams/{exam}/statistics', [ExamController::class, 'statistics'])->name('exams.statistics');

// Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');


Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::get('/users/import', [UserController::class, 'import'])->name('users.import');


require __DIR__ . '/auth.php';