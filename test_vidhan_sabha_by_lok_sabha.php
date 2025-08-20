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

echo "=== TESTING VIDHAN SABHA BY LOK SABHA ID API ===\n\n";

// Step 1: Login to get token
echo "1. Logging in...\n";
$loginData = [
    'email' => 'Sandeep@example.com',
    'password' => '12345678'
];

$loginResult = makeRequest('POST', "$baseUrl/login", $loginData);
if ($loginResult['status_code'] === 200) {
    $token = $loginResult['response']['token'];
    echo "✅ Login successful!\n";
} else {
    echo "❌ Login failed!\n";
    exit(1);
}

$headers = ['Authorization: Bearer ' . $token];

// Step 2: Create a Lok Sabha first
echo "\n2. Creating a Lok Sabha...\n";
$lokSabhaData = [
    'loksabha_name' => 'Test Lok Sabha for Vidhan Sabha API',
    'status' => '1'
];

$result = makeRequest('POST', "$baseUrl/lok-sabhas", $lokSabhaData, $headers);
if ($result['status_code'] === 201) {
    $lokSabhaId = $result['response']['lok_sabha']['id'];
    echo "✅ Lok Sabha created with ID: $lokSabhaId\n";
} else {
    echo "❌ Lok Sabha creation failed!\n";
    exit(1);
}

// Step 3: Create multiple Vidhan Sabhas for this Lok Sabha
echo "\n3. Creating Vidhan Sabhas for Lok Sabha ID: $lokSabhaId\n";

for ($i = 1; $i <= 3; $i++) {
    $vidhanSabhaData = [
        'loksabha_id' => $lokSabhaId,
        'vidhansabha_name' => "Test Vidhan Sabha $i",
        'vidhan_status' => '1'
    ];
    
    $result = makeRequest('POST', "$baseUrl/vidhan-sabhas", $vidhanSabhaData, $headers);
    if ($result['status_code'] === 201) {
        echo "✅ Vidhan Sabha $i created successfully!\n";
    } else {
        echo "❌ Vidhan Sabha $i creation failed!\n";
    }
}

// Step 4: Test the API - Fetch Vidhan Sabhas by Lok Sabha ID
echo "\n4. Testing API: GET /api/vidhan-sabhas/lok-sabha/{loksabhaId}\n";
echo "URL: $baseUrl/vidhan-sabhas/lok-sabha/$lokSabhaId\n";

$result = makeRequest('GET', "$baseUrl/vidhan-sabhas/lok-sabha/$lokSabhaId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful!\n";
    echo "Total Vidhan Sabhas found: " . $result['response']['pagination']['total'] . "\n";
    echo "Current page: " . $result['response']['pagination']['current_page'] . "\n";
    echo "Per page: " . $result['response']['pagination']['per_page'] . "\n";
    
    if (!empty($result['response']['vidhan_sabhas'])) {
        echo "\nVidhan Sabhas found:\n";
        foreach ($result['response']['vidhan_sabhas'] as $vidhanSabha) {
            echo "- ID: " . $vidhanSabha['id'] . ", Name: " . $vidhanSabha['vidhansabha_name'] . "\n";
        }
    } else {
        echo "No Vidhan Sabhas found for this Lok Sabha.\n";
    }
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 5: Test with non-existent Lok Sabha ID
echo "\n5. Testing with non-existent Lok Sabha ID...\n";
$nonExistentId = 99999;
$result = makeRequest('GET', "$baseUrl/vidhan-sabhas/lok-sabha/$nonExistentId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful (returns empty result for non-existent ID)!\n";
    echo "Total Vidhan Sabhas found: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

echo "\n=== API TESTING COMPLETE ===\n";
echo "The API endpoint is: GET /api/vidhan-sabhas/lok-sabha/{loksabhaId}\n";
echo "This endpoint returns all Vidhan Sabhas that belong to the specified Lok Sabha ID.\n";
?>
