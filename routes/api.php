<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SavedCourseController;
use App\Models\Group;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes ----------------------------------

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
// -----------------------------------------------


// Course Routes ---------------------------------

Route::middleware('auth:api')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);
});

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
});
// -----------------------------------------------

// Saved Courses ---------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function (){
    Route::get('/saved-courses', [SavedCourseController::class, 'index']);
    Route::post('/saved-courses/{course}', [SavedCourseController::class, 'store']);
    Route::delete('/saved-courses/{course}', [SavedCourseController::class, 'destroy']);
});
// -----------------------------------------------

// Interests Routes ------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/student/interests', [InterestController::class, 'myInterests']);
    Route::post('/student/interests', [InterestController::class, 'SelectStudentInterests']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/courses/{course}/interests', [InterestController::class, 'attachCourseInterests']);
    Route::get('/courses/{course}/interests', [InterestController::class, 'courseInterests']);
});

Route::middleware(['auth:api', 'role:student'])->get('/student/recommended-courses', [InterestController::class, 'recommendedCourses']);

// -------------------------------------------------

// Enrollment Routes -----------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'myEnrollments']);
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store']);
    Route::delete('/course/{course}/leav', [EnrollmentController::class, 'destroy']);
});

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('/courses/{course}/students', [EnrollmentController::class, 'courseStudents']);
});

// --------------------------------------------------------


// Group Routes ----------------------------------------------------

Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('/groups/{course}', [GroupController::class, 'ListGroups']);
    Route::get('/groups/{group}/students', [GroupController::class, 'groupParticipants']);
});

// -----------------------------------------------------------------


// Payment Routes --------------------------------------------------

Route::middleware(['auth:api', 'role:student'])->group(function (){
    Route::get('/courses/{course}/pay', [PaymentController::class, 'createCheckoutSession']);
});

Route::get('/payment/success', [PaymentController::class, 'success']);
// ------------------------------------------------------------------