<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    #[OA\Get(
        path: "/api/courses",
        summary: "List all courses",
        tags: ["Courses"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "courses", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        $courses = $this->courseService->getAllCourses();

        return response()->json([
            'courses' => $courses
        ]);
    }

    #[OA\Get(
        path: "/api/courses/{course}",
        summary: "Get course details",
        tags: ["Courses"],
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
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "course", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 404, description: "Course not found")]
    public function show(Course $course) 
    {
        return response()->json([
            'course' => $this->courseService->getCourseById($course)
        ]);
    }

    #[OA\Post(
        path: "/api/courses",
        summary: "Create a new course",
        tags: ["Courses"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "price"],
            properties: [
                new OA\Property(property: "title", type: "string", example: "Laravel Advanced"),
                new OA\Property(property: "description", type: "string", example: "Deep dive into Laravel"),
                new OA\Property(property: "price", type: "number", format: "float", example: 49.99)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Course Created with success",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "course", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only teachers can create courses")]
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $course = $this->courseService->createCourse($validated, auth('api')->id());

        return response()->json([
            'message' => 'Course Created with success',
            'course' => $course
        ], 201);
    }

    #[OA\Put(
        path: "/api/courses/{course}",
        summary: "Update an existing course",
        tags: ["Courses"],
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
            properties: [
                new OA\Property(property: "title", type: "string"),
                new OA\Property(property: "description", type: "string"),
                new OA\Property(property: "price", type: "number", format: "float")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Course Updated Successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "course", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the course teacher can update")]
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        try {
            $updatedCourse = $this->courseService->updateCourse($course, $validated, auth('api')->id());
            
            return response()->json([
                'message' => 'Course Updated Successfully',
                'course' => $updatedCourse
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Delete(
        path: "/api/courses/{course}",
        summary: "Delete a course",
        tags: ["Courses"],
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
        description: "Course Deleted Successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the course teacher can delete")]
    public function destroy(Course $course)
    {
        try {
            $this->courseService->deleteCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'Course Deleted Successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
