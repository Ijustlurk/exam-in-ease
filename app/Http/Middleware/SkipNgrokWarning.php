<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SkipNgrokWarning
{
    /**
     * Handle an incoming request.
     * This middleware adds the ngrok-skip-browser-warning header to skip the Ngrok warning page
     * when accessing the API through Ngrok tunnels.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add header to skip Ngrok browser warning
        $request->headers->set('ngrok-skip-browser-warning', 'true');
        
        $response = $next($request);
        
        // Also add to response headers for good measure
        if ($response instanceof Response) {
            $response->headers->set('ngrok-skip-browser-warning', 'true');
        }
        
        return $response;
    }
}
