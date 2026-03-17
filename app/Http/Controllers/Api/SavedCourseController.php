<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SavedCourse;
use Illuminate\Http\Request;

class SavedCourseController extends Controller
{
    public function index()
    {
        $savedCourses = SavedCourse::with(['course', 'student']);

        return response()->json([
            'saved Courses' => $savedCourses
        ]);
    }

    public function store(Course $course)
    {
        $studentId = auth('api')->id();
        
        $alreadySaved = SavedCourse::where('student_id', $studentId)
                        ->where('course_id', $course->id)
                        ->first();

        if($alreadySaved) {
            return response()->json([
                'message' => 'Course already saved'
            ], 409);
        }

        $savedCourse = SavedCourse::create([
            'student_id' => $studentId,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'message' => 'Course Saved with success',
            'saved Course' => $savedCourse,
        ], 201);
    }
}
