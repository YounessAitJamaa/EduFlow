<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    #[OA\Get(
        path: "/api/statistics",
        summary: "Get teacher statistics",
        description: "Returns total students, total courses, and total earnings for the authenticated teacher.",
        tags: ["Statistics"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Teacher statistics retrieved",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "statistics",
                    type: "object",
                    properties: [
                        new OA\Property(property: "total_students", type: "integer", example: 150),
                        new OA\Property(property: "total_courses", type: "integer", example: 5),
                        new OA\Property(property: "total_earnings", type: "number", format: "float", example: 2500.5)
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 403, description: "Forbidden - Only teachers can view statistics")]
    #[OA\Response(response: 500, description: "Internal server error")]
    public function index()
    {
        try {
            $stats = $this->statisticsService->getTeacherStatistics(auth('api')->id());

            return response()->json([
                'statistics' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
