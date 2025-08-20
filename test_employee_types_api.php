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

echo "=== EMPLOYEE TYPES API TEST ===\n";

// Login
[$code, $raw, $json] = req('POST', "$baseUrl/login", [
    'email' => 'Sandeep@example.com',
    'password' => '12345678'
]);
if ($code !== 200) { echo "Login failed: $raw\n"; exit(1); }
$token = $json['token'];
$auth = ['Authorization: Bearer ' . $token];

// Create
[$code, $raw, $json] = req('POST', "$baseUrl/employee-types", [
    'type_name' => 'Contractor',
    'status' => '1'
], $auth);
if ($code !== 201) { echo "Create failed: $raw\n"; exit(1); }
$id = $json['employee_type']['id'] ?? $json['id'] ?? null;
echo "Created ID: $id\n";

// List
[$code, $raw] = req('GET', "$baseUrl/employee-types", null, $auth);
echo "List status: $code\n";

// Show
[$code, $raw] = req('GET', "$baseUrl/employee-types/$id", null, $auth);
echo "Show status: $code\n";

// Update
[$code, $raw] = req('PUT', "$baseUrl/employee-types/$id", [
    'type_name' => 'Full-Time',
    'status' => 'active'
], $auth);
echo "Update status: $code\n";

// Delete
[$code, $raw] = req('DELETE', "$baseUrl/employee-types/$id", null, $auth);
echo "Delete status: $code\n";

echo "=== DONE ===\n";
