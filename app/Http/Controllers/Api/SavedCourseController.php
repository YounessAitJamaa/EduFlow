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
        $studentId = auth('api')->id();
        $savedCourses = SavedCourse::with(['course', 'student'])
                            ->where('student_id', $studentId)
                            ->get();

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

    public function destroy(Course $course)
    {   
        $studentId = auth('api')->id();

        $savedCourse = SavedCourse::where('student_id', $studentId)
                        ->where('course_id', $course->id)
                        ->first();
        
        if(!$savedCourse) {
            return response()->json([
                'message' => 'Saved Course not found'
            ], 409);
        }

        $savedCourse->delete();

        return response()->json([
            'message' => 'Saved course remove successfully'
        ]);
    }
}
