<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
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

    public function groupParticipants(Group $group)
    {
        $teacherId = auth('api')->id();

        $group = Group::with('students', 'course')
                ->where('id', $group->id)
                ->first();
        
        if(!$group || $group->course->teacher_id !== $teacherId)
        {
            return response()->json([
                'message' => 'Group not found or access denied'
            ], 404);
        }

        $students = $group->students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ];
        });

        return response()->json([
            'group id' => $group->id,
            'group name' => $group->name,
            'course id' => $group->course->id,
            'course title' => $group->course->title,
            'students' => $students
        ]);
    }
}
