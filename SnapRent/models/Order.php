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
        $conn = getConnection();

        try {
            $conn->beginTransaction();

            // Find existing customer by WhatsApp
            $stmtFind = $conn->prepare("SELECT customer_id FROM customer WHERE whatsapp = ? LIMIT 1 FOR UPDATE");
            $stmtFind->execute([$whatsApp]);
            $existing = $stmtFind->fetch(PDO::FETCH_ASSOC);

            if ($existing && isset($existing['customer_id'])) {
                $customerId = (int)$existing['customer_id'];
                // Update customer with latest input so name is not stale/random
                $stmtUpdate = $conn->prepare("UPDATE customer SET full_name = ?, ktp_id = ?, updated_at = CURRENT_TIMESTAMP WHERE customer_id = ?");
                $stmtUpdate->execute([$fullName, $ktpId, $customerId]);
            } else {
                $stmtInsert = $conn->prepare("INSERT INTO customer(full_name, whatsapp, ktp_id) VALUES(?, ?, ?)");
                $stmtInsert->execute([$fullName, $whatsApp, $ktpId]);
                $customerId = (int)$conn->lastInsertId();
            }

            // Generate order ID similar to the previous stored procedure
            $orderId = 'ORD-' . date('ym') . '-' . strtoupper(substr(md5((string)mt_rand()), 0, 6));

            $stmtOrder = $conn->prepare("INSERT INTO orders(order_id, customer_id, rental_date, notes, status, total_amount) VALUES(?, ?, ?, ?, 'MENUNGGU_PEMBAYARAN', 0)");
            $stmtOrder->execute([$orderId, $customerId, $rentalDate, $notes]);

            $conn->commit();
            return $orderId;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
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

    // Update order + items (replace all items) and adjust stock accordingly
    public static function updateItems($orderId, $fullName, $whatsApp, $ktpId, $rentalDate, $notes, $items) {
        $conn = getConnection();

        try {
            $conn->beginTransaction();

            // Lock order row
            $stmtOrder = $conn->prepare("SELECT * FROM orders WHERE order_id = ? FOR UPDATE");
            $stmtOrder->execute([$orderId]);
            $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
            if (!$order) {
                throw new Exception('Order not found');
            }

            // Update customer data
            $customerId = $order['customer_id'];
            $stmtCustomer = $conn->prepare("UPDATE customer SET full_name = ?, whatsapp = ?, ktp_id = ? WHERE customer_id = ?");
            $stmtCustomer->execute([$fullName, $whatsApp, $ktpId, $customerId]);

            // Restore stock from existing items
            $stmtExisting = $conn->prepare("SELECT product_id, quantity FROM order_item WHERE order_id = ?");
            $stmtExisting->execute([$orderId]);
            $existingItems = $stmtExisting->fetchAll(PDO::FETCH_ASSOC);
            foreach ($existingItems as $it) {
                $stmtRestore = $conn->prepare("UPDATE product SET stock = stock + ? WHERE product_id = ?");
                $stmtRestore->execute([$it['quantity'], $it['product_id']]);
            }

            // Remove existing items and reset total
            $stmtDelete = $conn->prepare("DELETE FROM order_item WHERE order_id = ?");
            $stmtDelete->execute([$orderId]);

            $stmtReset = $conn->prepare("UPDATE orders SET total_amount = 0, rental_date = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE order_id = ?");
            $stmtReset->execute([$rentalDate, $notes, $orderId]);

            // Add new items (will reserve stock via sp_add_order_item)
            foreach ($items as $item) {
                if (!isset($item['productId'], $item['quantity'], $item['duration'])) {
                    throw new Exception('Invalid items payload');
                }

                $productId = $item['productId'];
                $qty = (int)$item['quantity'];
                $dur = (int)$item['duration'];

                if ($qty < 1 || $dur < 1) {
                    throw new Exception('Quantity and duration must be at least 1');
                }

                $stmtAdd = $conn->prepare("CALL sp_add_order_item(?, ?, ?, ?)");
                $stmtAdd->execute([$orderId, $productId, $qty, $dur]);
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
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
            throw $e;
        }
    }

    // Delete order and related records (hard delete)
    public static function deleteOrder($orderId) {
        $conn = getConnection();

        try {
            $conn->beginTransaction();

            // Lock order row
            $stmtOrder = $conn->prepare("SELECT order_id, customer_id FROM orders WHERE order_id = ? FOR UPDATE");
            $stmtOrder->execute([$orderId]);
            $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
            if (!$order) {
                throw new Exception('Order not found');
            }

            $customerId = isset($order['customer_id']) ? (int)$order['customer_id'] : null;

            // Restore stock from order items
            $stmtItems = $conn->prepare("SELECT product_id, quantity FROM order_item WHERE order_id = ?");
            $stmtItems->execute([$orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as $it) {
                $stmtRestore = $conn->prepare("UPDATE product SET stock = stock + ? WHERE product_id = ?");
                $stmtRestore->execute([(int)$it['quantity'], $it['product_id']]);
            }

            // Delete dependent rows first
            $stmtDelPayment = $conn->prepare("DELETE FROM payment WHERE order_id = ?");
            $stmtDelPayment->execute([$orderId]);

            $stmtDelItems = $conn->prepare("DELETE FROM order_item WHERE order_id = ?");
            $stmtDelItems->execute([$orderId]);

            $stmtDelOrder = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmtDelOrder->execute([$orderId]);

            // Optional cleanup: delete customer if they have no remaining orders
            if (!empty($customerId)) {
                $stmtCountOrders = $conn->prepare("SELECT COUNT(*) AS cnt FROM orders WHERE customer_id = ?");
                $stmtCountOrders->execute([$customerId]);
                $cntRow = $stmtCountOrders->fetch(PDO::FETCH_ASSOC);
                $remaining = $cntRow ? (int)$cntRow['cnt'] : 0;

                if ($remaining === 0) {
                    $stmtDelCustomer = $conn->prepare("DELETE FROM customer WHERE customer_id = ?");
                    $stmtDelCustomer->execute([$customerId]);
                }
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
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
