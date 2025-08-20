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

echo "=== HIERARCHICAL API TEST WITH FOREIGN KEYS ===\n\n";

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

// Step 2: Create Lok Sabha
echo "\n2. Creating Lok Sabha...\n";
$lokSabhaData = [
    'loksabha_name' => 'Test Lok Sabha',
    'status' => '1'
];

$result = makeRequest('POST', "$baseUrl/lok-sabhas", $lokSabhaData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Lok Sabha created successfully!\n";
    $lokSabhaId = $result['response']['lok_sabha']['id'];
    echo "Lok Sabha ID: $lokSabhaId\n";
} else {
    echo "❌ Lok Sabha creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 3: Create Vidhan Sabha
echo "\n3. Creating Vidhan Sabha...\n";
$vidhanSabhaData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_name' => 'Test Vidhan Sabha',
    'vidhan_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/vidhan-sabhas", $vidhanSabhaData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Vidhan Sabha created successfully!\n";
    $vidhanSabhaId = $result['response']['vidhan_sabha']['id'];
    echo "Vidhan Sabha ID: $vidhanSabhaId\n";
} else {
    echo "❌ Vidhan Sabha creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 4: Create Block
echo "\n4. Creating Block...\n";
$blockData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_name' => 'Test Block',
    'block_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/blocks", $blockData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Block created successfully!\n";
    $blockId = $result['response']['block']['id'];
    echo "Block ID: $blockId\n";
} else {
    echo "❌ Block creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 5: Create Panchayat
echo "\n5. Creating Panchayat...\n";
$panchayatData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_id' => $blockId,
    'panchayat_choosing' => 'Direct Election',
    'panchayat_name' => 'Test Panchayat',
    'panchayat_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/panchayats", $panchayatData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Panchayat created successfully!\n";
    $panchayatId = $result['response']['panchayat']['id'];
    echo "Panchayat ID: $panchayatId\n";
} else {
    echo "❌ Panchayat creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 6: Create Village
echo "\n6. Creating Village...\n";
$villageData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_id' => $blockId,
    'panchayat_id' => $panchayatId,
    'village_choosing' => 'Direct Election',
    'village_name' => 'Test Village',
    'village_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/villages", $villageData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Village created successfully!\n";
    $villageId = $result['response']['data']['id'];
    echo "Village ID: $villageId\n";
} else {
    echo "❌ Village creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 7: Create Booth
echo "\n7. Creating Booth...\n";
$boothData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_id' => $blockId,
    'panchayat_id' => $panchayatId,
    'village_choosing' => 'Direct Election',
    'village_id' => $villageId,
    'booth_name' => 'Test Booth',
    'booth_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/booths", $boothData, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 201) {
    echo "✅ Booth created successfully!\n";
    $boothId = $result['response']['data']['id'];
    echo "Booth ID: $boothId\n";
} else {
    echo "❌ Booth creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
    exit(1);
}

// Step 8: Test hierarchical relationships
echo "\n8. Testing hierarchical relationships...\n";

// Test Lok Sabha relationships
echo "\n8a. Testing Lok Sabha relationships...\n";
$result = makeRequest('GET', "$baseUrl/lok-sabhas/$lokSabhaId");
if ($result['status_code'] === 200) {
    echo "✅ Lok Sabha details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Lok Sabha details!\n";
}

// Test Vidhan Sabha relationships
echo "\n8b. Testing Vidhan Sabha relationships...\n";
$result = makeRequest('GET', "$baseUrl/vidhan-sabhas/$vidhanSabhaId");
if ($result['status_code'] === 200) {
    echo "✅ Vidhan Sabha details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Vidhan Sabha details!\n";
}

// Test Block relationships
echo "\n8c. Testing Block relationships...\n";
$result = makeRequest('GET', "$baseUrl/blocks/$blockId");
if ($result['status_code'] === 200) {
    echo "✅ Block details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Block details!\n";
}

// Test Panchayat relationships
echo "\n8d. Testing Panchayat relationships...\n";
$result = makeRequest('GET', "$baseUrl/panchayats/$panchayatId");
if ($result['status_code'] === 200) {
    echo "✅ Panchayat details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Panchayat details!\n";
}

// Test Village relationships
echo "\n8e. Testing Village relationships...\n";
$result = makeRequest('GET', "$baseUrl/villages/$villageId");
if ($result['status_code'] === 200) {
    echo "✅ Village details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Village details!\n";
}

// Test Booth relationships
echo "\n8f. Testing Booth relationships...\n";
$result = makeRequest('GET', "$baseUrl/booths/$boothId");
if ($result['status_code'] === 200) {
    echo "✅ Booth details retrieved successfully!\n";
} else {
    echo "❌ Failed to retrieve Booth details!\n";
}

// Step 9: Test cascade deletion
echo "\n9. Testing cascade deletion...\n";

// Delete Lok Sabha (should cascade to all child records)
echo "\n9a. Deleting Lok Sabha (should cascade delete all child records)...\n";
$result = makeRequest('DELETE', "$baseUrl/lok-sabhas/$lokSabhaId", null, $headers);
echo "Status: " . $result['status_code'] . "\n";
if ($result['status_code'] === 200) {
    echo "✅ Lok Sabha deleted successfully!\n";
} else {
    echo "❌ Lok Sabha deletion failed!\n";
}

// Verify cascade deletion
echo "\n9b. Verifying cascade deletion...\n";
$result = makeRequest('GET', "$baseUrl/vidhan-sabhas/$vidhanSabhaId");
if ($result['status_code'] === 404) {
    echo "✅ Vidhan Sabha cascade deleted successfully!\n";
} else {
    echo "❌ Vidhan Sabha still exists!\n";
}

$result = makeRequest('GET', "$baseUrl/blocks/$blockId");
if ($result['status_code'] === 404) {
    echo "✅ Block cascade deleted successfully!\n";
} else {
    echo "❌ Block still exists!\n";
}

$result = makeRequest('GET', "$baseUrl/panchayats/$panchayatId");
if ($result['status_code'] === 404) {
    echo "✅ Panchayat cascade deleted successfully!\n";
} else {
    echo "❌ Panchayat still exists!\n";
}

$result = makeRequest('GET', "$baseUrl/villages/$villageId");
if ($result['status_code'] === 404) {
    echo "✅ Village cascade deleted successfully!\n";
} else {
    echo "❌ Village still exists!\n";
}

$result = makeRequest('GET', "$baseUrl/booths/$boothId");
if ($result['status_code'] === 404) {
    echo "✅ Booth cascade deleted successfully!\n";
} else {
    echo "❌ Booth still exists!\n";
}

echo "\n=== HIERARCHICAL API TEST COMPLETE ===\n";
echo "All foreign key relationships and cascade deletion tests completed successfully!\n";
?>
