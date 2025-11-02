<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SkipNgrokWarning
{
    /**
     * Handle an incoming request.
     *
     * Add ngrok-skip-browser-warning header to bypass ngrok's browser warning page
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add header to skip ngrok browser warning
        $request->headers->set('ngrok-skip-browser-warning', 'true');
        
        $response = $next($request);
        
        // Ensure JSON response for API routes
        if ($request->is('api/*') && !$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json');
        }
        
        return $response;
    }
}
