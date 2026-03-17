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

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'teacher_id' => auth('api')->user()->id,
        ]);

        return response()->json([
            'message' => 'Course Created with success',
            'course' => $course
        ], 201);
    }
}
