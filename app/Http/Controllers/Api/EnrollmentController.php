<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use Exception;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    public function store(Course $course)
    {
        try {
            $result = $this->enrollmentService->enrollStudent($course, auth('api')->id());

            return response()->json([
                'message' => 'Enrolled Successfully',
                'enrollment' => $result['enrollment'],
                'group' => $result['group'],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy(Course $course)
    {
        try {
            $this->enrollmentService->leaveCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'You left the course successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function myEnrollments()
    {
        $enrollments = $this->enrollmentService->getMyEnrollments(auth('api')->id());

        return response()->json([
            'enrollments' => $enrollments
        ]);
    }

    public function courseStudents(Course $course)
    {
        try {
            $students = $this->enrollmentService->getCourseStudents($course, auth('api')->id());

            return response()->json([
                'course_id' => $course->id,
                'course_title' => $course->title,
                'students' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
