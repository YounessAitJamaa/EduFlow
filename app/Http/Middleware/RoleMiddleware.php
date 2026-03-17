<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if(!auth('api')->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if(auth('api')->user()->role !== $role) {
            return response()->json([
                'message' => 'Access Denied'
            ], 403);
        }
        return $next($request);
    }
}
