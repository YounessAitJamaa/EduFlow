<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use App\Services\GroupService;
use Exception;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function ListGroups(Course $course)
    {
        try {
            $groups = $this->groupService->getCourseGroups($course, auth('api')->id());

            return response()->json([
                'course_id' => $course->id,
                'course_title' => $course->title,
                'groups' => $groups
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function groupParticipants(Group $group)
    {
        try {
            $response = $this->groupService->getGroupParticipants($group, auth('api')->id());
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
