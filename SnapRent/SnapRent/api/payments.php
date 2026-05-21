<?php
// API for payment-related operations
header('Content-Type: application/json');
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        if ($action === 'by_order_id' && isset($_GET['order_id'])) {
            // Get payments for a specific order
            $payments = Payment::getByOrderId($_GET['order_id']);
            echo json_encode($payments);
        }
        elseif ($action === 'filter' && isset($_GET['status'])) {
            // Get payments with filters
            $status = $_GET['status'];
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            
            $payments = Payment::getAllWithFilters($status, $startDate, $endDate);
            echo json_encode($payments);
        }
        else {
            // By default, return all payments
            $payments = Payment::getAllWithFilters();
            echo json_encode($payments);
        }
        break;
    
    case 'POST':
        // Handle payment creation and updates
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'record' && isset($data['orderId'], $data['amount'], $data['paymentMethod'])) {
            // Record a new payment
            $transactionId = isset($data['transactionId']) ? $data['transactionId'] : null;
            $proofOfPayment = isset($data['proofOfPayment']) ? $data['proofOfPayment'] : null;
            
            $result = Payment::recordPayment(
                $data['orderId'], 
                $data['amount'], 
                $data['paymentMethod'], 
                $transactionId, 
                $proofOfPayment
            );
            
            echo json_encode(['success' => $result]);
        }
        elseif ($action === 'update_status' && isset($data['paymentId'], $data['status'])) {
            // Update payment status
            $result = Payment::updateStatus($data['paymentId'], $data['status']);
            
            // If payment is confirmed, update order status as well
            if ($data['status'] === 'DIKONFIRMASI' && isset($data['orderId'])) {
                Order::updateStatus($data['orderId'], 'PEMBAYARAN_DIKONFIRMASI');
            }
            
            echo json_encode(['success' => $result]);
        }
        else {
            echo json_encode(['error' => 'Invalid parameters']);
        }
        break;
        
    default:
        // Method not allowed
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
