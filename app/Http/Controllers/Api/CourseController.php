<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher')->get();

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function show(Course $course) 
    {
        return response()->json([
            'course' => $course->load('teacher')
        ]);
    }
}
