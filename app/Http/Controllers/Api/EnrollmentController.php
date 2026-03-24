<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
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

        $group = $this->assignStudentToGroup($studentId, $course->id);

        return response()->json([
            'message' => 'Enrolled Successfully',
            'enrollment' => $enrollment,
            'group' => $group,
        ], 201);
    }

    private function assignStudentToGroup($studentId, $courseId)
    {
        $group = Group::where('course_id', $courseId)
                    ->withCount('students')
                    ->orderBy('id', 'desc')
                    ->get()
                    ->first(fn($g) => $g->students_count < 25);
        
        if(!$group) {
            $group = Group::create([
                'course_id' => $courseId,
                'name' => 'Group ' . (Group::where('course_id', $courseId)->count() + 1)
            ]);
        }

        $group->students()->attach($studentId);

        return $group;
    }

    public function destroy(Course $course) 
    {
        $studentId = auth('api')->id();

        $enrollment = Enrollment::where('student_id', $studentId)
                                ->where('course_id', $course->id)
                                ->first();
        
        if(!$enrollment)
        {
            return response()->json([
                'message' => 'Enrollment not found'
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'message' => 'You left the course successfully'
        ]);
    }

    public function myEnrollments()
    {
        $studentId = auth('api')->id();

        $enrollments = Enrollment::with('course')
            ->where('student_id', $studentId)
            ->get();

        return response()->json([
            'enrollments' => $enrollments
        ]);
    }

    public function courseStudents(Course $course)
    {
        $teacherId = auth('api')->id();

        $searchedCourse = Course::with('enrollments.student')
                    ->where('id', $course->id)
                    ->where('teacher_id', $teacherId)
                    ->first();

        if(!$searchedCourse) {
            return response()->json([
                'message' => 'Course not found or access denied'
            ], 404);
        }

        $students = $course->enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->student->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
            ];
        });

        return response()->json([
            'course_id' => $course->id,
            'course_title' => $course->title,
            'students' => $students
        ]);
    }
}
