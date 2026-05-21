<?php
require_once __DIR__ . '/../config/db_connection.php';

class Product {
    // Get all products
    public static function getAll() {
        $sql = "SELECT p.product_id, c.name AS category_name, p.name, p.price_per_day, 
                p.image_url, p.specs, p.stock, p.is_available 
                FROM product p 
                JOIN category c ON p.category_id = c.category_id";
        return fetchAll($sql);
    }
    
    // Get product by ID
    public static function getById($productId) {
        $sql = "SELECT p.product_id, c.name AS category_name, p.name, p.price_per_day, 
                p.image_url, p.specs, p.stock, p.is_available 
                FROM product p 
                JOIN category c ON p.category_id = c.category_id 
                WHERE p.product_id = ?";
        return fetchOne($sql, [$productId]);
    }
    
    // Get products by category
    public static function getByCategory($categoryId) {
        $sql = "SELECT p.product_id, c.name AS category_name, p.name, p.price_per_day, 
                p.image_url, p.specs, p.stock, p.is_available 
                FROM product p 
                JOIN category c ON p.category_id = c.category_id 
                WHERE p.category_id = ?";
        return fetchAll($sql, [$categoryId]);
    }
    
    // Get available products (in stock and available for rent)
    public static function getAvailable() {
        $sql = "SELECT p.product_id, c.name AS category_name, p.name, p.price_per_day, 
                p.image_url, p.specs, p.stock, p.is_available 
                FROM product p 
                JOIN category c ON p.category_id = c.category_id 
                WHERE p.is_available = TRUE AND p.stock > 0";
        return fetchAll($sql);
    }
    
    // Update product stock
    public static function updateStock($productId, $newStock) {
        $sql = "UPDATE product SET stock = ? WHERE product_id = ?";
        return update($sql, [$newStock, $productId]);
    }
    
    // Check if product is available in requested quantity
    public static function checkAvailability($productId, $quantity) {
        $product = self::getById($productId);
        if (!$product) {
            return false;
        }
        
        return ($product['is_available'] && $product['stock'] >= $quantity);
    }
}
?>
