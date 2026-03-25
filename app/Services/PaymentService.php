<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
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
            'success_url' => url('/api/payment/success?course_id=' . $course->id . '&student_id=' . $studentId),
            'cancel_url' => url('/api/payment/cancel'),
        ]);

        return $session->url;
    }

    public function processSuccess(int $courseId, int $studentId): Enrollment
    {
        return Enrollment::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'status' => 'active',
        ]);
    }
}
