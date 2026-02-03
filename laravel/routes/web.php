<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::post('/account', [AccountController::class, 'update'])->name('account.update');

    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/homework', [StudentController::class, 'homework'])->name('homework');
        Route::get('/progress', [StudentController::class, 'progress'])->name('progress');
        Route::post('/reviews', [StudentController::class, 'storeReview'])->name('reviews.store');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::post('/homework', [TeacherController::class, 'storeHomework'])->name('homework.store');
        Route::post('/grades', [TeacherController::class, 'storeGrades'])->name('grades.store');
        Route::post('/attendance', [TeacherController::class, 'storeAttendance'])->name('attendance.store');
        Route::post('/comment', [TeacherController::class, 'storeComment'])->name('comment.store');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
        Route::post('/courses/{courseId}/delete', [AdminController::class, 'deleteCourse'])->name('courses.delete');
        Route::post('/groups', [AdminController::class, 'storeGroup'])->name('groups.store');
        Route::post('/groups/{groupId}/delete', [AdminController::class, 'deleteGroup'])->name('groups.delete');
        Route::post('/groups/{groupId}/teacher', [AdminController::class, 'assignTeacher'])->name('groups.teacher');
        Route::post('/groups/assign-student', [AdminController::class, 'assignStudent'])->name('groups.students');
        Route::post('/schedule', [AdminController::class, 'storeSchedule'])->name('schedule.store');
        Route::post('/schedule/{scheduleId}/delete', [AdminController::class, 'deleteSchedule'])->name('schedule.delete');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{userId}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
    });
});
