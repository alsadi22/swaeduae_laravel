<?php
// Simple test to check events page
$url = 'https://swaeduae.ae/events';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Length: " . strlen($response) . "\n";

// Check for key content
if (strpos($response, 'Volunteer Opportunities') !== false) {
    echo "✓ Found 'Volunteer Opportunities' title\n";
} else {
    echo "✗ Missing 'Volunteer Opportunities' title\n";
}

if (strpos($response, 'Search Events') !== false) {
    echo "✓ Found 'Search Events' form\n";
} else {
    echo "✗ Missing 'Search Events' form\n";
}

if (strpos($response, 'No Events Found') !== false) {
    echo "✓ Found 'No Events Found' message (no events to display)\n";
} else if (strpos($response, 'bg-white rounded-lg shadow-md overflow-hidden') !== false) {
    echo "✓ Found event cards (events are being displayed)\n";
} else {
    echo "? No clear indication of events or no-events message\n";
}

// Check for errors
if (strpos($response, 'error') !== false || strpos($response, 'Error') !== false) {
    echo "⚠ Found error text in response\n";
}

if (strpos($response, 'Exception') !== false) {
    echo "⚠ Found exception in response\n";
}

// Show first 500 characters
echo "\nFirst 500 characters of response:\n";
echo substr($response, 0, 500) . "\n";