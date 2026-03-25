<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use Exception;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAllCourses();

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function show(Course $course) 
    {
        return response()->json([
            'course' => $this->courseService->getCourseById($course)
        ]);
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $course = $this->courseService->createCourse($validated, auth('api')->id());

        return response()->json([
            'message' => 'Course Created with success',
            'course' => $course
        ], 201);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        try {
            $updatedCourse = $this->courseService->updateCourse($course, $validated, auth('api')->id());
            
            return response()->json([
                'message' => 'Course Updated Successfully',
                'course' => $updatedCourse
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy(Course $course)
    {
        try {
            $this->courseService->deleteCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Course Deleted Successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
