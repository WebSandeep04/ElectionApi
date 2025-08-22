<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

$baseUrl = 'http://localhost:8000/api';

echo "Testing Panchayat and Village Choosing APIs\n";
echo "==========================================\n\n";

// Test Panchayat Choosings
echo "1. Testing Panchayat Choosings:\n";
echo "   Getting all panchayat choosings...\n";

try {
    $response = Http::get("$baseUrl/panchayat-choosings");
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Found " . count($data['panchayat_choosings']) . " panchayat choosings:\n";
        foreach ($data['panchayat_choosings'] as $choosing) {
            echo "      - ID: {$choosing['id']}, Name: {$choosing['name']}, Status: {$choosing['status']}\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Active Panchayat Choosings
echo "2. Testing Active Panchayat Choosings:\n";
echo "   Getting active panchayat choosings...\n";

try {
    $response = Http::get("$baseUrl/panchayat-choosings/active");
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Found " . count($data['panchayat_choosings']) . " active panchayat choosings:\n";
        foreach ($data['panchayat_choosings'] as $choosing) {
            echo "      - ID: {$choosing['id']}, Name: {$choosing['name']}\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Village Choosings
echo "3. Testing Village Choosings:\n";
echo "   Getting all village choosings...\n";

try {
    $response = Http::get("$baseUrl/village-choosings");
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Found " . count($data['village_choosings']) . " village choosings:\n";
        foreach ($data['village_choosings'] as $choosing) {
            echo "      - ID: {$choosing['id']}, Name: {$choosing['name']}, Status: {$choosing['status']}\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Active Village Choosings
echo "4. Testing Active Village Choosings:\n";
echo "   Getting active village choosings...\n";

try {
    $response = Http::get("$baseUrl/village-choosings/active");
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Found " . count($data['village_choosings']) . " active village choosings:\n";
        foreach ($data['village_choosings'] as $choosing) {
            echo "      - ID: {$choosing['id']}, Name: {$choosing['name']}\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test creating a panchayat with choosing_id
echo "5. Testing Panchayat Creation with Choosing ID:\n";
echo "   Creating a panchayat with panchayat_choosing_id=1...\n";

try {
    $response = Http::post("$baseUrl/panchayats", [
        'loksabha_id' => 1,
        'vidhansabha_id' => 1,
        'block_id' => 1,
        'panchayat_choosing_id' => 1, // Mahanager pallika
        'panchayat_choosing' => 'Mahanager pallika', // Keep for backward compatibility
        'panchayat_name' => 'Test Panchayat with Choosing',
        'panchayat_status' => '1'
    ]);
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Created panchayat:\n";
        echo "      - ID: {$data['panchayat']['id']}\n";
        echo "      - Name: {$data['panchayat']['panchayat_name']}\n";
        echo "      - Choosing ID: {$data['panchayat']['panchayat_choosing_id']}\n";
        echo "      - Choosing Name: {$data['panchayat']['panchayat_choosing']}\n";
        if (isset($data['panchayat']['panchayat_choosing_data'])) {
            echo "      - Choosing Data: " . json_encode($data['panchayat']['panchayat_choosing_data']) . "\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test creating a village with choosing_id
echo "6. Testing Village Creation with Choosing ID:\n";
echo "   Creating a village with village_choosing_id=2...\n";

try {
    $response = Http::post("$baseUrl/villages", [
        'loksabha_id' => 1,
        'vidhansabha_id' => 1,
        'block_id' => 1,
        'panchayat_id' => 1,
        'village_choosing_id' => 2, // Village
        'village_choosing' => 'Village', // Keep for backward compatibility
        'village_name' => 'Test Village with Choosing',
        'village_status' => '1'
    ]);
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Created village:\n";
        echo "      - ID: {$data['data']['id']}\n";
        echo "      - Name: {$data['data']['village_name']}\n";
        echo "      - Choosing ID: {$data['data']['village_choosing_id']}\n";
        echo "      - Choosing Name: {$data['data']['village_choosing']}\n";
        if (isset($data['data']['village_choosing_data'])) {
            echo "      - Choosing Data: " . json_encode($data['data']['village_choosing_data']) . "\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test creating a booth with choosing_ids
echo "7. Testing Booth Creation with Choosing IDs:\n";
echo "   Creating a booth with panchayat_choosing_id=1 and village_choosing_id=2...\n";

try {
    $response = Http::post("$baseUrl/booths", [
        'loksabha_id' => 1,
        'vidhansabha_id' => 1,
        'block_id' => 1,
        'panchayat_id' => 1,
        'panchayat_choosing_id' => 1, // Mahanager pallika
        'panchayat_choosing' => 'Mahanager pallika', // Backward compatibility
        'village_id' => 1,
        'village_choosing_id' => 2, // Village
        'village_choosing' => 'Village', // Backward compatibility
        'booth_name' => 'Test Booth with Choosing',
        'booth_status' => '1'
    ]);
    
    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ Success! Created booth:\n";
        echo "      - ID: {$data['data']['id']}\n";
        echo "      - Name: {$data['data']['booth_name']}\n";
        echo "      - Panchayat Choosing ID: {$data['data']['panchayat_choosing_id']}\n";
        echo "      - Village Choosing ID: {$data['data']['village_choosing_id']}\n";
        if (isset($data['data']['panchayat_choosing_data'])) {
            echo "      - Panchayat Choosing Data: " . json_encode($data['data']['panchayat_choosing_data']) . "\n";
        }
        if (isset($data['data']['village_choosing_data'])) {
            echo "      - Village Choosing Data: " . json_encode($data['data']['village_choosing_data']) . "\n";
        }
    } else {
        echo "   ❌ Failed! Status: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Testing completed!\n";
