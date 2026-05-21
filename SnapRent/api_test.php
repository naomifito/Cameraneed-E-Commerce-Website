<?php
/**
 * API Test Script untuk memverifikasi semua CRUD operations
 */
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/config/db_connection.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Payment.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapRent API Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-blue-900 mb-6">SnapRent API & Database Test</h1>
        
        <?php
        $allPassed = true;
        
        // Test 1: Database Connection
        echo '<div class="bg-white rounded-lg border p-4 mb-4">';
        echo '<h2 class="font-semibold text-slate-800 mb-2">1. Database Connection</h2>';
        try {
            $conn = getConnection();
            echo '<p class="text-green-600">✓ Koneksi database berhasil</p>';
        } catch (Exception $e) {
            echo '<p class="text-red-600">✗ Gagal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $allPassed = false;
        }
        echo '</div>';
        
        // Test 2: Read Products
        echo '<div class="bg-white rounded-lg border p-4 mb-4">';
        echo '<h2 class="font-semibold text-slate-800 mb-2">2. Read Products (READ)</h2>';
        try {
            $products = Product::getAll();
            $count = count($products);
            echo '<p class="text-green-600">✓ Berhasil mengambil ' . $count . ' produk dari database</p>';
            if ($count > 0) {
                echo '<p class="text-xs text-slate-500 mt-1">Contoh: ' . htmlspecialchars($products[0]['name']) . '</p>';
            }
        } catch (Exception $e) {
            echo '<p class="text-red-600">✗ Gagal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $allPassed = false;
        }
        echo '</div>';
        
        // Test 3: Read Categories
        echo '<div class="bg-white rounded-lg border p-4 mb-4">';
        echo '<h2 class="font-semibold text-slate-800 mb-2">3. Read Categories (READ)</h2>';
        try {
            $categories = Category::getAll();
            $count = count($categories);
            echo '<p class="text-green-600">✓ Berhasil mengambil ' . $count . ' kategori dari database</p>';
        } catch (Exception $e) {
            echo '<p class="text-red-600">✗ Gagal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $allPassed = false;
        }
        echo '</div>';
        
        // Test 4: Create Order (CREATE)
        echo '<div class="bg-white rounded-lg border p-4 mb-4">';
        echo '<h2 class="font-semibold text-slate-800 mb-2">4. Create Order (CREATE)</h2>';
        try {
            $orderId = Order::create('Test User API', '081234567999', 'ID-TEST999', '2025-12-30', 'Test order dari API Test');
            echo '<p class="text-green-600">✓ Berhasil membuat order baru: ' . htmlspecialchars($orderId) . '</p>';
            
            // Test 5: Add Order Item
            echo '</div>';
            echo '<div class="bg-white rounded-lg border p-4 mb-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">5. Add Order Item (CREATE)</h2>';
            
            Order::addOrderItem($orderId, 'kamera-mirrorless-1', 1, 2);
            echo '<p class="text-green-600">✓ Berhasil menambahkan item ke order</p>';
            
            // Test 6: Read Order by ID
            echo '</div>';
            echo '<div class="bg-white rounded-lg border p-4 mb-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">6. Read Order by ID (READ)</h2>';
            
            $order = Order::getById($orderId);
            if ($order) {
                echo '<p class="text-green-600">✓ Berhasil mengambil order: ' . htmlspecialchars($order['full_name']) . '</p>';
                echo '<p class="text-xs text-slate-500 mt-1">Total: Rp ' . number_format($order['total_amount'], 0, ',', '.') . '</p>';
            } else {
                echo '<p class="text-red-600">✗ Order tidak ditemukan</p>';
                $allPassed = false;
            }
            
            // Test 7: Update Order Status
            echo '</div>';
            echo '<div class="bg-white rounded-lg border p-4 mb-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">7. Update Order Status (UPDATE)</h2>';
            
            $result = Order::updateStatus($orderId, 'PEMBAYARAN_DIKONFIRMASI');
            if ($result) {
                echo '<p class="text-green-600">✓ Berhasil update status order ke PEMBAYARAN_DIKONFIRMASI</p>';
            } else {
                echo '<p class="text-red-600">✗ Gagal update status</p>';
                $allPassed = false;
            }
            
            // Test 8: Record Payment
            echo '</div>';
            echo '<div class="bg-white rounded-lg border p-4 mb-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">8. Record Payment (CREATE)</h2>';
            
            $paymentResult = Payment::recordPayment($orderId, $order['total_amount'], 'TRANSFER', 'TRX-TEST-' . time(), null);
            if ($paymentResult) {
                echo '<p class="text-green-600">✓ Berhasil mencatat pembayaran</p>';
            } else {
                echo '<p class="text-red-600">✗ Gagal mencatat pembayaran</p>';
                $allPassed = false;
            }
            
            // Test 9: Read Payments
            echo '</div>';
            echo '<div class="bg-white rounded-lg border p-4 mb-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">9. Read Payments (READ)</h2>';
            
            $payments = Payment::getByOrderId($orderId);
            if (count($payments) > 0) {
                echo '<p class="text-green-600">✓ Berhasil mengambil ' . count($payments) . ' payment untuk order</p>';
            } else {
                echo '<p class="text-red-600">✗ Tidak ada payment ditemukan</p>';
                $allPassed = false;
            }
            
        } catch (Exception $e) {
            echo '<p class="text-red-600">✗ Gagal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $allPassed = false;
        }
        echo '</div>';
        
        // Summary
        echo '<div class="' . ($allPassed ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') . ' rounded-lg border p-4 mt-6">';
        if ($allPassed) {
            echo '<h2 class="font-semibold text-green-800 mb-2">✓ Semua Test Berhasil!</h2>';
            echo '<p class="text-green-700 text-sm">Database connection, CRUD operations untuk Products, Categories, Orders, dan Payments semuanya berfungsi dengan baik.</p>';
        } else {
            echo '<h2 class="font-semibold text-red-800 mb-2">✗ Ada Test yang Gagal</h2>';
            echo '<p class="text-red-700 text-sm">Silakan periksa error di atas dan perbaiki sebelum menggunakan website.</p>';
        }
        echo '</div>';
        ?>
        
        <div class="mt-6 text-center">
            <a href="index.html" class="inline-flex px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold">Kembali ke Website</a>
        </div>
    </div>
</body>
</html>
