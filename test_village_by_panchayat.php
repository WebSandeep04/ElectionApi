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

echo "=== TESTING VILLAGE BY PANCHAYAT ID API ===\n\n";

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
    'loksabha_name' => 'Test Lok Sabha for Village by Panchayat API',
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
    'vidhansabha_name' => 'Test Vidhan Sabha for Village by Panchayat API',
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

// Step 4: Create a Block
echo "\n4. Creating a Block...\n";
$blockData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_name' => 'Test Block for Village by Panchayat API',
    'block_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/blocks", $blockData, $headers);
if ($result['status_code'] === 201) {
    $blockId = $result['response']['block']['id'];
    echo "✅ Block created with ID: $blockId\n";
} else {
    echo "❌ Block creation failed!\n";
    exit(1);
}

// Step 5: Create a Panchayat
echo "\n5. Creating a Panchayat...\n";
$panchayatData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_id' => $blockId,
    'panchayat_choosing' => 'Direct Election',
    'panchayat_name' => 'Test Panchayat for Village by Panchayat API',
    'panchayat_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/panchayats", $panchayatData, $headers);
if ($result['status_code'] === 201) {
    $panchayatId = $result['response']['panchayat']['id'];
    echo "✅ Panchayat created with ID: $panchayatId\n";
} else {
    echo "❌ Panchayat creation failed!\n";
    exit(1);
}

// Step 6: Create multiple Villages for this Panchayat
echo "\n6. Creating Villages for Panchayat ID: $panchayatId\n";

for ($i = 1; $i <= 3; $i++) {
    $villageData = [
        'loksabha_id' => $lokSabhaId,
        'vidhansabha_id' => $vidhanSabhaId,
        'block_id' => $blockId,
        'panchayat_id' => $panchayatId,
        'village_choosing' => 'Direct Election',
        'village_name' => "Test Village $i",
        'village_status' => '1'
    ];
    
    $result = makeRequest('POST', "$baseUrl/villages", $villageData, $headers);
    if ($result['status_code'] === 201) {
        echo "✅ Village $i created successfully!\n";
    } else {
        echo "❌ Village $i creation failed!\n";
    }
}

// Step 7: Test the API - Fetch Villages by Panchayat ID
echo "\n7. Testing API: GET /api/villages/panchayat/{panchayatId}\n";
echo "URL: $baseUrl/villages/panchayat/$panchayatId\n";

$result = makeRequest('GET', "$baseUrl/villages/panchayat/$panchayatId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful!\n";
    echo "Total Villages found: " . $result['response']['pagination']['total'] . "\n";
    echo "Current page: " . $result['response']['pagination']['current_page'] . "\n";
    echo "Per page: " . $result['response']['pagination']['per_page'] . "\n";
    
    if (!empty($result['response']['data'])) {
        echo "\nVillages found:\n";
        foreach ($result['response']['data'] as $village) {
            echo "- ID: " . $village['id'] . ", Name: " . $village['village_name'] . "\n";
            echo "  Choosing: " . $village['village_choosing'] . "\n";
            echo "  Panchayat: " . $village['panchayat']['panchayat_name'] . "\n";
            echo "  Block: " . $village['block']['block_name'] . "\n";
            echo "  Vidhan Sabha: " . $village['vidhan_sabha']['vidhansabha_name'] . "\n";
            echo "  Lok Sabha: " . $village['lok_sabha']['loksabha_name'] . "\n";
        }
    } else {
        echo "No Villages found for this Panchayat.\n";
    }
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 8: Test with non-existent Panchayat ID
echo "\n8. Testing with non-existent Panchayat ID...\n";
$nonExistentId = 99999;
$result = makeRequest('GET', "$baseUrl/villages/panchayat/$nonExistentId");
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 200) {
    echo "✅ API call successful (returns empty result for non-existent ID)!\n";
    echo "Total Villages found: " . $result['response']['pagination']['total'] . "\n";
} else {
    echo "❌ API call failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

echo "\n=== API TESTING COMPLETE ===\n";
echo "The API endpoint is: GET /api/villages/panchayat/{panchayatId}\n";
echo "This endpoint returns all Villages that belong to the specified Panchayat ID.\n";
?>
