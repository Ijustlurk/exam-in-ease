<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ConfigureRateLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Configure rate limiters for different operation types
        $this->configureRateLimiters();
        
        return $next($request);
    }

    /**
     * Configure rate limiters for different API operation types
     */
    protected function configureRateLimiters()
    {
        // Read operations (GET) - Higher limit
        RateLimiter::for('api-read', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->ip());
        });

        // Write operations (POST/PUT) - Medium limit
        RateLimiter::for('api-write', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Delete operations - Lower limit
        RateLimiter::for('api-delete', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // Critical operations (override, reset password) - Strictest limit
        RateLimiter::for('api-critical', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
        });

        // Expensive operations (downloads, exports) - Very low limit
        RateLimiter::for('api-expensive', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(2)->by($request->ip());
        });

        // File uploads/imports - Strict limit
        RateLimiter::for('api-upload', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(2)->by($request->ip());
        });

        // Search/Filter operations - Medium-high limit
        RateLimiter::for('api-search', function (Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(15)->by($request->ip());
        });
    }
}
