<?php

use App\Http\Controllers\Admin\ManageClassesController;
use App\Http\Controllers\Admin\ManageSubjectController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Instructor\ExamStatisticsController;
use App\Http\Controllers\ProgramChair\ManageApprovalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExamStatisticsController as AdminExamStatisticsController;
use App\Http\Controllers\Instructor\ExamController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\ProgramChair\ExamStatisticsController as ProgramChairExamStatisticsController;
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
        namespace('App\Http\Controllers\Admin')->prefix('admin')->name('admin.')->middleware('can:admin-access')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{userId}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{userId}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{userId}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::get('/users/template/{role}', [UserController::class, 'downloadTemplate'])->name('users.download-template');
            Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
            Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
            Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])
                ->name('users.reset-password');

            Route::get('admin/{exam}', [MonitoringController::class, 'show'])->name('monitor.show');


            Route::controller(ManageClassesController::class)->group(function () {
                // Basic CRUD
                Route::get('/manage-classes', 'index')->name('manage-classes.index');
                Route::post('/manage-classes', 'store')->name('manage-classes.store');
                Route::get('/manage-classes/{id}', 'show')->name('manage-classes.show');
                Route::put('/manage-classes/{id}', 'update')->name('manage-classes.update');
                Route::delete('/manage-classes/{id}', 'destroy')->name('manage-classes.destroy');

                // Archive/Unarchive
                Route::post('/manage-classes/{id}/archive', 'archive')->name('manage-classes.archive');
                Route::post('/manage-classes/{id}/unarchive', 'unarchive')->name('manage-classes.unarchive');

                // Student Management
                Route::get('/manage-classes/{id}/students', 'manageStudents')->name('manage-classes.students');
                Route::get('/manage-classes/{id}/available-students', 'getAvailableStudents')->name('manage-classes.available-students');
                Route::get('/manage-classes/{id}/class-members', 'getClassMembers')->name('manage-classes.class-members');
                Route::post('/manage-classes/{id}/add-students', 'addStudents')->name('manage-classes.add-students');
                Route::delete('/manage-classes/{classId}/remove-student/{studentId}', 'removeStudent')->name('manage-classes.remove-student');

                // Copy Students
                Route::get('/manage-classes/{id}/other-classes', 'getOtherClasses')->name('manage-classes.other-classes');
                Route::post('/manage-classes/{id}/copy-students/{sourceClassId}', 'copyStudentsFromClass')->name('manage-classes.copy-students');
            });

            Route::get('/manage-subject', [ManageSubjectController::class, 'index'])
                ->name('manage-subject.index');
            Route::post('/manage-subject', [ManageSubjectController::class, 'store'])
                ->name('manage-subject.store');
            Route::get('/manage-subject/{id}', [ManageSubjectController::class, 'show'])
                ->name('manage-subject.show');
            Route::put('/manage-subject/{id}', [ManageSubjectController::class, 'update'])
                ->name('manage-subject.update');
            Route::delete('/manage-subject/{id}', [ManageSubjectController::class, 'destroy'])
                ->name('manage-subject.destroy');

  

            Route::get('/exam-statistics', [AdminExamStatisticsController::class, 'index'])
                ->name('exam-statistics.index');
            Route::get('/exam-statistics/{id}/show', [AdminExamStatisticsController::class, 'show'])
                ->name('exam-statistics.show');
            Route::get('/exam-statistics/{id}/stats', [AdminExamStatisticsController::class, 'stats'])
                ->name('exam-statistics.stats');
            Route::post('/exam-statistics/{id}/approve', [AdminExamStatisticsController::class, 'approve'])
                ->name('exam-statistics.approve');



        });




Route::
        namespace('App\Http\Controllers\ProgramChair')->prefix('programchair')->name('programchair.')->middleware('can:programchair-access')->group(function () {
            Route::prefix('manage-approval')->name('manage-approval.')->group(function () {

                // List all exams for approval
                Route::get('/programchair', [ManageApprovalController::class, 'index'])
                    ->name('index');
                // Details route must come before the show route to avoid {exam} catching "details"
                Route::get('/programchair/{exam}/details', [ManageApprovalController::class, 'getDetails'])
                    ->name('details');
                Route::get('/programchair/{exam}', [ManageApprovalController::class, 'show'])
                    ->name('show');
                Route::post('/{exam}/approve', [ManageApprovalController::class, 'approve'])
                    ->name('approve');
                Route::post('/programchair/{exam}/revise', [ManageApprovalController::class, 'revise'])
                    ->name('revise');
                Route::post('/programchair/{exam}/rescind', [ManageApprovalController::class, 'rescind'])
                    ->name('rescind');
            });
            Route::get('/exam-statistics', [ProgramChairExamStatisticsController::class, 'index'])->name('exam-statistics.index');


        });

// Instructor Routes
// Add these routes to your existing instructor routes group

Route::
        namespace('App\Http\Controllers\Instructor')
    ->prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth', 'can:instructor-access'])
    ->group(function () {
        // Exam Dashboard
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::delete('/exams/{examId}', [ExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');

        // Create/Edit Exam
        Route::get('/exams/create/{examId?}', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
        Route::post('/exams/{id}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');

        // Questions
        Route::get('/exams/{examId}/questions/{itemId}', [ExamController::class, 'getQuestion'])->name('exams.questions.get');
        Route::post('/exams/{examId}/questions', [ExamController::class, 'addQuestion'])->name('exams.questions.add');
        Route::put('/exams/{examId}/questions/{itemId}', [ExamController::class, 'updateQuestion'])->name('exams.questions.update');
        Route::delete('/exams/{examId}/questions/{itemId}', [ExamController::class, 'deleteQuestion'])->name('exams.questions.delete');
        Route::post('/exams/{examId}/questions/{itemId}/duplicate', [ExamController::class, 'duplicateQuestion'])->name('exams.questions.duplicate');
        Route::post('/exams/{examId}/questions/reorder', [ExamController::class, 'reorderQuestions'])->name('exams.questions.reorder');
        Route::post('/exams/{examId}/questions/reorder-drag', [ExamController::class, 'reorderQuestionsByDrag'])->name('exams.questions.reorder.drag');

        // Sections
        Route::post('/exams/{examId}/sections', [ExamController::class, 'addSection'])->name('exams.sections.add');
        Route::put('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'updateSection'])->name('exams.sections.update');
        Route::delete('/exams/{examId}/sections/{sectionId}', [ExamController::class, 'deleteSection'])->name('exams.sections.delete');
        Route::post('/exams/{examId}/sections/{sectionId}/duplicate', [ExamController::class, 'duplicateSection'])->name('exams.sections.duplicate');
        Route::post('/exams/{examId}/sections/reorder', [ExamController::class, 'reorderSections'])->name('exams.sections.reorder');
        
        // Preview and Download
        Route::get('/exams/{examId}/preview', [ExamController::class, 'preview'])->name('exams.preview');
        Route::get('/exams/{examId}/download/{format}', [ExamController::class, 'download'])->name('exams.download');
        
        Route::get('/api/exams/{id}/details', [ExamController::class, 'getExamDetails'])->name('exams.details');
        Route::get('/api/teachers/search', [ExamController::class, 'searchTeachers'])->name('teachers.search');
        Route::post('/exams/{examId}/collaborators', [ExamController::class, 'addCollaborators'])->name('exams.collaborators.add');
        Route::delete('/exams/{examId}/collaborators/{teacherId}', [ExamController::class, 'removeCollaborator'])->name('exams.collaborators.remove');
        Route::get('/exams/{examId}/collaborators', [ExamController::class, 'getCollaborators'])->name('exams.collaborators.get');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Instructor\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [\App\Http\Controllers\Instructor\NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Instructor\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/notifications/unread/count', [\App\Http\Controllers\Instructor\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

        // Get classes by subject (for the create exam modal)
        Route::get('/api/classes', [ExamController::class, 'getClasses'])->name('classes.get');

        // Exam Statistics
        Route::get('/exams-statistics', [ExamStatisticsController::class, 'index'])->name('exam-statistics.index');
        Route::get('/exams-statistics/{id}', [ExamStatisticsController::class, 'show'])->name('exam-statistics.show');
        Route::get('/exams-statistics/{id}/filter', [ExamStatisticsController::class, 'getFilteredStats'])->name('exam-statistics.filter');
        Route::get('/exams-statistics/{id}/questions', [ExamStatisticsController::class, 'getQuestionStats'])->name('exam-statistics.questions');
        Route::get('/instructor/exam-statistics/{exam}', [App\Http\Controllers\Instructor\ExamStatisticsController::class, 'show'])->name('instructor.exam-statistics.show');

    });


// Correct order for routes with wildcards
// Route::get('/exams/{exam}/statistics', [ExamController::class, 'statistics'])->name('exams.statistics');

// Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');


Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::get('/users/import', [UserController::class, 'import'])->name('users.import');


require __DIR__ . '/auth.php';