<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\PaymentService;
use App\Services\EnrollmentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

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

    public function success(Request $request)
    {
        try {
            $enrollment = $this->paymentService->processSuccess($request->course_id, $request->student_id);

            return response()->json([
                'message' => 'Payment successful, enrolled in course',
                'enrollment' => $enrollment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to process payment success: ' . $e->getMessage()
            ], 500);
        }
    }
}
