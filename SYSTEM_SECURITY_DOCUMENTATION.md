# 🔒 SYSTEM-WIDE SECURITY DOCUMENTATION

## Table of Contents
1. [Overview](#overview)
2. [Rate Limiting Strategy](#rate-limiting-strategy)
3. [Input Validation](#input-validation)
4. [Authorization & Access Control](#authorization--access-control)
5. [Database Security](#database-security)
6. [Concurrency & Race Conditions](#concurrency--race-conditions)
7. [Error Handling](#error-handling)
8. [Audit Logging](#audit-logging)
9. [Implementation Checklist](#implementation-checklist)
10. [Testing Guide](#testing-guide)

---

## Overview

This document outlines the comprehensive security measures implemented across the **Exam-in-Ease** system to protect against common vulnerabilities and ensure safe multi-user concurrent operations.

### Security Principles Applied
- **Defense in Depth**: Multiple layers of security
- **Least Privilege**: Minimal access rights
- **Fail Secure**: Deny access on error
- **Audit Trail**: Log all critical operations
- **Input Validation**: Never trust user input

---

## Rate Limiting Strategy

### Custom Rate Limit Categories

#### 1. **API Read Operations** (`api-read`)
- **Limit**: 100 requests/minute (authenticated), 20/min (guest)
- **Applies to**: GET requests for data retrieval
- **Examples**: View statistics, list exams, get questions
- **Rationale**: High volume needed for dashboard loading

#### 2. **API Write Operations** (`api-write`)
- **Limit**: 30 requests/minute (authenticated), 10/min (guest)
- **Applies to**: POST/PUT requests for data modification
- **Examples**: Add question, update exam, create class
- **Rationale**: Prevents spam but allows normal editing

#### 3. **API Delete Operations** (`api-delete`)
- **Limit**: 20 requests/minute (authenticated), 5/min (guest)
- **Applies to**: DELETE requests
- **Examples**: Delete question, remove student, destroy exam
- **Rationale**: Destructive operations need stricter control

#### 4. **API Critical Operations** (`api-critical`)
- **Limit**: 10 requests/minute (authenticated), 3/min (guest)
- **Applies to**: High-risk operations
- **Examples**: Override answer, reset password, approve exam
- **Rationale**: Potential for abuse or data corruption

#### 5. **API Expensive Operations** (`api-expensive`)
- **Limit**: 5 requests/minute (authenticated), 2/min (guest)
- **Applies to**: Resource-intensive operations
- **Examples**: Download Excel, generate PDF, export data
- **Rationale**: Prevents server resource exhaustion

#### 6. **API Upload Operations** (`api-upload`)
- **Limit**: 5 requests/minute (authenticated), 2/min (guest)
- **Applies to**: File uploads/imports
- **Examples**: Import users CSV, upload template
- **Rationale**: Large file processing is expensive

#### 7. **API Search/Filter Operations** (`api-search`)
- **Limit**: 60 requests/minute (authenticated), 15/min (guest)
- **Applies to**: Search and filter operations
- **Examples**: Filter statistics, search teachers
- **Rationale**: Common operation but can be query-intensive

### Route-Specific Rate Limits

```php
// INSTRUCTOR ROUTES
Route::get('/exams-statistics/{id}/filter')
    ->middleware('throttle:60,1')  // 60 requests/minute

Route::post('/exams-statistics/{id}/answer/{answerId}/override')
    ->middleware('throttle:30,1')  // 30 requests/minute

Route::delete('/exams-statistics/{id}/attempt/{attemptId}')
    ->middleware('throttle:20,1')  // 20 requests/minute

Route::get('/exams-statistics/{id}/download')
    ->middleware('throttle:10,1')  // 10 requests/minute (expensive)
```

### Testing Rate Limits

```bash
# Test rate limiting with curl
for i in {1..65}; do
  curl -X GET "http://localhost/instructor/exams-statistics/1/filter?class_id=all" \
    -H "Cookie: laravel_session=YOUR_SESSION" \
    -w "\n%{http_code}\n"
done

# Expected: First 60 succeed (200), 61st onwards fail (429 Too Many Requests)
```

---

## Input Validation

### Validation Rules Applied

#### 1. **Exam Statistics API**

```php
// getFilteredStats(), getQuestionStats(), getIndividualStats()
$validated = $request->validate([
    'class_id' => 'nullable|string|max:255'
]);

// Numeric ID validation
if (!is_numeric($examId)) {
    return response()->json(['error' => 'Invalid exam ID'], 400);
}
```

#### 2. **Score Distribution API**

```php
$validated = $request->validate([
    'class_id' => 'nullable|string|max:255'
]);

// Total points validation
if (!$totalPoints || $totalPoints <= 0) {
    // Return empty statistics
}

// Cap extreme values
if ($totalPoints > 10000) {
    $totalPoints = 10000;
}

// Score validation
if (!is_numeric($score) || $score < 0 || $score > $totalPoints) {
    continue; // Skip invalid scores
}
```

#### 3. **Answer Override API**

```php
$validated = $request->validate([
    'is_correct' => 'required|boolean',
    'points_earned' => 'required|numeric|min:0'
]);

// Validate points don't exceed maximum
if ($validated['points_earned'] > $item->points_awarded) {
    return response()->json([
        'error' => 'Points earned cannot exceed maximum points'
    ], 422);
}
```

### Input Sanitization Checklist

- [x] All user inputs validated with Laravel validation
- [x] Numeric IDs type-checked with `is_numeric()`
- [x] String inputs limited to max length
- [x] Boolean values strictly typed
- [x] Array inputs validated structure
- [x] File uploads checked mime types (where applicable)
- [x] SQL injection prevented via Eloquent/Query Builder
- [x] XSS prevented via Blade escaping `{{ }}`

---

## Authorization & Access Control

### Multi-Level Authorization

#### 1. **Route-Level Authorization**
```php
Route::middleware(['auth', 'can:instructor-access'])
```

#### 2. **Owner Verification**
```php
$exam = Exam::where('exam_id', $examId)
    ->where('teacher_id', Auth::id())  // Ownership check
    ->firstOrFail();
```

#### 3. **Resource Relationship Verification**
```php
// Verify class belongs to exam
$validClass = $exam->examAssignments()
    ->where('class_id', $classId)
    ->exists();

if (!$validClass) {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

#### 4. **Cross-User Protection**
- Instructor A cannot access Instructor B's exams
- Instructor A cannot access Instructor B's class data
- Students cannot access other students' attempts

### Authorization Vulnerabilities Fixed

| **Endpoint** | **Vulnerability** | **Fix** |
|--------------|-------------------|---------|
| `getFilteredStats()` | Class ID manipulation | Verify class belongs to exam |
| `getQuestionStats()` | Class ID manipulation | Verify class belongs to exam |
| `getIndividualStats()` | Class ID manipulation | Verify class belongs to exam |
| `getScoreDistribution()` | Class ID manipulation | Verify class belongs to exam |
| `overrideAnswer()` | Answer ID manipulation | Verify answer belongs to exam |

---

## Database Security

### Transaction Management

#### Race Condition Prevention

**Problem**: Two instructors modifying same answer simultaneously

**Solution**: Database row locking

```php
DB::transaction(function () use ($examId, $answerId, $validated) {
    $exam = Exam::where('exam_id', $examId)
        ->where('teacher_id', Auth::id())
        ->lockForUpdate()  // 🔒 Lock exam row
        ->firstOrFail();
    
    $answer = ExamAnswer::where('answer_id', $answerId)
        ->lockForUpdate()  // 🔒 Lock answer row
        ->firstOrFail();
    
    // Make modifications...
    $answer->save();
    
    // Locks released on commit
});
```

#### Recommended Database Indexes

```sql
-- Exam Statistics Performance
CREATE INDEX idx_exam_attempts_assignment_status 
  ON exam_attempts(exam_assignment_id, status);

CREATE INDEX idx_exam_answers_attempt_item 
  ON exam_answers(attempt_id, item_id);

CREATE INDEX idx_exams_teacher_status 
  ON exams(teacher_id, status);

CREATE INDEX idx_exam_assignments_exam_class 
  ON exam_assignments(exam_id, class_id);

CREATE INDEX idx_exam_items_exam_order 
  ON exam_items(exam_id, `order`);
```

### Query Optimization

#### N+1 Query Problem

**Before** (Makes 100+ queries):
```php
foreach ($assignments as $assignment) {
    $totalStudents += $assignment->class->students()->count();
}
```

**After** (Makes 2-3 queries):
```php
$totalStudents = $assignments->load('class.students')
    ->sum(function($assignment) {
        return $assignment->class->students->count();
    });
```

---

## Concurrency & Race Conditions

### Critical Sections Identified

1. **Answer Override** ✅ FIXED
   - Two instructors overriding same answer
   - Solution: Row locking with `lockForUpdate()`

2. **Score Recalculation** ✅ FIXED
   - Score updated while being read
   - Solution: Transaction with locks

3. **User Import** ⚠️ NEEDS FIX
   - Multiple admins importing users simultaneously
   - Recommendation: Add transaction + unique constraint

4. **Class Student Management** ⚠️ NEEDS FIX
   - Adding/removing students concurrently
   - Recommendation: Add transaction wrapper

### Deadlock Prevention

- Acquire locks in consistent order (Exam → Answer → Attempt)
- Keep transactions short
- Use appropriate isolation level
- Implement timeout handling

---

## Error Handling

### Error Response Strategy

#### 1. **Don't Leak Sensitive Information**

**Bad**:
```php
catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
    // ❌ Exposes: "SQLSTATE[42S02]: Table exam_attempts not found"
}
```

**Good**:
```php
catch (Exception $e) {
    \Log::error('Error in getScoreDistribution: ' . $e->getMessage(), [
        'exam_id' => $examId,
        'user_id' => Auth::id(),
        'trace' => $e->getTraceAsString()
    ]);
    
    return response()->json([
        'error' => 'Failed to fetch score distribution'
    ], 500);
}
```

#### 2. **Specific Exception Handling**

```php
try {
    // Operations...
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    return response()->json(['error' => 'Exam not found'], 404);
} catch (\Illuminate\Validation\ValidationException $e) {
    return response()->json(['error' => 'Invalid input', 'details' => $e->errors()], 422);
} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    return response()->json(['error' => 'Unauthorized access'], 403);
} catch (\Exception $e) {
    \Log::error('Unexpected error', ['exception' => $e]);
    return response()->json(['error' => 'An error occurred'], 500);
}
```

---

## Audit Logging

### What to Log

#### Critical Operations
```php
\Log::info('Answer override', [
    'exam_id' => $examId,
    'answer_id' => $answerId,
    'old_correct' => $answer->is_correct,
    'new_correct' => $validated['is_correct'],
    'old_points' => $answer->points_earned,
    'new_points' => $validated['points_earned'],
    'instructor_id' => Auth::id(),
    'student_id' => $answer->attempt->student_id,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now()
]);
```

#### Security Events
```php
\Log::warning('Unauthorized access attempt', [
    'user_id' => Auth::id(),
    'exam_id' => $examId,
    'class_id' => $classId,
    'ip' => $request->ip(),
    'route' => $request->path()
]);
```

#### Error Events
```php
\Log::error('Error in getFilteredStats', [
    'exam_id' => $id,
    'class_id' => $classId,
    'instructor_id' => Auth::id(),
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

### Log Rotation

Configure in `config/logging.php`:
```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 30, // Keep logs for 30 days
],
```

---

## Implementation Checklist

### ✅ Completed

- [x] Rate limiting on exam statistics endpoints
- [x] Input validation on all API endpoints
- [x] Authorization checks (owner verification)
- [x] Class ownership verification
- [x] Database transactions with row locking
- [x] Error handling without information leakage
- [x] Audit logging for critical operations
- [x] Type checking for numeric IDs
- [x] Business logic validation (max points)
- [x] CSRF protection (via Laravel auth middleware)

### ⚠️ High Priority Remaining

- [ ] Add rate limiting to ALL Admin routes
- [ ] Add rate limiting to ALL ProgramChair routes
- [ ] Add rate limiting to ALL Instructor routes
- [ ] Fix N+1 queries in getFilteredStats()
- [ ] Add transactions to user import
- [ ] Add transactions to class student management
- [ ] Implement database indexes
- [ ] Add request size limits
- [ ] Configure HTTPS enforcement (production)

### 🟡 Medium Priority Remaining

- [ ] Implement query result caching
- [ ] Add API response caching
- [ ] Set up monitoring/alerting
- [ ] Configure session timeout
- [ ] Add Content Security Policy headers
- [ ] Implement API versioning
- [ ] Add database connection pooling

### 🟢 Low Priority Recommendations

- [ ] Penetration testing
- [ ] Security audit
- [ ] Load testing
- [ ] Performance profiling
- [ ] Documentation update
- [ ] Training materials for instructors

---

## Testing Guide

### 1. Rate Limit Testing

```bash
# Test API endpoint rate limiting
for i in {1..65}; do
  curl -X GET "http://localhost/api/endpoint" \
    -H "Cookie: session=..." \
    -w "Request $i: %{http_code}\n"
done
```

### 2. Authorization Testing

```bash
# Test cross-user access
# Login as Instructor A
curl -X GET "http://localhost/instructor/exams-statistics/123/filter?class_id=999" \
  -H "Cookie: session_instructor_a=..."

# Expected: 403 Forbidden (if class 999 doesn't belong to Instructor A)
```

### 3. Concurrency Testing

```php
// PHPUnit test for race conditions
public function test_concurrent_answer_override()
{
    // Create exam and answer
    $exam = Exam::factory()->create();
    $answer = ExamAnswer::factory()->create();
    
    // Simulate 2 concurrent requests
    $promise1 = async(fn() => $this->overrideAnswer($answer->id, ['points_earned' => 10]));
    $promise2 = async(fn() => $this->overrideAnswer($answer->id, ['points_earned' => 15]));
    
    [$result1, $result2] = await([$promise1, $promise2]);
    
    // Verify final score is either 10 or 15, not corrupted
    $answer->refresh();
    $this->assertContains($answer->points_earned, [10, 15]);
}
```

### 4. Input Validation Testing

```bash
# Test invalid exam ID
curl -X GET "http://localhost/instructor/exams-statistics/abc/filter"
# Expected: 400 Bad Request

# Test SQL injection attempt
curl -X GET "http://localhost/instructor/exams-statistics/123/filter?class_id=' OR '1'='1"
# Expected: 403 Forbidden or validation error
```

---

## Security Monitoring

### Metrics to Track

1. **Failed Authorization Attempts**
   - Count per user
   - Alert if > 10/hour

2. **Rate Limit Violations**
   - Track IPs hitting limits
   - Alert if same IP hits multiple endpoints

3. **Database Query Time**
   - Alert if avg query > 1 second
   - Indicates N+1 or missing indexes

4. **Error Rate**
   - Track 5xx errors
   - Alert if > 1% of requests

5. **Unusual Patterns**
   - Same user accessing 100+ exams in short time
   - Rapid creation/deletion cycles

### Recommended Tools

- **Laravel Telescope**: Local development debugging
- **Sentry**: Error tracking and monitoring
- **New Relic / Datadog**: Application performance monitoring
- **CloudFlare**: DDoS protection
- **Fail2Ban**: IP blocking for repeated failures

---

## Contact & Support

For security concerns or to report vulnerabilities:
- Email: security@example.com
- Bug Bounty: /security/bug-bounty

---

**Last Updated**: November 3, 2025
**Version**: 1.0.0
**Maintained by**: Development Team
