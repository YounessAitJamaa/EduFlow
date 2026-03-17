<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

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

    public function update(Request $request, Course $course)
    {
        if($course->teacher_id !== auth('api')->id()) 
        {
            return response()->json([
                'message' => 'Access Denied',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Course Updated Successfully',
            'course' => $course
        ]);
    }
}
