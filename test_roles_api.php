<?php

/**
 * Roles API Test Script
 * 
 * This script tests all the Roles API endpoints:
 * - Public read endpoints (no auth required)
 * - Protected write endpoints (auth required)
 * - Custom endpoints (active, inactive, activate, deactivate)
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
    
    // Test GET /api/roles
    printResult("Testing GET /api/roles...", 'info');
    $result = makeApiCall("{$baseUrl}/roles");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/roles successful", 'success');
        if (isset($result['data']['data'])) {
            printResult("   Found " . count($result['data']['data']) . " roles", 'info');
        }
    } else {
        printResult("❌ GET /api/roles failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/roles/active
    printResult("Testing GET /api/roles/active...", 'info');
    $result = makeApiCall("{$baseUrl}/roles/active");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/roles/active successful", 'success');
    } else {
        printResult("❌ GET /api/roles/active failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/roles/inactive
    printResult("Testing GET /api/roles/inactive...", 'info');
    $result = makeApiCall("{$baseUrl}/roles/inactive");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/roles/inactive successful", 'success');
    } else {
        printResult("❌ GET /api/roles/inactive failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test GET /api/roles/{id}
    printResult("Testing GET /api/roles/1...", 'info');
    $result = makeApiCall("{$baseUrl}/roles/1");
    if ($result['http_code'] === 200) {
        printResult("✅ GET /api/roles/1 successful", 'success');
        if (isset($result['data']['data']['name'])) {
            printResult("   Role name: " . $result['data']['data']['name'], 'info');
        }
    } else {
        printResult("❌ GET /api/roles/1 failed: HTTP {$result['http_code']}", 'error');
    }
}

function testProtectedEndpoints() {
    global $baseUrl;
    
    printResult("\n🔒 Testing Protected Write Endpoints (Auth Required)", 'info');
    
    // Test POST /api/roles (create)
    printResult("Testing POST /api/roles (create)...", 'info');
    $roleData = [
        'name' => 'test_role_' . time(),
        'display_name' => 'Test Role',
        'description' => 'A test role created via API',
        'is_active' => true
    ];
    
    $result = makeApiCall("{$baseUrl}/roles", 'POST', $roleData);
    if ($result['http_code'] === 201) {
        printResult("✅ POST /api/roles successful", 'success');
        $roleId = $result['data']['data']['id'];
        
        // Test PUT /api/roles/{id} (update)
        printResult("Testing PUT /api/roles/{$roleId} (update)...", 'info');
        $updateData = [
            'display_name' => 'Updated Test Role',
            'description' => 'Updated description'
        ];
        
        $updateResult = makeApiCall("{$baseUrl}/roles/{$roleId}", 'PUT', $updateData);
        if ($updateResult['http_code'] === 200) {
            printResult("✅ PUT /api/roles/{$roleId} successful", 'success');
        } else {
            printResult("❌ PUT /api/roles/{$roleId} failed: HTTP {$updateResult['http_code']}", 'error');
        }
        
        // Test POST /api/roles/{id}/deactivate
        printResult("Testing POST /api/roles/{$roleId}/deactivate...", 'info');
        $deactivateResult = makeApiCall("{$baseUrl}/roles/{$roleId}/deactivate", 'POST');
        if ($deactivateResult['http_code'] === 200) {
            printResult("✅ POST /api/roles/{$roleId}/deactivate successful", 'success');
        } else {
            printResult("❌ POST /api/roles/{$roleId}/deactivate failed: HTTP {$deactivateResult['http_code']}", 'error');
        }
        
        // Test POST /api/roles/{id}/activate
        printResult("Testing POST /api/roles/{$roleId}/activate...", 'info');
        $activateResult = makeApiCall("{$baseUrl}/roles/{$roleId}/activate", 'POST');
        if ($activateResult['http_code'] === 200) {
            printResult("✅ POST /api/roles/{$roleId}/activate successful", 'success');
        } else {
            printResult("❌ POST /api/roles/{$roleId}/activate failed: HTTP {$activateResult['http_code']}", 'error');
        }
        
        // Test DELETE /api/roles/{id}
        printResult("Testing DELETE /api/roles/{$roleId}...", 'info');
        $deleteResult = makeApiCall("{$baseUrl}/roles/{$roleId}", 'DELETE');
        if ($deleteResult['http_code'] === 200) {
            printResult("✅ DELETE /api/roles/{$roleId} successful", 'success');
        } else {
            printResult("❌ DELETE /api/roles/{$roleId} failed: HTTP {$deleteResult['http_code']}", 'error');
        }
        
    } else {
        printResult("❌ POST /api/roles failed: HTTP {$result['http_code']}", 'error');
        if (isset($result['data']['message'])) {
            printResult("   Error: " . $result['data']['message'], 'error');
        }
    }
}

function testSearchAndFiltering() {
    global $baseUrl;
    
    printResult("\n🔍 Testing Search and Filtering", 'info');
    
    // Test search
    printResult("Testing search with 'admin'...", 'info');
    $result = makeApiCall("{$baseUrl}/roles?search=admin");
    if ($result['http_code'] === 200) {
        printResult("✅ Search successful", 'success');
    } else {
        printResult("❌ Search failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test filtering by status
    printResult("Testing filter by active status...", 'info');
    $result = makeApiCall("{$baseUrl}/roles?is_active=1");
    if ($result['http_code'] === 200) {
        printResult("✅ Filter by active status successful", 'success');
    } else {
        printResult("❌ Filter by active status failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test sorting
    printResult("Testing sorting by name...", 'info');
    $result = makeApiCall("{$baseUrl}/roles?sort_by=name&sort_order=asc");
    if ($result['http_code'] === 200) {
        printResult("✅ Sorting successful", 'success');
    } else {
        printResult("❌ Sorting failed: HTTP {$result['http_code']}", 'error');
    }
    
    // Test pagination
    printResult("Testing pagination...", 'info');
    $result = makeApiCall("{$baseUrl}/roles?page=1&per_page=5");
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
    
    // Test creating role without required fields
    printResult("Testing POST /api/roles with missing required fields...", 'info');
    $invalidData = [
        'description' => 'Only description provided'
    ];
    
    $result = makeApiCall("{$baseUrl}/roles", 'POST', $invalidData);
    if ($result['http_code'] === 422) {
        printResult("✅ Validation error correctly returned (HTTP 422)", 'success');
        if (isset($result['data']['errors'])) {
            printResult("   Validation errors: " . implode(', ', array_keys($result['data']['errors'])), 'info');
        }
    } else {
        printResult("❌ Expected validation error, got HTTP {$result['http_code']}", 'error');
    }
    
    // Test creating role with duplicate name
    printResult("Testing POST /api/roles with duplicate name...", 'info');
    $duplicateData = [
        'name' => 'admin',
        'display_name' => 'Another Admin'
    ];
    
    $result = makeApiCall("{$baseUrl}/roles", 'POST', $duplicateData);
    if ($result['http_code'] === 422) {
        printResult("✅ Duplicate name validation error correctly returned (HTTP 422)", 'success');
    } else {
        printResult("❌ Expected duplicate name error, got HTTP {$result['http_code']}", 'error');
    }
}

function testUnauthenticatedAccess() {
    global $baseUrl;
    
    printResult("\n🚫 Testing Unauthenticated Access to Protected Endpoints", 'info');
    
    // Reset token to test unauthenticated access
    global $token;
    $token = null;
    
    $protectedEndpoints = [
        'POST /api/roles' => ['POST', "{$baseUrl}/roles", ['name' => 'test', 'display_name' => 'Test']],
        'PUT /api/roles/1' => ['PUT', "{$baseUrl}/roles/1", ['display_name' => 'Updated']],
        'DELETE /api/roles/1' => ['DELETE', "{$baseUrl}/roles/1"],
        'POST /api/roles/1/activate' => ['POST', "{$baseUrl}/roles/1/activate"],
        'POST /api/roles/1/deactivate' => ['POST', "{$baseUrl}/roles/1/deactivate"],
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
printResult("🚀 Starting Roles API Test Suite", 'info');
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
printResult("- Public endpoints: GET /api/roles, /api/roles/{id}, /api/roles/active, /api/roles/inactive", 'info');
printResult("- Protected endpoints: POST, PUT, DELETE /api/roles, /api/roles/{id}/activate, /api/roles/{id}/deactivate", 'info');
printResult("- Features: Search, filtering, sorting, pagination, validation", 'info');
printResult("- Authentication: Bearer token required for write operations", 'info');
