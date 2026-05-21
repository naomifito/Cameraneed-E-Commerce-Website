<?php
require_once __DIR__ . '/../config/db_connection.php';

class Category {
    // Get all categories
    public static function getAll() {
        $sql = "SELECT * FROM category ORDER BY name";
        return fetchAll($sql);
    }
    
    // Get category by ID
    public static function getById($categoryId) {
        $sql = "SELECT * FROM category WHERE category_id = ?";
        return fetchOne($sql, [$categoryId]);
    }
    
    // Get category with product count
    public static function getAllWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.product_id) as product_count 
                FROM category c 
                LEFT JOIN product p ON c.category_id = p.category_id 
                GROUP BY c.category_id 
                ORDER BY c.name";
        return fetchAll($sql);
    }
}
?>
