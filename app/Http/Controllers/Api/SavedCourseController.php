<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedCourse;
use Illuminate\Http\Request;

class SavedCourseController extends Controller
{
    public function index()
    {
        $savedCourses = SavedCourse::with(['course', 'student']);

        return response()->json([
            'saved Courses' => $savedCourses
        ]);
    }
}
