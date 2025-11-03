# PowerShell Script to Test Rate Limiting with Concurrent Users
# This script simulates multiple concurrent users hitting various endpoints to verify rate limits work correctly

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Rate Limiting Concurrent User Test Script" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$baseUrl = "http://localhost/exam1/public"
$apiUrl = "$baseUrl/api"
$webUrl = "$baseUrl"

# Test counters
$totalTests = 0
$passedTests = 0
$failedTests = 0

function Test-RateLimit {
    param(
        [string]$endpoint,
        [string]$method = "GET",
        [int]$limit,
        [int]$extraRequests = 5,
        [hashtable]$headers = @{},
        [string]$body = $null
    )
    
    $totalTests++
    Write-Host "Testing: $method $endpoint (Limit: $limit/min)" -ForegroundColor Yellow
    
    $successCount = 0
    $rateLimitCount = 0
    
    # Send requests up to limit + extra
    for ($i = 1; $i -le ($limit + $extraRequests); $i++) {
        try {
            $params = @{
                Uri = $endpoint
                Method = $method
                Headers = $headers
                TimeoutSec = 5
                ErrorAction = 'SilentlyContinue'
            }
            
            if ($body -and ($method -eq "POST" -or $method -eq "PUT" -or $method -eq "DELETE")) {
                $params.Body = $body
            }
            
            $response = Invoke-WebRequest @params
            
            if ($response.StatusCode -eq 200 -or $response.StatusCode -eq 201) {
                $successCount++
            }
        }
        catch {
            # Check if it's a 429 (Too Many Requests) error
            if ($_.Exception.Response.StatusCode.value__ -eq 429) {
                $rateLimitCount++
            }
        }
        
        # Small delay to avoid overwhelming the server
        Start-Sleep -Milliseconds 50
    }
    
    Write-Host "  Results: $successCount successful, $rateLimitCount rate-limited" -ForegroundColor Gray
    
    # Verify rate limit was triggered
    if ($rateLimitCount -gt 0 -and $successCount -le $limit) {
        Write-Host "  ✓ PASS - Rate limit enforced correctly" -ForegroundColor Green
        $script:passedTests++
    }
    else {
        Write-Host "  ✗ FAIL - Rate limit not working as expected" -ForegroundColor Red
        $script:failedTests++
    }
    
    Write-Host ""
    
    # Wait before next test to reset rate limit window
    Start-Sleep -Seconds 2
}

function Test-ConcurrentRequests {
    param(
        [string]$endpoint,
        [string]$method = "GET",
        [int]$concurrentUsers = 5,
        [int]$requestsPerUser = 15,
        [hashtable]$headers = @{}
    )
    
    $totalTests++
    Write-Host "Testing Concurrent Users: $method $endpoint" -ForegroundColor Yellow
    Write-Host "  $concurrentUsers users x $requestsPerUser requests each" -ForegroundColor Gray
    
    # Create script block for each user
    $scriptBlock = {
        param($url, $method, $requests, $headers)
        
        $success = 0
        $rateLimited = 0
        
        for ($i = 1; $i -le $requests; $i++) {
            try {
                $params = @{
                    Uri = $url
                    Method = $method
                    Headers = $headers
                    TimeoutSec = 5
                    ErrorAction = 'SilentlyContinue'
                }
                
                $response = Invoke-WebRequest @params
                if ($response.StatusCode -eq 200 -or $response.StatusCode -eq 201) {
                    $success++
                }
            }
            catch {
                if ($_.Exception.Response.StatusCode.value__ -eq 429) {
                    $rateLimited++
                }
            }
            Start-Sleep -Milliseconds 50
        }
        
        return @{ Success = $success; RateLimited = $rateLimited }
    }
    
    # Start concurrent jobs
    $jobs = @()
    for ($i = 1; $i -le $concurrentUsers; $i++) {
        $jobs += Start-Job -ScriptBlock $scriptBlock -ArgumentList $endpoint, $method, $requestsPerUser, $headers
    }
    
    # Wait for all jobs to complete
    $jobs | Wait-Job | Out-Null
    
    # Collect results
    $totalSuccess = 0
    $totalRateLimited = 0
    
    foreach ($job in $jobs) {
        $result = Receive-Job -Job $job
        $totalSuccess += $result.Success
        $totalRateLimited += $result.RateLimited
        Remove-Job -Job $job
    }
    
    Write-Host "  Results: $totalSuccess successful, $totalRateLimited rate-limited" -ForegroundColor Gray
    
    if ($totalRateLimited -gt 0) {
        Write-Host "  ✓ PASS - Rate limit enforced under concurrent load" -ForegroundColor Green
        $script:passedTests++
    }
    else {
        Write-Host "  ✗ FAIL - No rate limiting detected" -ForegroundColor Red
        $script:failedTests++
    }
    
    Write-Host ""
    Start-Sleep -Seconds 2
}

# ============================================
# TEST 1: Mobile API - Health Check (60/min)
# ============================================
Write-Host "=== TEST 1: Mobile API - Health Check ===" -ForegroundColor Cyan
Test-RateLimit -endpoint "$apiUrl/health" -method "GET" -limit 60

# ============================================
# TEST 2: Mobile API - Login (10/min)
# ============================================
Write-Host "=== TEST 2: Mobile API - Login ===" -ForegroundColor Cyan
$loginBody = @{
    email = "test@example.com"
    password = "password123"
} | ConvertTo-Json

$loginHeaders = @{
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

Test-RateLimit -endpoint "$apiUrl/login" -method "POST" -limit 10 -headers $loginHeaders -body $loginBody

# ============================================
# TEST 3: Concurrent Users - Health Check
# ============================================
Write-Host "=== TEST 3: Concurrent Users - Health Check ===" -ForegroundColor Cyan
Test-ConcurrentRequests -endpoint "$apiUrl/health" -method "GET" -concurrentUsers 5 -requestsPerUser 15

# ============================================
# TEST 4: Admin Route - User Import (5/min)
# ============================================
Write-Host "=== TEST 4: Admin Route - User Import (Critical) ===" -ForegroundColor Cyan
# Note: This will fail without authentication, but we can test rate limiting still works
Test-RateLimit -endpoint "$webUrl/admin/users/import" -method "POST" -limit 5

# ============================================
# TEST 5: Instructor Route - Exam Create (20/min)
# ============================================
Write-Host "=== TEST 5: Instructor Route - Exam Create ===" -ForegroundColor Cyan
# Note: This will fail without authentication, but we can test rate limiting still works
Test-RateLimit -endpoint "$webUrl/instructor/exams" -method "POST" -limit 20

# ============================================
# TEST 6: Concurrent Users - Multiple Endpoints
# ============================================
Write-Host "=== TEST 6: Concurrent Users - Mixed Load ===" -ForegroundColor Cyan
Write-Host "Simulating 10 concurrent users hitting health check..." -ForegroundColor Gray
Test-ConcurrentRequests -endpoint "$apiUrl/health" -method "GET" -concurrentUsers 10 -requestsPerUser 10

# ============================================
# SUMMARY
# ============================================
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "TEST SUMMARY" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Total Tests: $totalTests" -ForegroundColor White
Write-Host "Passed: $passedTests" -ForegroundColor Green
Write-Host "Failed: $failedTests" -ForegroundColor Red

if ($failedTests -eq 0) {
    Write-Host "`n✓ ALL TESTS PASSED - Rate limiting is working correctly!" -ForegroundColor Green
}
else {
    Write-Host "`n✗ SOME TESTS FAILED - Please review rate limit configuration" -ForegroundColor Red
}

Write-Host "`nNote: Some tests may show authentication errors (401/403), but we're" -ForegroundColor Yellow
Write-Host "testing if rate limits (429 errors) are enforced correctly." -ForegroundColor Yellow
Write-Host ""
