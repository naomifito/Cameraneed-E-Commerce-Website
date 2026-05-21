<?php
// API for payment-related operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

            try {
                $result = Payment::recordPayment(
                    $data['orderId'],
                    $data['amount'],
                    $data['paymentMethod'],
                    $transactionId,
                    $proofOfPayment
                );

                if (is_int($result)) {
                    echo json_encode(['success' => true, 'payment_id' => $result]);
                } else {
                    // Try to provide a payment_id even if the model couldn't return it.
                    $latest = Payment::getByOrderId($data['orderId']);
                    $paymentId = null;
                    if (is_array($latest) && count($latest) > 0 && isset($latest[0]['payment_id'])) {
                        $paymentId = (int)$latest[0]['payment_id'];
                    }
                    echo json_encode(['success' => $result === true, 'payment_id' => $paymentId]);
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        elseif ($action === 'update_status' && isset($data['paymentId'], $data['status'])) {
            // Update payment status
            try {
                $result = Payment::updateStatus($data['paymentId'], $data['status']);

                // If payment is confirmed, update order status as well
                if ($data['status'] === 'DIKONFIRMASI' && isset($data['orderId'])) {
                    Order::updateStatus($data['orderId'], 'PEMBAYARAN_DIKONFIRMASI');
                }

                // Payment::updateStatus returns rowcount; normalize to boolean
                echo json_encode(['success' => ($result !== false && $result !== null)]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
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
