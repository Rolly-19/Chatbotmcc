<?php
include './apikey.php';

// Your API key
$apiKey = $api_key['API_KEY'];

// API endpoint
$url = 'https://api.cohere.com/v1/chat';

// Initialize cURL session
$ch = curl_init($url);

// Headers
$headers = [
    'Accept: application/json',
    'Content-Type: application/json',
    "Authorization: Bearer $apiKey"
];

// Get POST data
$postData = file_get_contents('php://input'); // Get raw POST data

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute cURL request and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    // Print response
    echo $response;
}

// Close cURL session
curl_close($ch);