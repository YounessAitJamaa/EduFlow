<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SavedCourseController;
use App\Http\Controllers\Api\StatisticsController;
use App\Models\Group;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes ----------------------------------

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
// -----------------------------------------------


// Course Routes ---------------------------------

Route::middleware('auth:api')->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
});

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/teacher/statistics', [StatisticsController::class, 'index'])->name('teacher.statistics');
});
// -----------------------------------------------

// Saved Courses ---------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/saved-courses', [SavedCourseController::class, 'index'])->name('saved-courses.index');
    Route::post('/saved-courses/{course}', [SavedCourseController::class, 'store'])->name('saved-courses.store');
    Route::delete('/saved-courses/{course}', [SavedCourseController::class, 'destroy'])->name('saved-courses.destroy');
});
// -----------------------------------------------

// Interests Routes ------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/student/interests', [InterestController::class, 'myInterests'])->name('student.interests.index');
    Route::post('/student/interests', [InterestController::class, 'SelectStudentInterests'])->name('student.interests.store');
    Route::get('/interests', [InterestController::class, 'index'])->name('interests.all');
});

Route::middleware('auth:api')->group(function () {
    Route::post('/courses/{course}/interests', [InterestController::class, 'attachCourseInterests'])->name('courses.interests.attach');
    Route::get('/courses/{course}/interests', [InterestController::class, 'courseInterests'])->name('courses.interests.index');
});

Route::middleware(['auth:api', 'role:student'])->get('/student/recommended-courses', [InterestController::class, 'recommendedCourses'])->name('student.recommended-courses');

// -------------------------------------------------

// Enrollment Routes -----------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'myEnrollments'])->name('enrollments.index');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('/courses/{course}/leave', [EnrollmentController::class, 'destroy'])->name('enrollments.leave');
});

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('/courses/{course}/students', [EnrollmentController::class, 'courseStudents'])->name('courses.students.index');
});

// --------------------------------------------------------


// Group Routes ----------------------------------------------------

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('/groups/{course}', [GroupController::class, 'ListGroups'])->name('groups.index');
    Route::get('/groups/{group}/students', [GroupController::class, 'groupParticipants'])->name('groups.students.index');
});

// -----------------------------------------------------------------


// Payment Routes --------------------------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::post('/payment/checkout/{course}', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout');
});

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
// ------------------------------------------------------------------