<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }
    public function createCheckoutSession(Course $course, int $studentId): string
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $course->title,
                        ],
                        'unit_amount' => $course->price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => url('/payment/success?course_id=' . $course->id),
            'cancel_url' => url('/payment/cancel'),
        ]);

        return $session->url;
    }

    public function processSuccess(int $courseId, int $studentId): array
    {
        $course = Course::findOrFail($courseId);
        return $this->enrollmentService->enrollStudent($course, $studentId);
    }
}
