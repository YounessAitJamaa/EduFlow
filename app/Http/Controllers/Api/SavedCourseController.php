<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\SavedCourseService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SavedCourseController extends Controller
{
    protected $savedCourseService;

    public function __construct(SavedCourseService $savedCourseService)
    {
        $this->savedCourseService = $savedCourseService;
    }

    #[OA\Get(
        path: "/api/saved-courses",
        summary: "List all saved courses",
        tags: ["Wishlist"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of saved courses",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "saved_courses", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        $savedCourses = $this->savedCourseService->getMySavedCourses(auth('api')->id());

        return response()->json([
            'saved_courses' => $savedCourses
        ]);
    }

    #[OA\Post(
        path: "/api/saved-courses/{course}",
        summary: "Add course to wishlist",
        tags: ["Wishlist"],
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
        response: 201,
        description: "Course saved successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "saved_course", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 409, description: "Course already saved")]
    public function store(Course $course)
    {
        try {
            $savedCourse = $this->savedCourseService->saveCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Course saved successfully',
                'saved_course' => $savedCourse
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Delete(
        path: "/api/saved-courses/{course}",
        summary: "Remove course from wishlist",
        tags: ["Wishlist"],
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
        description: "Course removed from saved courses",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 404, description: "Saved course not found")]
    public function destroy(Course $course)
    {
        try {
            $this->savedCourseService->removeSavedCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Course removed from saved courses'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
