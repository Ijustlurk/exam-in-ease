# AI Essay Grading Integration Guide

## Overview

This system now supports **automatic AI-powered essay grading** using external AI providers like OpenAI (GPT-4), Anthropic (Claude), Google (Gemini), or custom APIs. Essays are automatically graded when students submit their exam attempts, with support for fallback to manual grading if AI is unavailable or confidence is low.

---

## Table of Contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Setup Instructions](#setup-instructions)
4. [Configuration Options](#configuration-options)
5. [How It Works](#how-it-works)
6. [Database Schema](#database-schema)
7. [Grading Rubric Format](#grading-rubric-format)
8. [API Provider Setup](#api-provider-setup)
9. [Testing](#testing)
10. [Troubleshooting](#troubleshooting)
11. [Cost Management](#cost-management)

---

## Features

- **Automatic AI Grading**: Essays are graded in real-time when students submit exams
- **Multiple AI Providers**: Support for OpenAI, Claude, Gemini, or custom APIs
- **Intelligent Fallback**: Automatically falls back to manual grading on errors or low confidence
- **Partial Credit**: AI can award partial points based on answer quality
- **Detailed Feedback**: Students receive AI-generated feedback explaining their grade
- **Confidence Scoring**: Each grade includes a confidence level (0-100%)
- **Manual Review Flag**: Low-confidence grades are flagged for instructor review
- **Retry Logic**: Automatic retry with exponential backoff on API failures
- **Comprehensive Logging**: All AI grading attempts are logged for auditing

---

## Requirements

### System Requirements
- PHP 8.1 or higher
- Laravel 10.10+
- MySQL database
- Guzzle HTTP Client (already included)
- Active internet connection for AI API calls

### API Requirements
- API key from your chosen provider (OpenAI, Claude, Gemini, etc.)
- Sufficient API credits/quota for your expected usage

---

## Setup Instructions

### Step 1: Choose an AI Provider

Select one of the supported providers:
- **OpenAI** (GPT-4, GPT-3.5-turbo)
- **Anthropic Claude** (Claude 3 Opus, Sonnet)
- **Google Gemini** (Gemini Pro, Gemini 1.5 Pro)
- **Custom API** (any API with similar interface)

### Step 2: Obtain API Credentials

#### For OpenAI:
1. Go to [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)
2. Create a new API key
3. (Optional) Get your Organization ID from account settings

#### For Claude/Anthropic:
1. Go to [https://console.anthropic.com/](https://console.anthropic.com/)
2. Navigate to API keys
3. Create a new API key

#### For Google Gemini:
1. Go to [https://makersuite.google.com/app/apikey](https://makersuite.google.com/app/apikey)
2. Create a new API key

### Step 3: Configure Environment Variables

Copy your `.env.example` to `.env` (if not already done), then add:

```env
# AI Essay Grading Configuration
AI_PROVIDER=openai                    # Options: openai, claude, gemini, custom
AI_API_KEY=your_api_key_here          # REQUIRED: Your API key
AI_API_ENDPOINT=                      # Optional: Custom endpoint URL
AI_ORGANIZATION_ID=                   # Optional: For OpenAI organization accounts
AI_MODEL=gpt-4                        # Model name (see provider-specific section)
AI_MAX_TOKENS=1500                    # Maximum response length
AI_TEMPERATURE=0.3                    # Creativity (0.0-1.0, lower = more consistent)

# AI Grading Features
AI_GRADING_ENABLED=true               # Enable/disable AI grading
AI_FALLBACK_TO_MANUAL=true            # Fallback to manual on errors
AI_TIMEOUT=30                         # API timeout in seconds
AI_RETRY_ATTEMPTS=2                   # Number of retry attempts on failure
AI_MIN_CONFIDENCE=70                  # Minimum confidence to auto-grade (0-100)

# AI Rate Limiting
AI_RATE_LIMIT_RPM=60                  # Requests per minute limit
AI_USE_QUEUE=false                    # Use queue for rate limiting (future feature)

# AI Logging
AI_LOGGING_ENABLED=true               # Log all AI grading attempts
AI_LOG_CHANNEL=stack                  # Laravel log channel to use
```

**Important:** Never commit your `.env` file with API keys to version control!

### Step 4: Run Database Migration

```bash
php artisan migrate
```

This creates the following columns in `exam_answers`:
- `ai_feedback` (TEXT, nullable)
- `ai_confidence` (TINYINT, nullable)
- `requires_manual_review` (BOOLEAN, default false)

### Step 5: Test Configuration

Create a test essay question and submit a sample answer to verify AI grading works.

---

## Configuration Options

### AI_PROVIDER

Determines which AI service to use:
- `openai` - OpenAI GPT models
- `claude` or `anthropic` - Anthropic Claude models
- `gemini` - Google Gemini models
- `custom` - Custom API endpoint

### AI_MODEL

Model identifier varies by provider:

**OpenAI:**
- `gpt-4` - Most capable, higher cost
- `gpt-4-turbo` - Faster, slightly less expensive
- `gpt-3.5-turbo` - Faster, cheaper, less capable

**Claude:**
- `claude-3-opus-20240229` - Most capable
- `claude-3-sonnet-20240229` - Balanced
- `claude-3-haiku-20240307` - Fastest, cheapest

**Gemini:**
- `gemini-pro` - Standard model
- `gemini-1.5-pro` - Latest version

### AI_TEMPERATURE

Controls randomness/creativity (0.0 to 1.0):
- `0.0-0.3` - More consistent, deterministic (recommended for grading)
- `0.4-0.7` - Balanced
- `0.8-1.0` - More creative, less consistent

### AI_MIN_CONFIDENCE

If AI confidence falls below this threshold, the essay is flagged for manual review:
- `70` (default) - Balanced
- `80-90` - Stricter (more manual reviews)
- `50-60` - Lenient (fewer manual reviews)

---

## How It Works

### Grading Flow

```
1. Student submits exam attempt
   ↓
2. System processes each answer by type
   ↓
3. For essay questions:
   ├─ Check if AI grading is enabled
   ├─ Call EssayGradingService
   │  ├─ Build grading prompt with question + rubric + student answer
   │  ├─ Call AI provider API
   │  ├─ Parse JSON response (score, feedback, confidence)
   │  └─ Return grading result
   ├─ Store result with AI feedback
   └─ If AI unavailable → Mark for manual review
   ↓
4. Save all answers to exam_answers table
   ↓
5. Calculate total score
   ↓
6. Update exam_attempts table
   ↓
7. Return results to student
```

### AI Prompt Structure

The system sends this prompt to the AI:

```
You are an expert essay grader. Grade the following student essay response.

QUESTION:
[Essay question text]

GRADING RUBRIC/EXPECTED ANSWER:
[Content from exam_items.expected_answer field]

STUDENT ANSWER:
[Student's essay text]

MAXIMUM POINTS: [Points available]

Please evaluate this answer and provide:
1. A score from 0 to [max points] (can include decimals for partial credit)
2. Detailed feedback explaining the grade
3. Your confidence level in this grading (0-100%)

Respond ONLY with valid JSON in this exact format:
{
  "score": <number>,
  "feedback": "<explanation>",
  "confidence": <number>
}

Be fair and consistent. Award partial credit for partially correct answers.
```

### Response Parsing

The AI returns a JSON response:
```json
{
  "score": 7.5,
  "feedback": "The essay demonstrates a good understanding of the main concepts. The explanation of relativity is clear, though some details about time dilation could be expanded. The student correctly identifies key principles but misses the mathematical relationship E=mc².",
  "confidence": 85
}
```

The system:
1. Validates the JSON structure
2. Ensures score is within 0 to max_points
3. Ensures confidence is within 0 to 100
4. Determines `is_correct` (true if score ≥ 50% of max points)
5. Flags for manual review if confidence < AI_MIN_CONFIDENCE

---

## Database Schema

### New Columns in `exam_answers` Table

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `ai_feedback` | TEXT | Yes | AI-generated explanation of the grade |
| `ai_confidence` | TINYINT | Yes | AI confidence score (0-100) |
| `requires_manual_review` | BOOLEAN | No | Flag indicating instructor review needed |

### Example Record

```sql
INSERT INTO exam_answers (
    attempt_id,
    item_id,
    answer_text,
    is_correct,
    points_earned,
    ai_feedback,
    ai_confidence,
    requires_manual_review
) VALUES (
    123,
    45,
    'The theory of relativity...',
    true,
    7.50,
    'Good understanding shown. Missing E=mc² equation.',
    85,
    false
);
```

---

## Grading Rubric Format

The `exam_items.expected_answer` field stores the grading rubric. This can be:

### Format 1: Simple Model Answer

```
Einstein's theory of relativity describes how space and time are linked for objects moving at a constant speed in a straight line. The theory includes two parts: special relativity and general relativity. Key concepts include time dilation, length contraction, and the famous equation E=mc².
```

### Format 2: Structured Rubric

```
GRADING CRITERIA:
- Mention both special and general relativity (3 points)
- Explain time dilation concept (2 points)
- Include E=mc² equation (2 points)
- Provide clear examples (3 points)

TOTAL: 10 points

Sample key points:
1. Special relativity deals with constant speed
2. General relativity deals with gravity
3. Time moves slower at higher speeds
4. Mass and energy are interchangeable
```

### Format 3: JSON Format (Advanced)

```json
{
  "max_points": 10,
  "model_answer": "Theory that describes the relationship between space and time...",
  "criteria": [
    {"description": "Mentions special relativity", "points": 2},
    {"description": "Mentions general relativity", "points": 2},
    {"description": "Explains time dilation", "points": 2},
    {"description": "Includes E=mc²", "points": 2},
    {"description": "Provides examples", "points": 2}
  ]
}
```

**Recommendation:** The AI works well with all formats. More detailed rubrics lead to more consistent grading.

---

## API Provider Setup

### OpenAI Setup

**File:** `.env`
```env
AI_PROVIDER=openai
AI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxx
AI_ORGANIZATION_ID=org-xxxxxxxxxxxxxxxxxxxx  # Optional
AI_MODEL=gpt-4
AI_MAX_TOKENS=1500
AI_TEMPERATURE=0.3
```

**Endpoint:** `https://api.openai.com/v1/chat/completions` (automatic)

**Cost:** [Pricing](https://openai.com/pricing)
- GPT-4: ~$0.03/1K input tokens, ~$0.06/1K output tokens
- GPT-3.5-turbo: ~$0.0015/1K input tokens, ~$0.002/1K output tokens

### Claude/Anthropic Setup

**File:** `.env`
```env
AI_PROVIDER=claude
AI_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxxxx
AI_MODEL=claude-3-sonnet-20240229
AI_MAX_TOKENS=1500
AI_TEMPERATURE=0.3
```

**Endpoint:** `https://api.anthropic.com/v1/messages` (automatic)

**Cost:** [Pricing](https://www.anthropic.com/pricing)
- Claude 3 Opus: ~$15/$75 per million tokens (in/out)
- Claude 3 Sonnet: ~$3/$15 per million tokens (in/out)

### Google Gemini Setup

**File:** `.env`
```env
AI_PROVIDER=gemini
AI_API_KEY=AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AI_MODEL=gemini-pro
AI_MAX_TOKENS=1500
AI_TEMPERATURE=0.3
```

**Endpoint:** `https://generativelanguage.googleapis.com/v1/models/{model}:generateContent` (automatic)

**Cost:** [Pricing](https://ai.google.dev/pricing)
- Gemini Pro: Free tier available, then ~$0.00025/1K chars

### Custom API Setup

**File:** `.env`
```env
AI_PROVIDER=custom
AI_API_KEY=your_custom_api_key
AI_API_ENDPOINT=https://your-api.com/v1/completions
AI_MODEL=your-model-name
AI_MAX_TOKENS=1500
AI_TEMPERATURE=0.3
```

**Expected Request Format:**
```json
{
  "model": "your-model-name",
  "prompt": "The grading prompt...",
  "max_tokens": 1500,
  "temperature": 0.3
}
```

**Expected Response Format:**
```json
{
  "response": "JSON response string"
}
```
Or any format with fields: `response`, `text`, `content`, `choices[0].text`

---

## Testing

### Test 1: Verify Configuration

```php
use App\Services\EssayGradingService;

// Check if AI is available
if (EssayGradingService::isAvailable()) {
    echo "AI grading is configured and enabled!";
} else {
    echo "AI grading is not available. Check your .env configuration.";
}
```

### Test 2: Grade Sample Essay

Create an essay question in the admin panel:
- **Question:** "Explain photosynthesis."
- **Expected Answer (Rubric):** "Photosynthesis is the process by which plants convert light energy into chemical energy (glucose). It requires sunlight, water, and carbon dioxide. The process occurs in chloroplasts and produces oxygen as a byproduct. Key equation: 6CO2 + 6H2O + light → C6H12O6 + 6O2"
- **Points:** 10

Submit a test answer through the API:
```json
{
  "duration_taken": 300,
  "answers": [
    {
      "item_id": 1,
      "answer": "Plants use sunlight to make food. They take in CO2 and water, and produce glucose and oxygen. This happens in chloroplasts."
    }
  ]
}
```

Check the result:
```sql
SELECT
    answer_text,
    points_earned,
    ai_feedback,
    ai_confidence,
    requires_manual_review
FROM exam_answers
WHERE item_id = 1;
```

Expected result: Points ~6-8, feedback explaining what's missing, confidence ~80-90%.

### Test 3: Check Logs

```bash
tail -f storage/logs/laravel.log | grep "Essay graded by AI"
```

You should see:
```
[2025-11-01 12:34:56] local.INFO: Essay graded by AI {"item_id":1,"points_earned":7.5,"confidence":85,"requires_manual_review":false}
```

---

## Troubleshooting

### Issue: AI grading not working

**Symptoms:** Essays marked with `is_correct = NULL` and `requires_manual_review = true`

**Solutions:**
1. Check `.env` configuration:
   ```bash
   grep "^AI_" .env
   ```
   Ensure `AI_GRADING_ENABLED=true` and `AI_API_KEY` is set

2. Check logs:
   ```bash
   tail -50 storage/logs/laravel.log
   ```
   Look for error messages

3. Verify API key:
   ```bash
   curl https://api.openai.com/v1/models \
     -H "Authorization: Bearer YOUR_API_KEY"
   ```

### Issue: Invalid JSON response error

**Symptoms:** Log shows "Failed to parse AI response as JSON"

**Cause:** AI returned malformed JSON or wrapped in markdown

**Solution:** The service automatically strips markdown code blocks. If issue persists:
1. Increase `AI_TEMPERATURE` to 0.1 (sometimes 0.0 causes issues)
2. Change AI model (e.g., gpt-4 instead of gpt-3.5-turbo)
3. Check raw response in logs

### Issue: Low confidence scores

**Symptoms:** All essays flagged for manual review

**Solutions:**
1. Lower `AI_MIN_CONFIDENCE` threshold
2. Provide more detailed rubrics in `expected_answer`
3. Use a more capable model (e.g., GPT-4 instead of GPT-3.5)

### Issue: API timeout errors

**Symptoms:** "AI API call failed" with timeout message

**Solutions:**
1. Increase `AI_TIMEOUT` (e.g., to 60 seconds)
2. Reduce `AI_MAX_TOKENS` (faster responses)
3. Check internet connection

### Issue: Rate limit errors

**Symptoms:** "429 Too Many Requests" in logs

**Solutions:**
1. Reduce `AI_RATE_LIMIT_RPM`
2. Upgrade your API plan for higher limits
3. Enable `AI_USE_QUEUE=true` (requires queue configuration)

---

## Cost Management

### Estimating Costs

**Typical Essay Grading:**
- Prompt: ~300-500 tokens
- Essay: ~100-500 tokens (depending on length)
- Response: ~100-200 tokens
- **Total per essay:** ~500-1200 tokens

**Example Calculation (OpenAI GPT-4):**
- Input: 800 tokens × $0.03/1K = $0.024
- Output: 150 tokens × $0.06/1K = $0.009
- **Cost per essay:** ~$0.033

For 100 students × 5 essays each = 500 essays:
- **Total cost:** ~$16.50

### Cost Optimization Tips

1. **Use cheaper models for simple questions:**
   ```env
   AI_MODEL=gpt-3.5-turbo  # ~10x cheaper than GPT-4
   ```

2. **Reduce max_tokens:**
   ```env
   AI_MAX_TOKENS=1000  # Shorter responses
   ```

3. **Selective grading:**
   - Only use AI for long-form essays
   - Keep MCQ/TORF/ENUM auto-grading (no AI cost)

4. **Manual review threshold:**
   - Set `AI_MIN_CONFIDENCE=80` to reduce API calls on uncertain grading

5. **Caching (future feature):**
   - Cache identical answers to avoid re-grading

### Monitoring Usage

**Laravel logs:**
```bash
grep "Essay graded by AI" storage/logs/laravel.log | wc -l
```

**Provider dashboards:**
- OpenAI: [https://platform.openai.com/usage](https://platform.openai.com/usage)
- Claude: [https://console.anthropic.com/account/billing](https://console.anthropic.com/account/billing)
- Gemini: [https://console.cloud.google.com/apis/api/generativelanguage.googleapis.com/quotas](https://console.cloud.google.com/apis/api/generativelanguage.googleapis.com/quotas)

---

## Architecture

### Files Modified

1. **config/ai.php** - AI configuration
2. **app/Services/EssayGradingService.php** - Core grading service
3. **app/Http/Controllers/Api/ExamController.php** - Integration point
4. **database/migrations/2025_11_01_000001_add_ai_feedback_to_exam_answers_table.php** - Database schema

### Class: EssayGradingService

**Location:** `app/Services/EssayGradingService.php`

**Key Methods:**
```php
public function gradeEssay(
    string $question,
    ?string $rubric,
    string $studentAnswer,
    int $maxPoints
): array
```

Returns:
```php
[
    'points_earned' => 7.5,      // Score awarded
    'is_correct' => true,         // Pass/fail (>= 50%)
    'feedback' => 'Good work...', // Explanation
    'confidence' => 85            // AI confidence (0-100)
]
```

**Static Method:**
```php
public static function isAvailable(): bool
```

Checks if AI grading is configured and enabled.

---

## Security Considerations

1. **API Key Protection:**
   - Never commit `.env` files
   - Use environment variables on production servers
   - Rotate keys periodically

2. **Input Validation:**
   - All student answers are sanitized before sending to AI
   - No executable code is sent to AI

3. **Output Validation:**
   - AI responses are parsed and validated
   - Scores capped at maximum points
   - Malformed responses trigger fallback

4. **Rate Limiting:**
   - Prevent abuse of AI API
   - Configurable request limits

5. **Logging:**
   - All AI requests logged (without API keys)
   - Audit trail for grading decisions

---

## Future Enhancements

Possible improvements:
- [ ] Plagiarism detection
- [ ] Multi-language support
- [ ] Instructor feedback integration (AI learns from corrections)
- [ ] Batch grading API
- [ ] Answer similarity clustering
- [ ] Real-time grading preview for instructors
- [ ] Custom rubric templates
- [ ] AI-suggested improvements for student essays
- [ ] Analytics dashboard for AI grading accuracy

---

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review this documentation
3. Check provider status pages:
   - [OpenAI Status](https://status.openai.com/)
   - [Anthropic Status](https://status.anthropic.com/)
   - [Google Cloud Status](https://status.cloud.google.com/)

---

## License

This AI integration is part of the Exam-in-Ease system and follows the same license terms.

---

**Last Updated:** 2025-11-01
**Version:** 1.0.0
