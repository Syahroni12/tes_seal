<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated

        if (Auth::user() && Auth::user()->role == 'admin') {

            // User is authenticated and has the 'admin' role
            // You can proceed with the request
            return $next($request);
        } else {
            // User is authenticated but does not have the 'admin' role
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
    }
}
