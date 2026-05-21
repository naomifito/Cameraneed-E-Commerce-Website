<?php
/**
 * Debug API untuk SnapRent
 * File ini digunakan untuk menguji API secara langsung
 */

// Set header untuk JSON
header('Content-Type: application/json');

// Include required files
require_once 'config/db_connection.php';

// Test koneksi database
try {
    $conn = getConnection();
    echo json_encode(['status' => 'success', 'message' => 'Database connection OK']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Test stored procedure sp_create_order
echo "\n\n--- Testing sp_create_order ---\n";
try {
    $conn = getConnection();
    $stmt = $conn->prepare("CALL sp_create_order(?, ?, ?, ?, ?, @order_id)");
    $stmt->execute(['Test User', '081234567890', 'ID12345', '2025-12-20', 'Test order']);
    
    $result = $conn->query("SELECT @order_id as order_id")->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'order_id' => $result]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'sp_create_order failed: ' . $e->getMessage()]);
}

// Test API POST request
echo "\n\n--- Testing API POST ---\n";
$data = [
    'fullName' => 'Test Customer API',
    'whatsApp' => '081234567891',
    'ktpId' => 'ID54321',
    'rentalDate' => '2025-12-21',
    'notes' => 'Test via API',
    'items' => [
        ['productId' => 'kamera-mirrorless-1', 'quantity' => 1, 'duration' => 2]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents('http://localhost/SnapRent/api/orders.php?action=create', false, $context);

if ($result === false) {
    echo json_encode(['status' => 'error', 'message' => 'API call failed']);
} else {
    echo json_encode(['status' => 'success', 'api_response' => json_decode($result)]);
}
?>
