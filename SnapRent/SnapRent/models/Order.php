<?php
require_once __DIR__ . '/../config/db_connection.php';

class Order {
    // Get all orders
    public static function getAll() {
        $sql = "SELECT o.*, c.full_name, c.whatsapp FROM orders o 
                JOIN customer c ON o.customer_id = c.customer_id 
                ORDER BY o.created_at DESC";
        return fetchAll($sql);
    }
    
    // Get order by ID
    public static function getById($orderId) {
        $sql = "SELECT o.*, c.full_name, c.whatsapp, c.ktp_id FROM orders o 
                JOIN customer c ON o.customer_id = c.customer_id 
                WHERE o.order_id = ?";
        return fetchOne($sql, [$orderId]);
    }
    
    // Get orders with status filter
    public static function getByStatus($status) {
        $sql = "SELECT o.*, c.full_name, c.whatsapp FROM orders o 
                JOIN customer c ON o.customer_id = c.customer_id 
                WHERE o.status = ? 
                ORDER BY o.created_at DESC";
        return fetchAll($sql, [$status]);
    }
    
    // Get order items for an order
    public static function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name, p.image_url, c.name as category_name 
                FROM order_item oi 
                JOIN product p ON oi.product_id = p.product_id 
                JOIN category c ON p.category_id = c.category_id 
                WHERE oi.order_id = ?";
        return fetchAll($sql, [$orderId]);
    }
    
    // Create new order (using stored procedure)
    public static function create($fullName, $whatsApp, $ktpId, $rentalDate, $notes) {
        try {
            $conn = getConnection();
            $stmt = $conn->prepare("CALL sp_create_order(?, ?, ?, ?, ?, @order_id)");
            $stmt->execute([$fullName, $whatsApp, $ktpId, $rentalDate, $notes]);
            
            // Get the output parameter
            $result = $conn->query("SELECT @order_id as order_id")->fetch(PDO::FETCH_ASSOC);
            return $result['order_id'];
        } catch(PDOException $e) {
            die("Error creating order: " . $e->getMessage());
        }
    }
    
    // Add item to order (using stored procedure)
    public static function addOrderItem($orderId, $productId, $quantity, $duration) {
        try {
            $conn = getConnection();
            $stmt = $conn->prepare("CALL sp_add_order_item(?, ?, ?, ?)");
            $stmt->execute([$orderId, $productId, $quantity, $duration]);
            return true;
        } catch(PDOException $e) {
            die("Error adding order item: " . $e->getMessage());
        }
    }
    
    // Update order status (using stored procedure)
    public static function updateStatus($orderId, $newStatus) {
        try {
            $conn = getConnection();
            $stmt = $conn->prepare("CALL sp_update_order_status(?, ?)");
            $stmt->execute([$orderId, $newStatus]);
            return true;
        } catch(PDOException $e) {
            die("Error updating order status: " . $e->getMessage());
        }
    }
    
    // Get upcoming orders (rental date is today or in the future)
    public static function getUpcomingOrders() {
        $sql = "SELECT o.*, c.full_name, c.whatsapp FROM orders o 
                JOIN customer c ON o.customer_id = c.customer_id 
                WHERE o.rental_date >= CURDATE() 
                ORDER BY o.rental_date ASC";
        return fetchAll($sql);
    }
}
?>
