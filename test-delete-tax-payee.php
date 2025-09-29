<?php

/**
 * Test Tax Payee Delete Functionality
 */

echo "🧪 Testing Tax Payee Delete Functionality\n";
echo "========================================\n\n";

// First, let's create a tax payee to delete
$createData = [
    'title' => '1',
    'name' => 'Delete Test User ' . time(),
    'nic' => '88332338' . rand(10, 99) . 'V',
    'tel' => '0778590294',
    'address' => '126 Delete Test Address',
    'email' => 'deletetest' . time() . '@gmail.com'
];

echo "📋 Creating test tax payee...\n";
echo json_encode($createData, JSON_PRETTY_PRINT) . "\n\n";

// Create the tax payee
$createUrl = 'http://127.0.0.1:8000/api/tax-payees';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $createUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($createData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$createResponse = curl_exec($ch);
$createHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$createError = curl_error($ch);

curl_close($ch);

echo "📊 Create Response:\n";
echo "HTTP Code: $createHttpCode\n";

if ($createError) {
    echo "❌ cURL Error: $createError\n";
    exit(1);
}

$createData = json_decode($createResponse, true);
if ($createHttpCode === 201 && isset($createData['data']['id'])) {
    $taxPayeeId = $createData['data']['id'];
    echo "✅ Tax payee created successfully with ID: $taxPayeeId\n\n";
    
    // Now test the delete functionality
    echo "🗑️ Testing delete functionality...\n";
    
    $deleteUrl = "http://127.0.0.1:8000/api/tax-payees/$taxPayeeId";
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $deleteUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $deleteResponse = curl_exec($ch);
    $deleteHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $deleteError = curl_error($ch);
    
    curl_close($ch);
    
    echo "📊 Delete Response:\n";
    echo "HTTP Code: $deleteHttpCode\n";
    
    if ($deleteError) {
        echo "❌ cURL Error: $deleteError\n";
    } else {
        $deleteData = json_decode($deleteResponse, true);
        if ($deleteData) {
            echo "Response Body:\n";
            echo json_encode($deleteData, JSON_PRETTY_PRINT) . "\n";
            
            if ($deleteHttpCode === 200) {
                echo "\n✅ SUCCESS: Tax payee deleted successfully!\n";
            } else {
                echo "\n❌ ERROR: Delete failed\n";
            }
        } else {
            echo "Raw Response: $deleteResponse\n";
        }
    }
    
} else {
    echo "❌ Failed to create tax payee for testing\n";
    echo "Response: $createResponse\n";
}

echo "\n🎉 Delete test completed!\n";
