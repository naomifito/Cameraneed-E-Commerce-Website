<?php
// API for product-related operations
header('Content-Type: application/json');
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        if ($action === 'get_by_id' && isset($_GET['id'])) {
            // Get specific product by ID
            $product = Product::getById($_GET['id']);
            echo json_encode($product ? $product : ['error' => 'Product not found']);
        } 
        elseif ($action === 'get_by_category' && isset($_GET['category_id'])) {
            // Get products by category
            $products = Product::getByCategory($_GET['category_id']);
            echo json_encode($products);
        }
        elseif ($action === 'get_available') {
            // Get only available products
            $products = Product::getAvailable();
            echo json_encode($products);
        }
        elseif ($action === 'categories') {
            // Get all categories
            $categories = Category::getAll();
            echo json_encode($categories);
        }
        elseif ($action === 'categories_with_count') {
            // Get categories with product count
            $categories = Category::getAllWithProductCount();
            echo json_encode($categories);
        }
        else {
            // Get all products by default
            $products = Product::getAll();
            echo json_encode($products);
        }
        break;
    
    case 'POST':
        // For any POST requests requiring authentication
        // This would be extended in a real application
        echo json_encode(['error' => 'Unauthorized']);
        break;
        
    default:
        // Method not allowed
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
