<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\InterestService;
use Exception;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    protected $interestService;

    public function __construct(InterestService $interestService)
    {
        $this->interestService = $interestService;
    }

    public function SelectStudentInterests(Request $request)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        $interests = $this->interestService->syncStudentInterests(auth('api')->user(), $validated['interest_ids']);

        return response()->json([
            'message' => 'Interests selected succefully',
            'interests' => $interests,
        ]);
    }

    public function myInterests()
    {
        $interests = $this->interestService->getStudentInterests(auth('api')->user());

        return response()->json([
            'interests' => $interests
        ]);
    }

    public function attachCourseInterests(Request $request, Course $course)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        try {
            $interests = $this->interestService->attachCourseInterests($course, $validated['interest_ids'], auth('api')->id());

            return response()->json([
                'message' => 'Course interests updated successfully',
                'interests' => $interests
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function courseInterests(Course $course)
    {
        $interests = $this->interestService->getCourseInterests($course);

        return response()->json([
            'Course title' => $course->title,
            'interests' => $interests,
        ]);
    }

    public function recommendedCourses()
    {
        $courses = $this->interestService->getRecommendedCourses(auth('api')->user());

        return response()->json([
            'recommended courses' => $courses
        ]);
    }
}
