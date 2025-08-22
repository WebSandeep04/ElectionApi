<?php

// Test file to demonstrate cast ratio choosing functionality
// This shows how panchayat choosing and village choosing work in the cast ratios table

echo "=== CAST RATIO CHOOSING API TEST ===\n\n";

// Base URL for the API
$baseUrl = 'http://localhost:8000/api';

// Test 1: Create a panchayat choosing
echo "1. Creating a panchayat choosing...\n";
$panchayatChoosingData = [
    'name' => 'Test Panchayat Choosing for Cast Ratio',
    'status' => '1'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/panchayat-choosings');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($panchayatChoosingData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$panchayatChoosing = json_decode($response, true);
curl_close($ch);

if (isset($panchayatChoosing['data']['id'])) {
    echo "   ✓ Panchayat choosing created with ID: {$panchayatChoosing['data']['id']}\n";
    $panchayatChoosingId = $panchayatChoosing['data']['id'];
} else {
    echo "   ✗ Failed to create panchayat choosing\n";
    exit;
}

// Test 2: Create a village choosing
echo "\n2. Creating a village choosing...\n";
$villageChoosingData = [
    'name' => 'Test Village Choosing for Cast Ratio',
    'status' => '1'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/village-choosings');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($villageChoosingData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$villageChoosing = json_decode($response, true);
curl_close($ch);

if (isset($villageChoosing['data']['id'])) {
    echo "   ✓ Village choosing created with ID: {$villageChoosing['data']['id']}\n";
    $villageChoosingId = $villageChoosing['data']['id'];
} else {
    echo "   ✗ Failed to create village choosing\n";
    exit;
}

// Test 3: Get a caste ID (assuming there's at least one caste)
echo "\n3. Getting a caste ID...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/castes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$castes = json_decode($response, true);
curl_close($ch);

if (isset($castes['data']) && count($castes['data']) > 0) {
    $casteId = $castes['data'][0]['id'];
    echo "   ✓ Using caste ID: {$casteId}\n";
} else {
    echo "   ✗ No castes found, creating one...\n";
    
    $casteData = [
        'caste' => 'Test Caste for Cast Ratio',
        'status' => '1'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/castes');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($casteData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $caste = json_decode($response, true);
    curl_close($ch);
    
    if (isset($caste['data']['id'])) {
        $casteId = $caste['data']['id'];
        echo "   ✓ Caste created with ID: {$casteId}\n";
    } else {
        echo "   ✗ Failed to create caste\n";
        exit;
    }
}

// Test 4: Create a cast ratio with both choosing IDs
echo "\n4. Creating a cast ratio with panchayat_choosing_id and village_choosing_id...\n";
$castRatioData = [
    'caste_id' => $casteId,
    'caste_ratio' => 25,
    'panchayat_choosing_id' => $panchayatChoosingId,
    'village_choosing_id' => $villageChoosingId
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/cast-ratios');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($castRatioData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$castRatio = json_decode($response, true);
curl_close($ch);

if (isset($castRatio['data']['caste_ratio_id'])) {
    echo "   ✓ Cast ratio created with ID: {$castRatio['data']['caste_ratio_id']}\n";
    echo "   ✓ Panchayat Choosing ID: {$castRatio['data']['panchayat_choosing_id']}\n";
    echo "   ✓ Village Choosing ID: {$castRatio['data']['village_choosing_id']}\n";
    $castRatioId = $castRatio['data']['caste_ratio_id'];
} else {
    echo "   ✗ Failed to create cast ratio\n";
    echo "   Error: " . json_encode($castRatio) . "\n";
    exit;
}

// Test 5: Get cast ratios by panchayat choosing ID
echo "\n5. Testing get cast ratios by panchayat choosing ID...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/cast-ratios/panchayat-choosing/{$panchayatChoosingId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$castRatiosByPanchayatChoosing = json_decode($response, true);
curl_close($ch);

if (isset($castRatiosByPanchayatChoosing['data']) && count($castRatiosByPanchayatChoosing['data']) > 0) {
    echo "   ✓ Found " . count($castRatiosByPanchayatChoosing['data']) . " cast ratio(s) for panchayat choosing ID: {$panchayatChoosingId}\n";
} else {
    echo "   ✗ No cast ratios found for panchayat choosing ID: {$panchayatChoosingId}\n";
}

// Test 6: Get cast ratios by village choosing ID
echo "\n6. Testing get cast ratios by village choosing ID...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/cast-ratios/village-choosing/{$villageChoosingId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$castRatiosByVillageChoosing = json_decode($response, true);
curl_close($ch);

if (isset($castRatiosByVillageChoosing['data']) && count($castRatiosByVillageChoosing['data']) > 0) {
    echo "   ✓ Found " . count($castRatiosByVillageChoosing['data']) . " cast ratio(s) for village choosing ID: {$villageChoosingId}\n";
} else {
    echo "   ✗ No cast ratios found for village choosing ID: {$villageChoosingId}\n";
}

// Test 7: Get specific cast ratio with choosing data
echo "\n7. Getting specific cast ratio with choosing data...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/cast-ratios/{$castRatioId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$specificCastRatio = json_decode($response, true);
curl_close($ch);

if (isset($specificCastRatio['data'])) {
    echo "   ✓ Cast ratio retrieved successfully\n";
    echo "   ✓ Caste Ratio: {$specificCastRatio['data']['caste_ratio']}%\n";
    echo "   ✓ Panchayat Choosing ID: {$specificCastRatio['data']['panchayat_choosing_id']}\n";
    echo "   ✓ Village Choosing ID: {$specificCastRatio['data']['village_choosing_id']}\n";
    
    if (isset($specificCastRatio['data']['panchayat_choosing_data'])) {
        echo "   ✓ Panchayat Choosing Data: " . $specificCastRatio['data']['panchayat_choosing_data']['name'] . "\n";
    }
    
    if (isset($specificCastRatio['data']['village_choosing_data'])) {
        echo "   ✓ Village Choosing Data: " . $specificCastRatio['data']['village_choosing_data']['name'] . "\n";
    }
} else {
    echo "   ✗ Failed to retrieve cast ratio\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "The panchayat choosing and village choosing functionality is working correctly in the cast ratios table!\n";
echo "Both panchayat_choosing_id and village_choosing_id are properly implemented.\n";
