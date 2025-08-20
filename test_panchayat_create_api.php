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

echo "=== TESTING PANCHAYAT CREATE API ===\n\n";

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
    'loksabha_name' => 'Test Lok Sabha for Panchayat API',
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
    'vidhansabha_name' => 'Test Vidhan Sabha for Panchayat API',
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
    'block_name' => 'Test Block for Panchayat API',
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

// Step 5: Test Panchayat Create API
echo "\n5. Testing Panchayat Create API: POST /api/panchayats\n";

$panchayatData = [
    'loksabha_id' => $lokSabhaId,
    'vidhansabha_id' => $vidhanSabhaId,
    'block_id' => $blockId,
    'panchayat_choosing' => 'Direct Election',
    'panchayat_name' => 'Test Panchayat',
    'panchayat_status' => '1'
];

echo "Request Data:\n";
echo json_encode($panchayatData, JSON_PRETTY_PRINT) . "\n\n";

$result = makeRequest('POST', "$baseUrl/panchayats", $panchayatData, $headers);
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 201) {
    echo "✅ Panchayat created successfully!\n";
    echo "Panchayat ID: " . $result['response']['panchayat']['id'] . "\n";
    echo "Panchayat Name: " . $result['response']['panchayat']['panchayat_name'] . "\n";
    echo "Panchayat Choosing: " . $result['response']['panchayat']['panchayat_choosing'] . "\n";
    echo "Block ID: " . $result['response']['panchayat']['block_id'] . "\n";
    echo "Vidhan Sabha ID: " . $result['response']['panchayat']['vidhansabha_id'] . "\n";
    echo "Lok Sabha ID: " . $result['response']['panchayat']['loksabha_id'] . "\n";
    
    // Check if relationships are loaded
    if (isset($result['response']['panchayat']['lok_sabha'])) {
        echo "✅ Lok Sabha relationship loaded: " . $result['response']['panchayat']['lok_sabha']['loksabha_name'] . "\n";
    }
    if (isset($result['response']['panchayat']['vidhan_sabha'])) {
        echo "✅ Vidhan Sabha relationship loaded: " . $result['response']['panchayat']['vidhan_sabha']['vidhansabha_name'] . "\n";
    }
    if (isset($result['response']['panchayat']['block'])) {
        echo "✅ Block relationship loaded: " . $result['response']['panchayat']['block']['block_name'] . "\n";
    }
} else {
    echo "❌ Panchayat creation failed!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 6: Test with missing required fields
echo "\n6. Testing with missing required fields...\n";

$invalidData = [
    'panchayat_name' => 'Test Panchayat Without Required Fields'
];

$result = makeRequest('POST', "$baseUrl/panchayats", $invalidData, $headers);
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 422) {
    echo "✅ Validation working correctly!\n";
    echo "Validation errors:\n";
    if (isset($result['response']['errors'])) {
        foreach ($result['response']['errors'] as $field => $errors) {
            echo "- $field: " . implode(', ', $errors) . "\n";
        }
    }
} else {
    echo "❌ Validation not working as expected!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

// Step 7: Test with invalid foreign keys
echo "\n7. Testing with invalid foreign keys...\n";

$invalidForeignKeyData = [
    'loksabha_id' => 99999,
    'vidhansabha_id' => 99999,
    'block_id' => 99999,
    'panchayat_choosing' => 'Direct Election',
    'panchayat_name' => 'Test Panchayat with Invalid FKs',
    'panchayat_status' => '1'
];

$result = makeRequest('POST', "$baseUrl/panchayats", $invalidForeignKeyData, $headers);
echo "Status: " . $result['status_code'] . "\n";

if ($result['status_code'] === 422) {
    echo "✅ Foreign key validation working correctly!\n";
    echo "Validation errors:\n";
    if (isset($result['response']['errors'])) {
        foreach ($result['response']['errors'] as $field => $errors) {
            echo "- $field: " . implode(', ', $errors) . "\n";
        }
    }
} else {
    echo "❌ Foreign key validation not working as expected!\n";
    echo "Response: " . $result['raw_response'] . "\n";
}

echo "\n=== PANCHAYAT CREATE API TESTING COMPLETE ===\n";
echo "The API endpoint is: POST /api/panchayats\n";
echo "Required fields: loksabha_id, vidhansabha_id, block_id, panchayat_choosing, panchayat_name\n";
?>
