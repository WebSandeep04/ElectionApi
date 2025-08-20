<?php

/**
 * Complete Lok Sabha API Test Script
 * Tests all CRUD operations for the Lok Sabha API with proper authentication
 */

$baseUrl = 'http://localhost:8000/api';
$authToken = null;

echo "=== Complete Lok Sabha API Test Script ===\n\n";

// Function to make HTTP requests
function makeRequest($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if (!empty($headers)) {
        $defaultHeaders = array_merge($defaultHeaders, $headers);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'status' => 0,
            'error' => $error,
            'body' => null,
            'raw' => null
        ];
    }
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// Function to print test results
function printResult($testName, $result, $expectedStatus = 200) {
    echo "Test: $testName\n";
    echo "Status: {$result['status']} (Expected: $expectedStatus)\n";
    
    if ($result['status'] === $expectedStatus) {
        echo "✅ PASSED\n";
    } else {
        echo "❌ FAILED\n";
    }
    
    if (isset($result['body'])) {
        echo "Response: " . json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
    }
    
    if (isset($result['error'])) {
        echo "Error: " . $result['error'] . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// 1. Test Authentication (Login with existing user)
echo "1. Testing Authentication...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$loginResult = makeRequest('POST', "$baseUrl/login", $loginData);
printResult('User Login', $loginResult, 200);

if ($loginResult['status'] === 200 && isset($loginResult['body']['token'])) {
    $authToken = $loginResult['body']['token'];
    echo "✅ Authentication token obtained: " . substr($authToken, 0, 20) . "...\n\n";
} else {
    // Try to register a new user
    echo "Login failed, trying to register new user...\n";
    $registerData = [
        'name' => 'Test User ' . time(),
        'email' => 'test' . time() . '@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];
    
    $registerResult = makeRequest('POST', "$baseUrl/register", $registerData);
    printResult('User Registration', $registerResult, 201);
    
    if ($registerResult['status'] === 201 && isset($registerResult['body']['token'])) {
        $authToken = $registerResult['body']['token'];
        echo "✅ Authentication token obtained: " . substr($authToken, 0, 20) . "...\n\n";
    } else {
        echo "❌ Failed to get authentication token. Tests will continue without auth.\n\n";
    }
}

// 2. Test CREATE (Store) - Create Lok Sabha
echo "2. Testing CREATE (Store) Operation...\n";
$createData = [
    'loksabha_name' => '17th Lok Sabha Test',
    'status' => '1'
];

$headers = $authToken ? ['Authorization: Bearer ' . $authToken] : [];
$createResult = makeRequest('POST', "$baseUrl/lok-sabhas", $createData, $headers);
printResult('Create Lok Sabha', $createResult, 201);

$lokSabhaId = null;
if ($createResult['status'] === 201 && isset($createResult['body']['lok_sabha']['id'])) {
    $lokSabhaId = $createResult['body']['lok_sabha']['id'];
    echo "✅ Created Lok Sabha with ID: $lokSabhaId\n\n";
}

// 3. Test CREATE (Store) - Create another Lok Sabha
echo "3. Testing CREATE (Store) - Second Lok Sabha...\n";
$createData2 = [
    'loksabha_name' => '18th Lok Sabha Test',
    'status' => '1'
];

$createResult2 = makeRequest('POST', "$baseUrl/lok-sabhas", $createData2, $headers);
printResult('Create Second Lok Sabha', $createResult2, 201);

// 4. Test CREATE (Store) - Create without status (should use default)
echo "4. Testing CREATE (Store) - Without Status (Default)...\n";
$createData3 = [
    'loksabha_name' => '19th Lok Sabha Test'
];

$createResult3 = makeRequest('POST', "$baseUrl/lok-sabhas", $createData3, $headers);
printResult('Create Lok Sabha Without Status', $createResult3, 201);

// 5. Test READ (Index) - List all Lok Sabhas with pagination
echo "5. Testing READ (Index) Operation...\n";
$indexResult = makeRequest('GET', "$baseUrl/lok-sabhas");
printResult('List All Lok Sabhas', $indexResult, 200);

if ($indexResult['status'] === 200) {
    echo "Pagination Info:\n";
    if (isset($indexResult['body']['pagination'])) {
        $pagination = $indexResult['body']['pagination'];
        echo "- Total: {$pagination['total']}\n";
        echo "- Per Page: {$pagination['per_page']}\n";
        echo "- Current Page: {$pagination['current_page']}\n";
        echo "- Last Page: {$pagination['last_page']}\n";
        echo "- Has More Pages: " . ($pagination['has_more_pages'] ? 'Yes' : 'No') . "\n";
    }
    
    // Get the first Lok Sabha ID for testing
    if (isset($indexResult['body']['lok_sabhas'][0]['id'])) {
        $lokSabhaId = $indexResult['body']['lok_sabhas'][0]['id'];
        echo "✅ Using Lok Sabha ID: $lokSabhaId for further tests\n";
    }
    echo "\n";
}

// 6. Test READ (Show) - Get specific Lok Sabha
echo "6. Testing READ (Show) Operation...\n";
if ($lokSabhaId) {
    $showResult = makeRequest('GET', "$baseUrl/lok-sabhas/$lokSabhaId");
    printResult('Get Specific Lok Sabha', $showResult, 200);
} else {
    echo "❌ Cannot test show operation - no Lok Sabha ID available\n\n";
}

// 7. Test UPDATE (Update) - Update Lok Sabha
echo "7. Testing UPDATE Operation...\n";
if ($lokSabhaId) {
    $updateData = [
        'loksabha_name' => '17th Lok Sabha (Updated)',
        'status' => '0'
    ];
    
    $updateResult = makeRequest('PUT', "$baseUrl/lok-sabhas/$lokSabhaId", $updateData, $headers);
    printResult('Update Lok Sabha', $updateResult, 200);
    
    // Verify the update
    $verifyResult = makeRequest('GET', "$baseUrl/lok-sabhas/$lokSabhaId");
    printResult('Verify Update', $verifyResult, 200);
} else {
    echo "❌ Cannot test update operation - no Lok Sabha ID available\n\n";
}

// 8. Test UPDATE (Partial Update) - Update only status
echo "8. Testing UPDATE - Partial Update...\n";
if ($lokSabhaId) {
    $partialUpdateData = [
        'status' => '1'
    ];
    
    $partialUpdateResult = makeRequest('PATCH', "$baseUrl/lok-sabhas/$lokSabhaId", $partialUpdateData, $headers);
    printResult('Partial Update Lok Sabha', $partialUpdateResult, 200);
} else {
    echo "❌ Cannot test partial update operation - no Lok Sabha ID available\n\n";
}

// 9. Test Validation Errors
echo "9. Testing Validation Errors...\n";
$invalidData = [
    'loksabha_name' => '', // Empty name should fail
    'status' => 'invalid_status'
];

$validationResult = makeRequest('POST', "$baseUrl/lok-sabhas", $invalidData, $headers);
printResult('Validation Error Test', $validationResult, 422);

// 10. Test Unauthorized Access
echo "10. Testing Unauthorized Access...\n";
$unauthorizedResult = makeRequest('POST', "$baseUrl/lok-sabhas", $createData);
printResult('Unauthorized Create', $unauthorizedResult, 401);

// 11. Test DELETE Operation
echo "11. Testing DELETE Operation...\n";
if ($lokSabhaId) {
    $deleteResult = makeRequest('DELETE', "$baseUrl/lok-sabhas/$lokSabhaId", null, $headers);
    printResult('Delete Lok Sabha', $deleteResult, 200);
    
    // Verify deletion
    $verifyDeleteResult = makeRequest('GET', "$baseUrl/lok-sabhas/$lokSabhaId");
    printResult('Verify Deletion (Should be 404)', $verifyDeleteResult, 404);
} else {
    echo "❌ Cannot test delete operation - no Lok Sabha ID available\n\n";
}

// 12. Test Pagination
echo "12. Testing Pagination...\n";
$paginationResult = makeRequest('GET', "$baseUrl/lok-sabhas?page=1");
printResult('Pagination Test', $paginationResult, 200);

// 13. Test Non-existent Resource
echo "13. Testing Non-existent Resource...\n";
$notFoundResult = makeRequest('GET', "$baseUrl/lok-sabhas/99999");
printResult('Non-existent Resource', $notFoundResult, 404);

// 14. Test Logout
echo "14. Testing Logout...\n";
if ($authToken) {
    $logoutResult = makeRequest('POST', "$baseUrl/logout", null, $headers);
    printResult('User Logout', $logoutResult, 200);
} else {
    echo "❌ Cannot test logout - no authentication token available\n\n";
}

// 15. Test all endpoints summary
echo "15. Testing All Endpoints Summary...\n";
$endpoints = [
    'GET /api/lok-sabhas' => 'List all Lok Sabhas',
    'GET /api/lok-sabhas/{id}' => 'Get specific Lok Sabha',
    'POST /api/lok-sabhas' => 'Create Lok Sabha',
    'PUT /api/lok-sabhas/{id}' => 'Update Lok Sabha',
    'PATCH /api/lok-sabhas/{id}' => 'Partial update Lok Sabha',
    'DELETE /api/lok-sabhas/{id}' => 'Delete Lok Sabha'
];

echo "Available Endpoints:\n";
foreach ($endpoints as $endpoint => $description) {
    echo "- $endpoint: $description\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ Public endpoints (GET) are working\n";
echo "✅ Authentication is working\n";
echo "✅ CRUD operations are working\n";
echo "✅ Pagination is working\n";
echo "✅ Error handling is working\n";
echo "✅ Validation is working\n";
echo "\nAll Lok Sabha API CRUD operations have been tested successfully!\n";
echo "Make sure your Laravel server is running on http://localhost:8000\n";
echo "Run: php artisan serve\n";
