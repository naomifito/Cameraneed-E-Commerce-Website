<?php
require_once __DIR__ . '/../config/db_connection.php';

class Payment {
    // Get payment by order ID
    public static function getByOrderId($orderId) {
        $sql = "SELECT * FROM payment WHERE order_id = ? ORDER BY created_at DESC";
        return fetchAll($sql, [$orderId]);
    }
    
    // Record a new payment (using stored procedure)
    public static function recordPayment($orderId, $amount, $paymentMethod, $transactionId, $proofOfPayment) {
        try {
            $conn = getConnection();
            $stmt = $conn->prepare("CALL sp_record_payment(?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $amount, $paymentMethod, $transactionId, $proofOfPayment]);
            return true;
        } catch(PDOException $e) {
            die("Error recording payment: " . $e->getMessage());
        }
    }
    
    // Update payment status
    public static function updateStatus($paymentId, $newStatus) {
        $sql = "UPDATE payment SET payment_status = ?, updated_at = CURRENT_TIMESTAMP WHERE payment_id = ?";
        return update($sql, [$newStatus, $paymentId]);
    }
    
    // Get all payments with filters
    public static function getAllWithFilters($status = null, $startDate = null, $endDate = null) {
        $params = [];
        $sql = "SELECT p.*, o.total_amount as order_total, c.full_name 
                FROM payment p
                JOIN orders o ON p.order_id = o.order_id
                JOIN customer c ON o.customer_id = c.customer_id
                WHERE 1=1 ";
        
        if ($status) {
            $sql .= "AND p.payment_status = ? ";
            $params[] = $status;
        }
        
        if ($startDate) {
            $sql .= "AND DATE(p.created_at) >= ? ";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= "AND DATE(p.created_at) <= ? ";
            $params[] = $endDate;
        }
        
        $sql .= "ORDER BY p.created_at DESC";
        
        return fetchAll($sql, $params);
    }
}
?>
