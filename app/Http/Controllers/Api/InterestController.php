<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\InterestService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class InterestController extends Controller
{
    protected $interestService;

    public function __construct(InterestService $interestService)
    {
        $this->interestService = $interestService;
    }

    #[OA\Post(
        path: "/api/student/interests",
        summary: "Select student interests",
        tags: ["Interests"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["interest_ids"],
            properties: [
                new OA\Property(property: "interest_ids", type: "array", items: new OA\Items(type: "integer", example: 1))
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Interests selected successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "interests", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function SelectStudentInterests(Request $request)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        $interests = $this->interestService->syncStudentInterests(auth('api')->user(), $validated['interest_ids']);

        return response()->json([
            'message' => 'Interests selected successfully',
            'interests' => $interests,
        ]);
    }

    #[OA\Post(
        path: "/api/courses/{course}/interests",
        summary: "Attach interests to a course",
        tags: ["Interests"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "course",
        in: "path",
        description: "Course ID",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["interest_ids"],
            properties: [
                new OA\Property(property: "interest_ids", type: "array", items: new OA\Items(type: "integer", example: 1))
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Course interests updated successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "interests", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the course teacher can attach interests")]
    public function attachCourseInterests(Request $request, Course $course)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        try {
            $interests = $this->interestService->attachCourseInterests($course, $validated['interest_ids'], auth('api')->id());

            return response()->json([
                'message: ' => 'Course interests updated successfully',
                'interests' => $interests
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Get(
        path: "/api/student/recommended-courses",
        summary: "Get recommended courses based on interests",
        tags: ["Interests"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of recommended courses",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "recommended_courses", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function recommendedCourses()
    {
        $courses = $this->interestService->getRecommendedCourses(auth('api')->user());

        return response()->json([
            'recommended_courses' => $courses
        ]);
    }

    #[OA\Get(
        path: "/api/interests",
        summary: "Get all available interests",
        tags: ["Interests"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of all interests",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "interests", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        $interests = $this->interestService->getAllInterests();

        return response()->json([
            'interests' => $interests
        ]);
    }

    #[OA\Get(
        path: "/api/student/interests",
        summary: "Get student's selected interests",
        tags: ["Interests"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of student's interests",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "interests", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function myInterests()
    {
        $interests = $this->interestService->getStudentInterests(auth('api')->user());

        return response()->json([
            'interests' => $interests
        ]);
    }
}
