<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function SelectStudentInterests(Request $request)
    {
        $validated = $request->validate([
            'interest_ids' => ['required', 'array'],
            'interest_ids.*' => ['exists:interests,id'],
        ]);

        $student = auth('api')->user();

        $student->interests()->sync($validated['interest_ids']);

        return response()->json([
            'message' => 'Interests selected succefully',
            'interests' => $student->interests()->get(),
        ]);
    }
}
