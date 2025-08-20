<?php

/**
 * Simple test script for the Laravel Authentication API
 * Run this script to test all endpoints
 */

$baseUrl = 'http://localhost:8000/api';
$token = null;

echo "🧪 Testing Laravel Authentication API\n";
echo "=====================================\n\n";

// Test 1: Register a new user
echo "1. Testing User Registration...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$response = makeRequest($baseUrl . '/register', 'POST', $registerData);
if ($response['status'] === 201) {
    echo "✅ Registration successful!\n";
    $token = $response['data']['token'];
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Registration failed: " . $response['data']['message'] . "\n\n";
}

// Test 2: Login with the registered user
echo "2. Testing User Login...\n";
$loginData = [
    'email' => 'testuser@example.com',
    'password' => 'password123'
];

$response = makeRequest($baseUrl . '/login', 'POST', $loginData);
if ($response['status'] === 200) {
    echo "✅ Login successful!\n";
    $token = $response['data']['token'];
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Login failed: " . $response['data']['message'] . "\n\n";
}

// Test 3: Get user profile (requires authentication)
echo "3. Testing Get User Profile...\n";
$response = makeRequest($baseUrl . '/user', 'GET', null, $token);
if ($response['status'] === 200) {
    echo "✅ Get profile successful!\n";
    echo "   User: " . $response['data']['user']['name'] . " (" . $response['data']['user']['email'] . ")\n\n";
} else {
    echo "❌ Get profile failed: " . $response['data']['message'] . "\n\n";
}

// Test 4: Logout
echo "4. Testing User Logout...\n";
$response = makeRequest($baseUrl . '/logout', 'POST', null, $token);
if ($response['status'] === 200) {
    echo "✅ Logout successful!\n\n";
} else {
    echo "❌ Logout failed: " . $response['data']['message'] . "\n\n";
}

// Test 5: Try to access protected route after logout
echo "5. Testing Protected Route After Logout...\n";
$response = makeRequest($baseUrl . '/user', 'GET', null, $token);
if ($response['status'] === 401) {
    echo "✅ Authentication properly enforced!\n";
    echo "   (Expected 401 Unauthorized)\n\n";
} else {
    echo "❌ Authentication not working properly\n\n";
}

echo "🎉 All tests completed!\n";

/**
 * Helper function to make HTTP requests
 */
function makeRequest($url, $method, $data = null, $token = null) {
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
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
        'data' => json_decode($response, true)
    ];
}
