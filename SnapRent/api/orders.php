<?php
// API for order-related operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/Order.php';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        if ($action === 'get_by_id' && isset($_GET['id'])) {
            // Get specific order by ID
            $order = Order::getById($_GET['id']);
            if ($order) {
                // Get order items as well
                $order['items'] = Order::getOrderItems($order['order_id']);
                echo json_encode($order);
            } else {
                echo json_encode(['error' => 'Order not found']);
            }
        }
        elseif ($action === 'get_by_status' && isset($_GET['status'])) {
            // Get orders by status
            $orders = Order::getByStatus($_GET['status']);
            echo json_encode($orders);
        }
        elseif ($action === 'upcoming') {
            // Get upcoming orders
            $orders = Order::getUpcomingOrders();
            echo json_encode($orders);
        }
        else {
            // Get all orders
            $orders = Order::getAll();
            echo json_encode($orders);
        }
        break;
    
    case 'POST':
        // Handle order creation and updates
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'create' && isset($data['fullName'], $data['whatsApp'], $data['ktpId'], $data['rentalDate'])) {
            // Create new order
            $notes = isset($data['notes']) ? $data['notes'] : '';
            $orderId = Order::create($data['fullName'], $data['whatsApp'], $data['ktpId'], $data['rentalDate'], $notes);
            
            // Add order items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['productId'], $item['quantity'], $item['duration'])) {
                        Order::addOrderItem($orderId, $item['productId'], $item['quantity'], $item['duration']);
                    }
                }
            }
            
            echo json_encode(['success' => true, 'order_id' => $orderId]);
        }
        elseif ($action === 'delete_order' && isset($data['orderId'])) {
            try {
                $result = Order::deleteOrder($data['orderId']);
                echo json_encode(['success' => $result === true]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        elseif ($action === 'update_items' && isset($data['orderId'], $data['fullName'], $data['whatsApp'], $data['ktpId'], $data['rentalDate'], $data['items']) && is_array($data['items'])) {
            try {
                $result = Order::updateItems(
                    $data['orderId'],
                    $data['fullName'],
                    $data['whatsApp'],
                    $data['ktpId'],
                    $data['rentalDate'],
                    isset($data['notes']) ? $data['notes'] : '',
                    $data['items']
                );
                echo json_encode(['success' => $result === true]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        elseif ($action === 'update_status' && isset($data['orderId'], $data['status'])) {
            // Update order status
            try {
                $result = Order::updateStatus($data['orderId'], $data['status']);
                echo json_encode(['success' => $result === true]);
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
