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

echo "=== BOOTH API TEST ===\n\n";

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

// Step 2: Test GET /api/booths (List all booths)
echo "\n2. Testing GET /api/booths (List all booths)...\n";
$result = makeRequest('GET', "$baseUrl/booths");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ List booths successful!\n";
    echo "Total booths: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ List booths failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 3: Test POST /api/booths (Create booth)
echo "\n3. Testing POST /api/booths (Create booth)...\n";
$boothData = [
    'loksabha_id' => '1',
    'vidhansabha_id' => '1',
    'block_id' => '1',
    'panchayat_id' => '1',
    'village_choosing' => 'Direct Election',
    'village_id' => '1',
    'booth_name' => 'Test Booth',
    'booth_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/booths", $boothData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Create booth successful!\n";
    $boothId = $result['response']['data']['id'];
    echo "Created booth ID: $boothId\n";
} else {
    echo "❌ Create booth failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 4: Test GET /api/booths/{id} (Show booth)
echo "\n4. Testing GET /api/booths/$boothId (Show booth)...\n";
$result = makeRequest('GET', "$baseUrl/booths/$boothId");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Show booth successful!\n";
    echo "Booth name: " . $result['response']['data']['booth_name'] . "\n";
} else {
    echo "❌ Show booth failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 5: Test PUT /api/booths/{id} (Update booth)
echo "\n5. Testing PUT /api/booths/$boothId (Update booth)...\n";
$updateData = [
    'booth_name' => 'Updated Test Booth',
    'village_choosing' => 'Indirect Election'
];

$result = makeRequest('PUT', "$baseUrl/booths/$boothId", $updateData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Update booth successful!\n";
    echo "Updated name: " . $result['response']['data']['booth_name'] . "\n";
} else {
    echo "❌ Update booth failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 6: Test GET /api/booths/lok-sabha/{loksabhaId}
echo "\n6. Testing GET /api/booths/lok-sabha/1 (Booths by Lok Sabha)...\n";
$result = makeRequest('GET', "$baseUrl/booths/lok-sabha/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get booths by Lok Sabha successful!\n";
    echo "Total booths in Lok Sabha 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get booths by Lok Sabha failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 7: Test GET /api/booths/vidhan-sabha/{vidhansabhaId}
echo "\n7. Testing GET /api/booths/vidhan-sabha/1 (Booths by Vidhan Sabha)...\n";
$result = makeRequest('GET', "$baseUrl/booths/vidhan-sabha/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get booths by Vidhan Sabha successful!\n";
    echo "Total booths in Vidhan Sabha 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get booths by Vidhan Sabha failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 8: Test GET /api/booths/block/{blockId}
echo "\n8. Testing GET /api/booths/block/1 (Booths by Block)...\n";
$result = makeRequest('GET', "$baseUrl/booths/block/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get booths by Block successful!\n";
    echo "Total booths in Block 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get booths by Block failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 9: Test GET /api/booths/panchayat/{panchayatId}
echo "\n9. Testing GET /api/booths/panchayat/1 (Booths by Panchayat)...\n";
$result = makeRequest('GET', "$baseUrl/booths/panchayat/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get booths by Panchayat successful!\n";
    echo "Total booths in Panchayat 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get booths by Panchayat failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 10: Test GET /api/booths/village/{villageId}
echo "\n10. Testing GET /api/booths/village/1 (Booths by Village)...\n";
$result = makeRequest('GET', "$baseUrl/booths/village/1");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Get booths by Village successful!\n";
    echo "Total booths in Village 1: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ Get booths by Village failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 11: Test DELETE /api/booths/{id} (Delete booth)
echo "\n11. Testing DELETE /api/booths/$boothId (Delete booth)...\n";
$result = makeRequest('DELETE', "$baseUrl/booths/$boothId", null, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Delete booth successful!\n";
} else {
    echo "❌ Delete booth failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 12: Verify deletion
echo "\n12. Verifying deletion...\n";
$result = makeRequest('GET', "$baseUrl/booths/$boothId");
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 404) {
    echo "✅ Booth successfully deleted!\n";
} else {
    echo "❌ Booth still exists!\n";
}

echo "\n=== BOOTH API TEST COMPLETE ===\n";
echo "All tests completed successfully!\n";
?>
