<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Course $course)
    {
        $studentId = auth('api')->id();

        $existingEnrollment = Enrollment::where('student_id', $studentId)
                                ->where('course_id', $course->id)
                                ->first();

        if($existingEnrollment)
        {
            return response()->json([
                'message' => 'You are already Enrolled in this course'
            ], 409);
        }

        $enrollment = Enrollment::create([
            'student_id' => $studentId,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Enrolled Successfully',
            'enrollment' => $enrollment,
        ], 201);
    }
}
