<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EssayGradingService
{
    protected Client $client;
    protected string $provider;
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;
    protected float $temperature;
    protected ?string $apiEndpoint;
    protected ?string $organizationId;

    public function __construct()
    {
        $this->provider = Config::get('ai.provider', 'openai');
        $this->apiKey = Config::get('ai.api_key');
        $this->model = Config::get('ai.model', 'gpt-4');
        $this->maxTokens = Config::get('ai.max_tokens', 1500);
        $this->temperature = Config::get('ai.temperature', 0.3);
        $this->apiEndpoint = Config::get('ai.api_endpoint');
        $this->organizationId = Config::get('ai.organization_id');

        $this->client = new Client([
            'timeout' => Config::get('ai.grading.timeout', 30),
            'verify' => true,
        ]);
    }

    /**
     * Grade an essay using AI
     *
     * @param string $question The essay question/prompt
     * @param string|null $rubric The grading rubric or expected answer
     * @param string $studentAnswer The student's essay response
     * @param int $maxPoints Maximum points for this question
     * @return array ['points_earned' => float, 'is_correct' => bool|null, 'feedback' => string, 'confidence' => int]
     */
    public function gradeEssay(string $question, ?string $rubric, string $studentAnswer, int $maxPoints): array
    {
        // Check if AI grading is enabled
        if (!Config::get('ai.grading.enabled', true)) {
            return $this->fallbackToManual('AI grading is disabled');
        }

        // Validate API key
        if (empty($this->apiKey)) {
            Log::warning('AI API key not configured');
            return $this->fallbackToManual('API key not configured');
        }

        // Validate student answer is not empty
        if (empty(trim($studentAnswer))) {
            return [
                'points_earned' => 0,
                'is_correct' => false,
                'feedback' => 'No answer provided.',
                'confidence' => 100
            ];
        }

        try {
            // Build the grading prompt
            $prompt = $this->buildGradingPrompt($question, $rubric, $studentAnswer, $maxPoints);

            // Make API call based on provider
            $response = $this->callAIProvider($prompt);

            // Parse the response
            $result = $this->parseGradingResponse($response, $maxPoints);

            // Log if enabled
            if (Config::get('ai.logging.enabled', true)) {
                Log::channel(Config::get('ai.logging.channel', 'stack'))->info('AI Essay Grading', [
                    'question_preview' => substr($question, 0, 100),
                    'answer_length' => strlen($studentAnswer),
                    'points_awarded' => $result['points_earned'],
                    'max_points' => $maxPoints,
                    'confidence' => $result['confidence'],
                ]);
            }

            // Check confidence threshold
            $minConfidence = Config::get('ai.grading.min_confidence', 70);
            if ($result['confidence'] < $minConfidence) {
                Log::info("Low confidence grading ({$result['confidence']}%), marking for manual review");
                return $this->fallbackToManual("AI confidence below threshold ({$result['confidence']}%)");
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('AI Essay Grading Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (Config::get('ai.grading.fallback_to_manual', true)) {
                return $this->fallbackToManual($e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Build the grading prompt for the AI
     */
    protected function buildGradingPrompt(string $question, ?string $rubric, string $studentAnswer, int $maxPoints): string
    {
        $prompt = "You are an expert essay grader. Grade the following student essay response.\n\n";
        $prompt .= "QUESTION:\n{$question}\n\n";

        if (!empty($rubric)) {
            $prompt .= "GRADING RUBRIC/EXPECTED ANSWER:\n{$rubric}\n\n";
        }

        $prompt .= "STUDENT ANSWER:\n{$studentAnswer}\n\n";
        $prompt .= "MAXIMUM POINTS: {$maxPoints}\n\n";

        $prompt .= "Please evaluate this answer and provide:\n";
        $prompt .= "1. A score from 0 to {$maxPoints} (can include decimals for partial credit)\n";
        $prompt .= "2. Detailed feedback explaining the grade\n";
        $prompt .= "3. Your confidence level in this grading (0-100%)\n\n";

        $prompt .= "Respond ONLY with valid JSON in this exact format:\n";
        $prompt .= "{\n";
        $prompt .= "  \"score\": <number between 0 and {$maxPoints}>,\n";
        $prompt .= "  \"feedback\": \"<detailed explanation of the grade>\",\n";
        $prompt .= "  \"confidence\": <number between 0 and 100>\n";
        $prompt .= "}\n\n";

        $prompt .= "Be fair and consistent in grading. If the rubric is provided, follow it closely. ";
        $prompt .= "Award partial credit for partially correct answers.";

        return $prompt;
    }

    /**
     * Call the AI provider API
     */
    protected function callAIProvider(string $prompt): string
    {
        $retryAttempts = Config::get('ai.grading.retry_attempts', 2);
        $lastException = null;

        for ($attempt = 0; $attempt <= $retryAttempts; $attempt++) {
            try {
                if ($attempt > 0) {
                    // Exponential backoff: 2^attempt seconds
                    $delay = pow(2, $attempt);
                    Log::info("Retrying AI API call after {$delay}s (attempt {$attempt})");
                    sleep($delay);
                }

                switch ($this->provider) {
                    case 'openai':
                        return $this->callOpenAI($prompt);
                    case 'claude':
                    case 'anthropic':
                        return $this->callClaude($prompt);
                    case 'gemini':
                        return $this->callGemini($prompt);
                    default:
                        return $this->callCustomProvider($prompt);
                }
            } catch (GuzzleException $e) {
                $lastException = $e;
                Log::warning("AI API call failed (attempt {$attempt})", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        throw $lastException ?? new \Exception('AI API call failed');
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(string $prompt): string
    {
        $endpoint = $this->apiEndpoint ?? 'https://api.openai.com/v1/chat/completions';

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($this->organizationId) {
            $headers['OpenAI-Organization'] = $this->organizationId;
        }

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'json' => [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert essay grader. Always respond with valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Call Claude/Anthropic API
     */
    protected function callClaude(string $prompt): string
    {
        $endpoint = $this->apiEndpoint ?? 'https://api.anthropic.com/v1/messages';

        $response = $this->client->post($endpoint, [
            'headers' => [
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Call Google Gemini API
     */
    protected function callGemini(string $prompt): string
    {
        $endpoint = $this->apiEndpoint ?? "https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent";

        $response = $this->client->post($endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'key' => $this->apiKey,
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $this->temperature,
                    'maxOutputTokens' => $this->maxTokens,
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    /**
     * Call custom provider API (generic implementation)
     */
    protected function callCustomProvider(string $prompt): string
    {
        if (empty($this->apiEndpoint)) {
            throw new \Exception('API endpoint not configured for custom provider');
        }

        $response = $this->client->post($this->apiEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->model,
                'prompt' => $prompt,
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Try common response formats
        return $data['response']
            ?? $data['text']
            ?? $data['content']
            ?? $data['choices'][0]['text']
            ?? '';
    }

    /**
     * Parse the AI grading response
     */
    protected function parseGradingResponse(string $response, int $maxPoints): array
    {
        // Try to extract JSON from the response
        // Sometimes AI wraps JSON in markdown code blocks
        $response = trim($response);

        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $response, $matches)) {
            $response = $matches[1];
        }

        // Try to parse JSON
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response as JSON', [
                'response' => $response,
                'error' => json_last_error_msg()
            ]);
            throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
        }

        // Extract score, feedback, and confidence
        $score = floatval($data['score'] ?? 0);
        $feedback = $data['feedback'] ?? 'No feedback provided.';
        $confidence = intval($data['confidence'] ?? 50);

        // Validate score is within range
        $score = max(0, min($maxPoints, $score));

        // Validate confidence is within range
        $confidence = max(0, min(100, $confidence));

        // Determine if correct (>= 50% of max points)
        $isCorrect = $score >= ($maxPoints * 0.5);

        return [
            'points_earned' => round($score, 2),
            'is_correct' => $isCorrect,
            'feedback' => $feedback,
            'confidence' => $confidence,
        ];
    }

    /**
     * Return fallback response for manual grading
     */
    protected function fallbackToManual(string $reason): array
    {
        return [
            'points_earned' => 0,
            'is_correct' => null, // null indicates manual review needed
            'feedback' => 'This essay requires manual grading. Reason: ' . $reason,
            'confidence' => 0,
        ];
    }

    /**
     * Check if AI grading is available/configured
     */
    public static function isAvailable(): bool
    {
        return Config::get('ai.grading.enabled', true)
            && !empty(Config::get('ai.api_key'));
    }
}
