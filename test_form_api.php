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

echo "=== FORM API TEST ===\n";

// Login (existing test user or your own credentials)
[$code, $raw, $json] = req('POST', "$baseUrl/login", [
    'email' => 'Sandeep@example.com',
    'password' => '12345678'
]);
if ($code !== 200) { echo "Login failed: $raw\n"; exit(1); }
$token = $json['token'];
$auth = ['Authorization: Bearer ' . $token];

echo "Create form...\n";
[$code, $raw, $json] = req('POST', "$baseUrl/forms", [
    'name' => 'Customer Feedback',
    'questions' => [
        ['question' => 'Overall experience?', 'type' => 'single_choice', 'required' => true, 'options' => ['Excellent','Good','Average','Poor']],
        ['question' => 'Issues you faced?', 'type' => 'multiple_choice', 'options' => ['Price','Support','Quality','Delivery']],
        ['question' => 'Additional comments', 'type' => 'long_text', 'placeholder' => 'Write here...']
    ]
], $auth);
if ($code !== 201) { echo "Create failed: $raw\n"; exit(1); }
$formId = $json['form']['id'];
echo "Created form ID: $formId\n";

echo "List forms...\n";
[$code, $raw, $list] = req('GET', "$baseUrl/forms?page=1&per_page=10&search=Feedback");
echo "List status: $code, count: " . count($list['forms']) . "\n";

echo "Get form...\n";
[$code, $raw, $show] = req('GET', "$baseUrl/forms/$formId");
echo "Show status: $code, name: " . $show['form']['name'] . "\n";

echo "Update form...\n";
[$code, $raw, $upd] = req('PUT', "$baseUrl/forms/$formId", [
    'name' => 'Customer Feedback (v2)',
    'questions' => [
        ['question' => 'Overall experience?', 'type' => 'single_choice', 'required' => true, 'options' => ['Excellent','Good','Average','Poor']],
        ['question' => 'Any comments?', 'type' => 'long_text', 'placeholder' => 'Write here...']
    ]
], $auth);
echo "Update status: $code, name: " . $upd['form']['name'] . "\n";

echo "Delete form...\n";
[$code, $raw] = req('DELETE', "$baseUrl/forms/$formId", null, $auth);
echo "Delete status: $code\n";

echo "=== DONE ===\n";
