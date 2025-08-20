<?php

$baseUrl = 'http://localhost:8000/api';

function req($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $res, json_decode($res, true)];
}

echo "=== EMPLOYEE TYPES GUARD TEST ===\n";

// Login for protected actions
[$code, $raw, $json] = req('POST', "$baseUrl/login", [
    'email' => 'Sandeep@example.com',
    'password' => '12345678'
]);
if ($code !== 200) { echo "Login failed: $raw\n"; exit(1); }
$token = $json['token'];
$auth = ['Authorization: Bearer ' . $token];

// Create with token (should succeed)
echo "Create with token...\n";
[$code, $raw, $json] = req('POST', "$baseUrl/employee-types", ['type_name' => 'Temp Guard', 'status' => '1'], $auth);
if ($code !== 201) { echo "Create failed: $raw\n"; exit(1); }
$id = $json['employee_type']['id'] ?? null;
echo "Created ID: $id\n";

// Public list without token (should be 200)
echo "Public list...\n";
[$code, $raw] = req('GET', "$baseUrl/employee-types");
echo "List status: $code\n";

// Public show without token (should be 200)
echo "Public show...\n";
[$code, $raw] = req('GET', "$baseUrl/employee-types/$id");
echo "Show status: $code\n";

// Update without token (should be 401)
echo "Update without token...\n";
[$code, $raw] = req('PUT', "$baseUrl/employee-types/$id", ['type_name' => 'Updated NoAuth']);
echo "Update(no auth) status: $code\n";

// Delete with token (should be 200)
echo "Delete with token...\n";
[$code, $raw] = req('DELETE', "$baseUrl/employee-types/$id", null, $auth);
echo "Delete status: $code\n";

echo "=== DONE ===\n";
