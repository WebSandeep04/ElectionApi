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

echo "=== TESTING BLOCK BY VIDHAN SABHA ID API ===\n\n";

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
    'loksabha_name' => 'Test Lok Sabha for Block API',
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

// Step 3: Create a Vidhan Sabha
echo "\n3. Creating a Vidhan Sabha...\n";
$vidhanSabhaData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_name' => 'Test Vidhan Sabha for Block API',
    'vidhan_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/vidhan-sabhas", $vidhanSabhaData, $headers);
if ($result['status_code'] === 201) {
    $vidhanSabhaId = $result['response']['vidhan_sabha']['id'];
    echo "✅ Vidhan Sabha created with ID: $vidhanSabhaId\n";
} else {
    echo "❌ Vidhan Sabha creation failed!\n";
    exit(1);
}

// Step 4: Create multiple Blocks for this Vidhan Sabha
echo "\n4. Creating Blocks for Vidhan Sabha ID: $vidhanSabhaId\n";

for ($i = 1; $i <= 3; $i++) {
    $blockData = [
        'loksabha_id' => $lokSabhaId,
        'vidhansabha_id' => $vidhanSabhaId,
        'block_name' => "Test Block $i",
        'block_status' => '1'
    ];
    
    $result = makeRequest('POST', "$baseUrl/blocks", $blockData, $headers);
    if ($result['status_code'] === 201) {
        echo "✅ Block $i created successfully!\n";
    } else {
        echo "❌ Block $i creation failed!\n";
    }
}

// Step 5: Test the API - Fetch Blocks by Vidhan Sabha ID
echo "\n5. Testing API: GET /api/blocks/vidhan-sabha/{vidhansabhaId}\n";
echo "URL: $baseUrl/blocks/vidhan-sabha/$vidhanSabhaId\n";

$result = makeRequest('GET', "$baseUrl/blocks/vidhan-sabha/$vidhanSabhaId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful!\n";
    echo "Total Blocks found: " . $result['response']['pagination']['total'] . "\n";
    echo "Current page: " . $result['response']['pagination']['current_page'] . "\n";
    echo "Per page: " . $result['response']['pagination']['per_page'] . "\n";
    
    if (!empty($result['response']['blocks'])) {
        echo "\nBlocks found:\n";
        foreach ($result['response']['blocks'] as $block) {
            echo "- ID: " . $block['id'] . ", Name: " . $block['block_name'] . "\n";
            echo "  Lok Sabha: " . $block['lok_sabha']['loksabha_name'] . "\n";
            echo "  Vidhan Sabha: " . $block['vidhan_sabha']['vidhansabha_name'] . "\n";
        }
    } else {
        echo "No Blocks found for this Vidhan Sabha.\n";
    }
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 6: Test with non-existent Vidhan Sabha ID
echo "\n6. Testing with non-existent Vidhan Sabha ID...\n";
$nonExistentId = 99999;
$result = makeRequest('GET', "$baseUrl/blocks/vidhan-sabha/$nonExistentId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful (returns empty result for non-existent ID)!\n";
    echo "Total Blocks found: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

echo "\n=== API TESTING COMPLETE ===\n";
echo "The API endpoint is: GET /api/blocks/vidhan-sabha/{vidhansabhaId}\n";
echo "This endpoint returns all Blocks that belong to the specified Vidhan Sabha ID.\n";
?>
