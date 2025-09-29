<?php

/**
 * Test getByPayee method in TaxAssessmentController
 */

$baseUrl = 'http://127.0.0.1:8000/api';

echo "🧪 Testing getByPayee Method\n";
echo "==========================\n\n";

// Test with payee ID 1 (we know this exists from previous tests)
$payeeId = 1;

echo "📋 Test Data:\n";
echo "Payee ID: " . $payeeId . "\n\n";

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/tax-assessments/payee/' . $payeeId);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "📡 Response:\n";
echo "HTTP Code: " . $httpCode . "\n";

if ($error) {
    echo "❌ cURL Error: " . $error . "\n";
} else {
    echo "Response Body: " . $response . "\n";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "\n📊 Parsed Response:\n";
        if (is_array($data)) {
            echo "Number of assessments: " . count($data) . "\n";
            foreach ($data as $index => $assessment) {
                echo "\nAssessment " . ($index + 1) . ":\n";
                echo "- ID: " . ($assessment['id'] ?? 'N/A') . "\n";
                echo "- Year: " . ($assessment['year'] ?? 'N/A') . "\n";
                echo "- Amount: " . ($assessment['amount'] ?? 'N/A') . "\n";
                echo "- Status: " . ($assessment['status'] ?? 'N/A') . "\n";
                echo "- Due Date: " . ($assessment['due_date'] ?? 'N/A') . "\n";
                
                if (isset($assessment['tax_property'])) {
                    echo "- Property ID: " . ($assessment['tax_property']['id'] ?? 'N/A') . "\n";
                    echo "- Property Name: " . ($assessment['tax_property']['property_name'] ?? 'N/A') . "\n";
                    
                    if (isset($assessment['tax_property']['tax_payee'])) {
                        echo "- Payee Name: " . ($assessment['tax_property']['tax_payee']['name'] ?? 'N/A') . "\n";
                        echo "- Payee NIC: " . ($assessment['tax_property']['tax_payee']['nic'] ?? 'N/A') . "\n";
                    }
                }
            }
        } else {
            echo "Response is not an array: " . gettype($data) . "\n";
        }
    }
}

echo "\n✅ Test completed!\n";