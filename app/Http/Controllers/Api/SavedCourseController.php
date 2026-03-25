<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\SavedCourseService;
use Exception;
use Illuminate\Http\Request;

class SavedCourseController extends Controller
{
    protected $savedCourseService;

    public function __construct(SavedCourseService $savedCourseService)
    {
        $this->savedCourseService = $savedCourseService;
    }

    public function index()
    {
        $savedCourses = $this->savedCourseService->getMySavedCourses(auth('api')->id());

        return response()->json([
            'saved Courses' => $savedCourses
        ]);
    }

    public function store(Course $course)
    {
        try {
            $savedCourse = $this->savedCourseService->saveCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Course Saved with success',
                'saved Course' => $savedCourse,
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
            $this->savedCourseService->removeSavedCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Saved course remove successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
