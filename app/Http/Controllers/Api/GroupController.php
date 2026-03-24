<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function ListGroups(Course $course)
    {
        $teacherId = auth('api')->id();

        $SeachredCourse = Course::where('id', $course->id)
                ->where('teacher_id', $teacherId)
                ->with('groups')
                ->first();
        
        if (!$course)
        {
            return response()->json([
                'message' => 'Course not found or access denied'
            ], 404);
        }

        return response()->json([
            'course_id' => $SeachredCourse->id,
            'course_title' => $SeachredCourse->title,
            'groups' => $SeachredCourse->groups
        ]);
    }
}
