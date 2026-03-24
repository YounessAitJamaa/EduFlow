<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function createCheckoutSession(Course $course)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $course->title,
                    ],
                    'unit_amount' => $course->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/api/payment/success?course_id=' . $course->id . '&student_id=' . auth('api')->id()),
            'cancel_url' => url('/api/payment/cancel'),
        ]);

        return response()->json([
            'checkout_url' => $session->url
        ]);

    }

    public function success(Request $request)
    {
        $courseId = $request->course_id;

        $enrollment = Enrollment::create([
            'student_id' => $request->student_id,
            'course_id' => $courseId,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Payment successful, enrolled in course',
            'enrollment' => $enrollment
        ]);
    }
}
