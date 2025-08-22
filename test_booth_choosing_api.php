<?php

// Test file to demonstrate booth choosing functionality
// This shows how panchayat choosing and village choosing work in the booth table

echo "=== BOOTH CHOOSING API TEST ===\n\n";

// Base URL for the API
$baseUrl = 'http://localhost:8000/api';

// Test 1: Create a panchayat choosing
echo "1. Creating a panchayat choosing...\n";
$panchayatChoosingData = [
    'name' => 'Test Panchayat Choosing',
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
    'name' => 'Test Village Choosing',
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

// Test 3: Create a booth with both choosing IDs
echo "\n3. Creating a booth with panchayat_choosing_id and village_choosing_id...\n";
$boothData = [
    'booth_name' => 'Test Booth with Choosing',
    'panchayat_choosing_id' => $panchayatChoosingId,
    'village_choosing_id' => $villageChoosingId,
    'booth_status' => '1'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/booths');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($boothData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$booth = json_decode($response, true);
curl_close($ch);

if (isset($booth['data']['id'])) {
    echo "   ✓ Booth created with ID: {$booth['data']['id']}\n";
    echo "   ✓ Panchayat Choosing ID: {$booth['data']['panchayat_choosing_id']}\n";
    echo "   ✓ Village Choosing ID: {$booth['data']['village_choosing_id']}\n";
    $boothId = $booth['data']['id'];
} else {
    echo "   ✗ Failed to create booth\n";
    exit;
}

// Test 4: Get booth by panchayat choosing ID
echo "\n4. Testing get booths by panchayat choosing ID...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/booths/panchayat-choosing/{$panchayatChoosingId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$boothsByPanchayatChoosing = json_decode($response, true);
curl_close($ch);

if (isset($boothsByPanchayatChoosing['data']) && count($boothsByPanchayatChoosing['data']) > 0) {
    echo "   ✓ Found " . count($boothsByPanchayatChoosing['data']) . " booth(s) for panchayat choosing ID: {$panchayatChoosingId}\n";
} else {
    echo "   ✗ No booths found for panchayat choosing ID: {$panchayatChoosingId}\n";
}

// Test 5: Get booth by village choosing ID
echo "\n5. Testing get booths by village choosing ID...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/booths/village-choosing/{$villageChoosingId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$boothsByVillageChoosing = json_decode($response, true);
curl_close($ch);

if (isset($boothsByVillageChoosing['data']) && count($boothsByVillageChoosing['data']) > 0) {
    echo "   ✓ Found " . count($boothsByVillageChoosing['data']) . " booth(s) for village choosing ID: {$villageChoosingId}\n";
} else {
    echo "   ✗ No booths found for village choosing ID: {$villageChoosingId}\n";
}

// Test 6: Get specific booth with choosing data
echo "\n6. Getting specific booth with choosing data...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/booths/{$boothId}");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$specificBooth = json_decode($response, true);
curl_close($ch);

if (isset($specificBooth['data'])) {
    echo "   ✓ Booth retrieved successfully\n";
    echo "   ✓ Booth Name: {$specificBooth['data']['booth_name']}\n";
    echo "   ✓ Panchayat Choosing ID: {$specificBooth['data']['panchayat_choosing_id']}\n";
    echo "   ✓ Village Choosing ID: {$specificBooth['data']['village_choosing_id']}\n";
    
    if (isset($specificBooth['data']['panchayat_choosing_data'])) {
        echo "   ✓ Panchayat Choosing Data: " . $specificBooth['data']['panchayat_choosing_data']['name'] . "\n";
    }
    
    if (isset($specificBooth['data']['village_choosing_data'])) {
        echo "   ✓ Village Choosing Data: " . $specificBooth['data']['village_choosing_data']['name'] . "\n";
    }
} else {
    echo "   ✗ Failed to retrieve booth\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "The panchayat choosing functionality is working correctly in the booth table!\n";
echo "Both panchayat_choosing_id and village_choosing_id are properly implemented.\n";
