<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for AI-based essay grading.
    | Supported providers: openai, claude, gemini, custom
    |
    */

    'provider' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | API key and endpoint for your chosen AI provider
    |
    */

    'api_key' => env('AI_API_KEY'),
    'api_endpoint' => env('AI_API_ENDPOINT'),
    'organization_id' => env('AI_ORGANIZATION_ID'), // Optional, for OpenAI

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Model to use for essay grading
    | OpenAI: gpt-4, gpt-4-turbo, gpt-3.5-turbo
    | Claude: claude-3-opus-20240229, claude-3-sonnet-20240229
    | Gemini: gemini-pro, gemini-1.5-pro
    |
    */

    'model' => env('AI_MODEL', 'gpt-4'),

    /*
    |--------------------------------------------------------------------------
    | Generation Parameters
    |--------------------------------------------------------------------------
    |
    | Control the AI's behavior during grading
    |
    */

    'max_tokens' => env('AI_MAX_TOKENS', 1500),
    'temperature' => env('AI_TEMPERATURE', 0.3), // Lower = more consistent

    /*
    |--------------------------------------------------------------------------
    | Essay Grading Configuration
    |--------------------------------------------------------------------------
    */

    'grading' => [
        // Enable/disable AI grading
        'enabled' => env('AI_GRADING_ENABLED', true),

        // Fallback to manual grading on AI failure
        'fallback_to_manual' => env('AI_FALLBACK_TO_MANUAL', true),

        // Timeout for AI API calls (seconds)
        'timeout' => env('AI_TIMEOUT', 30),

        // Retry attempts on failure
        'retry_attempts' => env('AI_RETRY_ATTEMPTS', 2),

        // Minimum confidence score to auto-grade (0-100)
        // Below this, mark for manual review
        'min_confidence' => env('AI_MIN_CONFIDENCE', 70),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        // Maximum API calls per minute
        'requests_per_minute' => env('AI_RATE_LIMIT_RPM', 60),

        // Enable queueing for rate limit handling
        'use_queue' => env('AI_USE_QUEUE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        // Log all AI grading requests/responses
        'enabled' => env('AI_LOGGING_ENABLED', true),

        // Log channel to use
        'channel' => env('AI_LOG_CHANNEL', 'stack'),
    ],

];
