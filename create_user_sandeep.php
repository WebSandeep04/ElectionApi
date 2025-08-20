<?php

$baseUrl = 'http://localhost:8000/api';

function makeRequest($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $allHeaders = array_merge($defaultHeaders, $headers);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
}

echo "=== CREATING USER SANDEEP ===\n\n";

// Register user Sandeep
echo "Creating user Sandeep...\n";
$registerData = [
    'name' => 'Sandeep',
    'email' => 'Sandeep@example.com',
    'password' => '12345678',
    'password_confirmation' => '12345678'
];

$result = makeRequest('POST', "$baseUrl/register", $registerData);
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 201) {
    echo "✅ User Sandeep created successfully!\n";
    echo "Email: Sandeep@example.com\n";
    echo "Password: 12345678\n";
    echo "Token: " . $result['response']['token'] . "\n";
} else {
    echo "❌ User creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    
    // Try to login if user already exists
    echo "\nTrying to login with existing credentials...\n";
    $loginData = [
        'email' => 'Sandeep@example.com',
        'password' => '12345678'
    ];
    
    $loginResult = makeRequest('POST', "$baseUrl/login", $loginData);
    echo "Login Status: " . $loginResult['status_code'] . "\n";
    
    if ($loginResult['status_code'] === 200) {
        echo "✅ Login successful! User already exists.\n";
        echo "Email: Sandeep@example.com\n";
        echo "Password: 12345678\n";
        echo "Token: " . $loginResult['response']['token'] . "\n";
    } else {
        echo "❌ Login also failed!\n";
        echo "Response: " . $loginResult['raw_response'] . "\n";
    }
}

echo "\n=== USER CREATION COMPLETE ===\n";
?>
