<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHeadersPresent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            // Check for the required headers
            if (!$request->hasHeader('Accept') || !$request->hasHeader('Content-Type') ||
                $request->header('Accept') !== 'application/json' ||
                $request->header('Content-Type') !== 'application/json') {
                return response()->json([
                    'message' => 'The request must contain both Accept and Content-Type headers.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return $next($request);
    }
}
