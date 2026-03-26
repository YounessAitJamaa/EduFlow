<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use App\Services\GroupService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    #[OA\Get(
        path: "/api/courses/{course}/groups",
        summary: "List all groups for a course",
        tags: ["Groups"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "course",
        in: "path",
        description: "Course ID",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "List of groups",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "course_id", type: "integer"),
                new OA\Property(property: "course_title", type: "string"),
                new OA\Property(property: "groups", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the course teacher can view groups")]
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

    #[OA\Get(
        path: "/api/groups/{group}/students",
        summary: "List participants in a group",
        tags: ["Groups"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "group",
        in: "path",
        description: "Group ID",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "List of participants",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "group_id", type: "integer"),
                new OA\Property(property: "group_name", type: "string"),
                new OA\Property(property: "course_title", type: "string"),
                new OA\Property(property: "participants", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the teacher of the parent course can view")]
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
