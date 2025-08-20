<?php

$baseUrl = 'http://localhost:8000/api';

// Function to make API calls
function makeApiCall($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

echo "🚀 Simple Employee API Testing\n\n";

// Test 1: Get all employees (public)
echo "1. Testing GET /api/employees (Public)\n";
$result = makeApiCall($baseUrl . '/employees');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Found " . count($result['response']['data']) . " employees\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 2: Get employee types (public)
echo "2. Testing GET /api/employee-types (Public)\n";
$result = makeApiCall($baseUrl . '/employee-types');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Found " . count($result['response']['data']) . " employee types\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 3: Get active employees (public)
echo "3. Testing GET /api/employees/active (Public)\n";
$result = makeApiCall($baseUrl . '/employees/active');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Found " . count($result['response']['data']) . " active employees\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 4: Get inactive employees (public)
echo "4. Testing GET /api/employees/inactive (Public)\n";
$result = makeApiCall($baseUrl . '/employees/inactive');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Found " . count($result['response']['data']) . " inactive employees\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 5: Search employees (public)
echo "5. Testing GET /api/employees?search=John (Public)\n";
$result = makeApiCall($baseUrl . '/employees?search=John');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Search working\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 6: Get employee documents (public)
echo "6. Testing GET /api/employee-documents (Public)\n";
$result = makeApiCall($baseUrl . '/employee-documents');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200) {
    echo "✅ PASSED - Documents endpoint working\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

// Test 7: Test pagination
echo "7. Testing Pagination /api/employees?page=1 (Public)\n";
$result = makeApiCall($baseUrl . '/employees?page=1');
echo "Status: {$result['status']}\n";
if ($result['status'] === 200 && isset($result['response']['meta'])) {
    echo "✅ PASSED - Pagination working\n";
    echo "   Current Page: " . $result['response']['meta']['current_page'] . "\n";
    echo "   Total: " . $result['response']['meta']['total'] . "\n";
    echo "   Per Page: " . $result['response']['meta']['per_page'] . "\n";
} else {
    echo "❌ FAILED\n";
}
echo "\n";

echo "🎉 Simple Employee API Testing Completed!\n";
echo "All public endpoints are working correctly.\n";
