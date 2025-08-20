<?php

/**
 * Users API Test Script
 * 
 * This script tests all the Users API endpoints:
 * - Public read endpoints (no auth required)
 * - Protected write endpoints (auth required)
 * - Custom endpoints (active, inactive, by role, activate, deactivate)
 */

$baseUrl = 'http://localhost:8000/api';
$token = null;

// Colors for output
$colors = [
    'success' => "\033[32m",
    'error' => "\033[31m",
    'info' => "\033[34m",
    'warning' => "\033[33m",
    'reset' => "\033[0m"
];

function printResult($message, $type = 'info') {
    global $colors;
    echo $colors[$type] . $message . $colors['reset'] . "\n";
}

function makeApiCall($url, $method = 'GET', $data = null, $headers = []) {
    global $token;
    
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($token) {
        $defaultHeaders[] = "Authorization: Bearer {$token}";
    }
    
    $headers = array_merge($defaultHeaders, $headers);
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => 0];
    }
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'data' => json_decode($response, true)
    ];
}

function login() {
    global $baseUrl, $token;
    
    printResult("🔐 Logging in...", 'info');
    
    $loginData = [
        'email' => 'test@example.com',
        'password' => 'password'
    ];
    
    $result = makeApiCall("{$baseUrl}/login", 'POST', $loginData);
    
    if ($result['http_code'] === 200 && isset($result['data']['token'])) {
        $token = $result['data']['token'];
        printResult("✅ Login successful! Token: " . substr($token, 0, 20) . "...", 'success');
        return true;
    } else {
        printResult("❌ Login failed: " . ($result['data']['message'] ?? 'Unknown error'), 'error');
        return false;
    }
}

function testPublicEndpoints() {
    global $baseUrl;
    
    printResult("\n📖 Testing Public Read Endpoints (No Auth Required)", 'info');
    
    // Test GET /api/users
    printResult("Testing GET /api/users...", 'info');
    $result = makeApiCall("{$baseUrl}/users");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/users successful", 'success');
        if (isset($result['data']['data'])) {
            printResult("   Found " . count($result['data']['data']) . " users", 'info');
        }
    } else {
        printResult("❌ GET /api/users failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/users/active
    printResult("Testing GET /api/users/active...", 'info');
    $result = makeApiCall("{$baseUrl}/users/active");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/users/active successful", 'success');
    } else {
        printResult("❌ GET /api/users/active failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/users/inactive
    printResult("Testing GET /api/users/inactive...", 'info');
    $result = makeApiCall("{$baseUrl}/users/inactive");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/users/inactive successful", 'success');
    } else {
        printResult("❌ GET /api/users/inactive failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/users/role/1
    printResult("Testing GET /api/users/role/1...", 'info');
    $result = makeApiCall("{$baseUrl}/users/role/1");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/users/role/1 successful", 'success');
    } else {
        printResult("❌ GET /api/users/role/1 failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/users/{id}
    printResult("Testing GET /api/users/1...", 'info');
    $result = makeApiCall("{$baseUrl}/users/1");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/users/1 successful", 'success');
        if (isset($result['data']['data']['name'])) {
            printResult("   User name: " . $result['data']['data']['name'], 'info');
        }
    } else {
        printResult("❌ GET /api/users/1 failed: HTTP {$result['http_code']}", 'error');
    }
}

function testProtectedEndpoints() {
    global $baseUrl;
    
    printResult("\n🔒 Testing Protected Write Endpoints (Auth Required)", 'info');
    
    // Test POST /api/users (create)
    printResult("Testing POST /api/users (create)...", 'info');
    $userData = [
        'name' => 'Test User ' . time(),
        'email' => 'testuser' . time() . '@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => 2, // employee role
        'is_active' => true
    ];
    
    $result = makeApiCall("{$baseUrl}/users", 'POST', $userData);
    if ($result['http_code'] === 201) {
        printResult("✅ POST /api/users successful", 'success');
        $userId = $result['data']['data']['id'];
        
        // Test PUT /api/users/{id} (update)
        printResult("Testing PUT /api/users/{$userId} (update)...", 'info');
        $updateData = [
            'name' => 'Updated Test User',
            'is_active' => true
        ];
        
        $updateResult = makeApiCall("{$baseUrl}/users/{$userId}", 'PUT', $updateData);
        if ($updateResult['http_code'] === 200) {
            printResult("✅ PUT /api/users/{$userId} successful", 'success');
        } else {
            printResult("❌ PUT /api/users/{$userId} failed: HTTP {$updateResult['http_code']}", 'error');
        }
        
        // Test POST /api/users/{id}/deactivate
        printResult("Testing POST /api/users/{$userId}/deactivate...", 'info');
        $deactivateResult = makeApiCall("{$baseUrl}/users/{$userId}/deactivate", 'POST');
        if ($deactivateResult['http_code'] === 200) {
            printResult("✅ POST /api/users/{$userId}/deactivate successful", 'success');
        } else {
            printResult("❌ POST /api/users/{$userId}/deactivate failed: HTTP {$deactivateResult['http_code']}", 'error');
        }
        
        // Test POST /api/users/{id}/activate
        printResult("Testing POST /api/users/{$userId}/activate...", 'info');
        $activateResult = makeApiCall("{$baseUrl}/users/{$userId}/activate", 'POST');
        if ($activateResult['http_code'] === 200) {
            printResult("✅ POST /api/users/{$userId}/activate successful", 'success');
        } else {
            printResult("❌ POST /api/users/{$userId}/activate failed: HTTP {$activateResult['http_code']}", 'error');
        }
        
        // Test DELETE /api/users/{id}
        printResult("Testing DELETE /api/users/{$userId}...", 'info');
        $deleteResult = makeApiCall("{$baseUrl}/users/{$userId}", 'DELETE');
        if ($deleteResult['http_code'] === 200) {
            printResult("✅ DELETE /api/users/{$userId} successful", 'success');
        } else {
            printResult("❌ DELETE /api/users/{$userId} failed: HTTP {$deleteResult['http_code']}", 'error');
        }
        
    } else {
        printResult("❌ POST /api/users failed: HTTP {$result['http_code']}", 'error');
        if (isset($result['data']['message'])) {
            printResult("   Error: " . $result['data']['message'], 'error');
        }
    }
}

function testSearchAndFiltering() {
    global $baseUrl;
    
    printResult("\n🔍 Testing Search and Filtering", 'info');
    
    // Test search
    printResult("Testing search with 'test'...", 'info');
    $result = makeApiCall("{$baseUrl}/users?search=test");
    if ($result['http_code'] === 200) {
        printResult("✅ Search successful", 'success');
    } else {
        printResult("❌ Search failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test filtering by status
    printResult("Testing filter by active status...", 'info');
    $result = makeApiCall("{$baseUrl}/users?is_active=1");
    if ($result['http_code'] === 200) {
        printResult("✅ Filter by active status successful", 'success');
    } else {
        printResult("❌ Filter by active status failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test filtering by role
    printResult("Testing filter by role...", 'info');
    $result = makeApiCall("{$baseUrl}/users?role_id=1");
    if ($result['http_code'] === 200) {
        printResult("✅ Filter by role successful", 'success');
    } else {
        printResult("❌ Filter by role failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test sorting
    printResult("Testing sorting by name...", 'info');
    $result = makeApiCall("{$baseUrl}/users?sort_by=name&sort_order=asc");
    if ($result['http_code'] === 200) {
        printResult("✅ Sorting successful", 'success');
    } else {
        printResult("❌ Sorting failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test pagination
    printResult("Testing pagination...", 'info');
    $result = makeApiCall("{$baseUrl}/users?page=1&per_page=5");
    if ($result['http_code'] === 200) {
        printResult("✅ Pagination successful", 'success');
        if (isset($result['data']['meta'])) {
            $meta = $result['data']['meta'];
            printResult("   Page {$meta['current_page']} of {$meta['last_page']} (Total: {$meta['total']})", 'info');
        }
    } else {
        printResult("❌ Pagination failed: HTTP {$result['http_code']}", 'error');
    }
}

function testValidationErrors() {
    global $baseUrl;
    
    printResult("\n⚠️ Testing Validation Errors", 'info');
    
    // Test creating user without required fields
    printResult("Testing POST /api/users with missing required fields...", 'info');
    $invalidData = [
        'email' => 'invalid-email'
    ];
    
    $result = makeApiCall("{$baseUrl}/users", 'POST', $invalidData);
    if ($result['http_code'] === 422) {
        printResult("✅ Validation error correctly returned (HTTP 422)", 'success');
        if (isset($result['data']['errors'])) {
            printResult("   Validation errors: " . implode(', ', array_keys($result['data']['errors'])), 'info');
        }
    } else {
        printResult("❌ Expected validation error, got HTTP {$result['http_code']}", 'error');
    }
    
    // Test creating user with duplicate email
    printResult("Testing POST /api/users with duplicate email...", 'info');
    $duplicateData = [
        'name' => 'Duplicate User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];
    
    $result = makeApiCall("{$baseUrl}/users", 'POST', $duplicateData);
    if ($result['http_code'] === 422) {
        printResult("✅ Duplicate email validation error correctly returned (HTTP 422)", 'success');
    } else {
        printResult("❌ Expected duplicate email error, got HTTP {$result['http_code']}", 'error');
    }
}

function testUnauthenticatedAccess() {
    global $baseUrl;
    
    printResult("\n🚫 Testing Unauthenticated Access to Protected Endpoints", 'info');
    
    // Reset token to test unauthenticated access
    global $token;
    $token = null;
    
    $protectedEndpoints = [
        'POST /api/users' => ['POST', "{$baseUrl}/users", ['name' => 'Test', 'email' => 'test@test.com', 'password' => 'password', 'password_confirmation' => 'password']],
        'PUT /api/users/1' => ['PUT', "{$baseUrl}/users/1", ['name' => 'Updated']],
        'DELETE /api/users/1' => ['DELETE', "{$baseUrl}/users/1"],
        'POST /api/users/1/activate' => ['POST', "{$baseUrl}/users/1/activate"],
        'POST /api/users/1/deactivate' => ['POST', "{$baseUrl}/users/1/deactivate"],
    ];
    
    foreach ($protectedEndpoints as $endpoint => $params) {
        $method = $params[0];
        $url = $params[1];
        $data = $params[2] ?? null;
        
        printResult("Testing {$endpoint} without auth...", 'info');
        $result = makeApiCall($url, $method, $data);
        
        if ($result['http_code'] === 401) {
            printResult("✅ {$endpoint} correctly requires authentication (HTTP 401)", 'success');
        } else {
            printResult("❌ {$endpoint} should require authentication, got HTTP {$result['http_code']}", 'error');
        }
    }
}

// Main test execution
printResult("🚀 Starting Users API Test Suite", 'info');
printResult("Base URL: {$baseUrl}", 'info');

// Run tests
if (login()) {
    testPublicEndpoints();
    testProtectedEndpoints();
    testSearchAndFiltering();
    testValidationErrors();
    testUnauthenticatedAccess();
    
    printResult("\n🎉 All tests completed!", 'success');
} else {
    printResult("❌ Cannot proceed without authentication", 'error');
}

printResult("\n📝 Test Summary:", 'info');
printResult("- Public endpoints: GET /api/users, /api/users/{id}, /api/users/active, /api/users/inactive, /api/users/role/{roleId}", 'info');
printResult("- Protected endpoints: POST, PUT, DELETE /api/users, /api/users/{id}/activate, /api/users/{id}/deactivate", 'info');
printResult("- Features: Search, filtering by status/role, sorting, pagination, validation", 'info');
printResult("- Authentication: Bearer token required for write operations", 'info');
printResult("- Security: Password hashing, role validation, admin protection", 'info');
