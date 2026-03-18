<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function SelectStudentInterests(Request $request)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        $student = auth('api')->user();

        $student->interests()->sync($validated['interest_ids']);

        return response()->json([
            'message' => 'Interests selected succefully',
            'interests' => $student->interests()->get(),
        ]);
    }

    public function myInterests()
    {
        $student = auth('api')->user()->load('interests');

        return response()->json([
            'interests' => $student->interests
        ]);
    }

    public function attachCourseInterests(Request $request,Course $course)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        if($course->teacher_id !== auth('api')->id()) {
            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $course->interests()->sync($validated['interest_ids']);

        return response()->json([
            'message' => 'Course interests updated successfully',
            'interests' => $course->interests()->get()
        ]);
    }

    public function courseInterests(Course $course)
    {
        $interests = $course->interests()->get();

        return response()->json([
            'Course title' => $course->title,
            'interests' => $interests,
        ]);
    }
}
