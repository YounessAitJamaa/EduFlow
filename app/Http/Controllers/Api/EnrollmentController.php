<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use Exception;
use OpenApi\Attributes as OA;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    #[OA\Post(
        path: "/api/courses/{course}/enroll",
        summary: "Enroll in a course",
        tags: ["Enrollment"],
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
        description: "Enrolled Successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "enrollment", type: "object"),
                new OA\Property(property: "group", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 409, description: "Already Enrolled")]
    public function store(Course $course)
    {
        try {
            $result = $this->enrollmentService->enrollStudent($course, auth('api')->id());

            return response()->json([
                'message' => 'Enrolled Successfully',
                'enrollment' => $result['enrollment'],
                'group' => $result['group'],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Delete(
        path: "/api/courses/{course}/leave",
        summary: "Leave a course",
        tags: ["Enrollment"],
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
        description: "You left the course successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 404, description: "Enrollment not found")]
    public function destroy(Course $course)
    {
        try {
            $this->enrollmentService->leaveCourse($course, auth('api')->id());

            return response()->json([
                'message' => 'You left the course successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Get(
        path: "/api/enrollments",
        summary: "Get my enrollments",
        tags: ["Enrollment"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of student enrollments",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "enrollments", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function myEnrollments()
    {
        $enrollments = $this->enrollmentService->getMyEnrollments(auth('api')->id());

        return response()->json([
            'enrollments' => $enrollments
        ]);
    }

    #[OA\Get(
        path: "/api/courses/{course}/students",
        summary: "List students enrolled in a course",
        tags: ["Enrollment"],
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
        description: "List of enrolled students",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "course_id", type: "integer"),
                new OA\Property(property: "course_title", type: "string"),
                new OA\Property(property: "students", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only the course teacher can view")]
    public function courseStudents(Course $course)
    {
        try {
            $students = $this->enrollmentService->getCourseStudents($course, auth('api')->id());

            return response()->json([
                'course_id' => $course->id,
                'course_title' => $course->title,
                'students' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
