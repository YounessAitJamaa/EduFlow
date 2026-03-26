<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "password_confirmation", "role"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Leo Messi"),
                new OA\Property(property: "email", type: "string", format: "email", example: "leo@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
                new OA\Property(property: "role", type: "string", enum: ["student", "teacher"], example: "student")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "User Created Successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "User Created Successfully"),
                new OA\Property(property: "user", type: "object"),
                new OA\Property(property: "token", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 422, description: "Validation Error")]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password'=> ['required','string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:student,teacher'],
        ]);

        $result = $this->authService->register($validated);

        return response()->json([
            'message' => 'User Created Successfully',
            'user' => $result['user'],
            'token' => $result['token']
        ], 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "User Login",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "leo@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Login Successful",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Login Successful"),
                new OA\Property(property: "token", type: "string"),
                new OA\Property(property: "user", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Invalid credentials")]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $result = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login Successful',
                'token' => $result['token'],
                'user' => $result['user'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Get(
        path: "/api/me",
        summary: "Get authenticated user info",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "User profile retrieved",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "user", type: "object")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Unauthenticated")]
    public function me()
    {
        return response()->json([
            'user' => $this->authService->me()
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "User Logout",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Logged Out Successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Logged Out Successfully")
            ]
        )
    )]
    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Logged Out Successfully'
        ]);
    }

    #[OA\Post(
        path: "/api/forgot-password",
        summary: "Send password reset link",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "leo@example.com")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Reset link sent",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 400, description: "Error sending link")]
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $message = $this->authService->forgotPassword($validated);
            
            return response()->json([
                'message' => $message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    #[OA\Post(
        path: "/api/reset-password",
        summary: "Reset password using token",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["token", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "token", type: "string", example: "abc123token"),
                new OA\Property(property: "email", type: "string", format: "email", example: "leo@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "newpassword123")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Password reset successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 400, description: "Error resetting password")]
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $message = $this->authService->resetPassword($validated);

            return response()->json([
                'message' => $message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
