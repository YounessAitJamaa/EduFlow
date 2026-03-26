<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\PaymentService;
use App\Services\EnrollmentService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    #[OA\Post(
        path: "/api/payment/checkout/{course}",
        summary: "Create a Stripe checkout session for a course",
        tags: ["Payments"],
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
        description: "Checkout URL retrieved",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "checkout_url", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 500, description: "Failed to create checkout session")]
    public function createCheckoutSession(Course $course)
    {
        try {
            $checkoutUrl = $this->paymentService->createCheckoutSession($course, auth('api')->id());

            return response()->json([
                'checkout_url' => $checkoutUrl
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/payment/success",
        summary: "Handle successful payment redirect",
        tags: ["Payments"]
    )]
    #[OA\Parameter(
        name: "course_id",
        in: "query",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "student_id",
        in: "query",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Payment processed and student enrolled",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string"),
                new OA\Property(property: "enrollment", type: "object"),
                new OA\Property(property: "group", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 500, description: "Failed to process success")]
    public function success(Request $request)
    {
        try {
            $result = $this->paymentService->processSuccess($request->course_id, $request->student_id);

            return response()->json([
                'message' => 'Payment successful, enrolled in course',
                'enrollment' => $result['enrollment'],
                'group' => $result['group']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to process payment success: ' . $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/payment/cancel",
        summary: "Handle cancelled payment redirect",
        tags: ["Payments"]
    )]
    #[OA\Response(
        response: 200,
        description: "Payment cancelled message",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Payment cancelled")
            ]
        )
    )]
    public function cancel()
    {
        return response()->json([
            'message' => 'Payment cancelled'
        ]);
    }
}
