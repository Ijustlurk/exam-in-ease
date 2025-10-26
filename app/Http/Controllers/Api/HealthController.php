<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthController extends Controller
{
    /**
     * Health check endpoint for mobile app
     */
    public function check()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => Carbon::now()->toIso8601String(),
            'message' => 'Server is running',
        ]);
    }
}
