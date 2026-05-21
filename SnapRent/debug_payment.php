<?php
/**
 * Debug untuk payment page
 */
header('Content-Type: application/json');

// Get order ID from URL
$orderId = $_GET['orderId'] ?? '';

echo json_encode([
    'orderId' => $orderId,
    'available_orders' => []
]);

// Test API call
if ($orderId) {
    try {
        $response = file_get_contents("http://localhost/SnapRent/api/orders.php?action=get_by_id&id=$orderId");
        if ($response !== false) {
            $order = json_decode($response, true);
            echo json_encode([
                'api_response' => $order,
                'response_raw' => $response
            ]);
        } else {
            echo json_encode(['error' => 'Failed to call API']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Test all orders
try {
    $allOrdersResponse = file_get_contents("http://localhost/SnapRent/api/orders.php");
    if ($allOrdersResponse !== false) {
        $allOrders = json_decode($allOrdersResponse, true);
        echo json_encode(['all_orders' => $allOrders]);
    }
} catch (Exception $e) {
    echo json_encode(['error_all_orders' => $e->getMessage()]);
}
?>
