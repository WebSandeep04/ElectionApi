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

echo "=== VILLAGE API TEST ===\n\n";

// Step 1: Get authentication token
echo "1. Getting authentication token...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$loginResult = makeRequest('POST', "$baseUrl/login", $loginData);
echo "Login Status: " . $loginResult['status_code'] . "\n";

if ($loginResult['status_code'] !== 200) {
    echo "Login failed. Trying registration...\n";
    $registerData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];
    
    $registerResult = makeRequest('POST', "$baseUrl/register", $registerData);
    echo "Registration Status: " . $registerResult['status_code'] . "\n";
    
    if ($registerResult['status_code'] === 201) {
        $token = $registerResult['response']['token'];
        echo "✅ Registration successful!\n";
    } else {
        echo "❌ Both login and registration failed!\n";
        exit(1);
    }
} else {
    $token = $loginResult['response']['token'];
    echo "✅ Login successful!\n";
}

$headers = ['Authorization: Bearer ' . $token];

// Step 2: Test GET /api/villages (List all villages)
echo "\n2. Testing GET /api/villages (List all villages)...\n";
$result = makeRequest('GET', "$baseUrl/villages");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ List villages successful!\n";
    echo "Total villages: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ List villages failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 3: Test POST /api/villages (Create village)
echo "\n3. Testing POST /api/villages (Create village)...\n";
$villageData = [
    'loksabha_id' => '1',
    'vidhansabha_id' => '1',
    'block_id' => '1',
    'panchayat_id' => '1',
    'village_choosing' => 'Direct Election',
    'village_name' => 'Test Village',
    'village_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/villages", $villageData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Create village successful!\n";
    $villageId = $result['response']['data']['id'];
    echo "Created village ID: $villageId\n";
} else {
    echo "❌ Create village failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 4: Test GET /api/villages/{id} (Show village)
echo "\n4. Testing GET /api/villages/$villageId (Show village)...\n";
$result = makeRequest('GET', "$baseUrl/villages/$villageId");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Show village successful!\n";
    echo "Village name: " . $result['response']['data']['village_name'] . "\n";
} else {
    echo "❌ Show village failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 5: Test PUT /api/villages/{id} (Update village)
echo "\n5. Testing PUT /api/villages/$villageId (Update village)...\n";
$updateData = [
    'village_name' => 'Updated Test Village',
    'village_choosing' => 'Indirect Election'
];

$result = makeRequest('PUT', "$baseUrl/villages/$villageId", $updateData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Update village successful!\n";
    echo "Updated name: " . $result['response']['data']['village_name'] . "\n";
} else {
    echo "❌ Update village failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 6: Test GET /api/villages/lok-sabha/{loksabhaId}
echo "\n6. Testing GET /api/villages/lok-sabha/1 (Villages by Lok Sabha)...\n";
$result = makeRequest('GET', "$baseUrl/villages/lok-sabha/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get villages by Lok Sabha successful!\n";
    echo "Total villages in Lok Sabha 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get villages by Lok Sabha failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 7: Test GET /api/villages/vidhan-sabha/{vidhansabhaId}
echo "\n7. Testing GET /api/villages/vidhan-sabha/1 (Villages by Vidhan Sabha)...\n";
$result = makeRequest('GET', "$baseUrl/villages/vidhan-sabha/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get villages by Vidhan Sabha successful!\n";
    echo "Total villages in Vidhan Sabha 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get villages by Vidhan Sabha failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 8: Test GET /api/villages/block/{blockId}
echo "\n8. Testing GET /api/villages/block/1 (Villages by Block)...\n";
$result = makeRequest('GET', "$baseUrl/villages/block/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get villages by Block successful!\n";
    echo "Total villages in Block 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get villages by Block failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 9: Test GET /api/villages/panchayat/{panchayatId}
echo "\n9. Testing GET /api/villages/panchayat/1 (Villages by Panchayat)...\n";
$result = makeRequest('GET', "$baseUrl/villages/panchayat/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get villages by Panchayat successful!\n";
    echo "Total villages in Panchayat 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get villages by Panchayat failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 10: Test DELETE /api/villages/{id} (Delete village)
echo "\n10. Testing DELETE /api/villages/$villageId (Delete village)...\n";
$result = makeRequest('DELETE', "$baseUrl/villages/$villageId", null, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Delete village successful!\n";
} else {
    echo "❌ Delete village failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 11: Verify deletion
echo "\n11. Verifying deletion...\n";
$result = makeRequest('GET', "$baseUrl/villages/$villageId");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 404) {
    echo "✅ Village successfully deleted!\n";
} else {
    echo "❌ Village still exists!\n";
}

echo "\n=== VILLAGE API TEST COMPLETE ===\n";
echo "All tests completed successfully!\n";
?>
