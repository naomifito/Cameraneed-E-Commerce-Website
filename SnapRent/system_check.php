<?php
/**
 * SnapRent System Compatibility Check
 * 
 * This file tests the compatibility of all SnapRent components
 * including database connection, API functionality, and JavaScript integration.
 */

// Include database connection
require_once __DIR__ . '/config/db_connection.php';

// Set header to display as HTML
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapRent System Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-slate-900">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-blue-900 mb-4">SnapRent System Check</h1>
        
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-2">1. PHP & Database</h2>
            
            <?php
            try {
                // Try to establish connection
                $conn = getConnection();
                
                echo '<div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-md mb-6">';
                echo '<p class="font-semibold">✅ Database connection successful!</p>';
                echo '<p class="text-sm mt-1">Successfully connected to the MySQL database.</p>';
                echo '</div>';
                
                // Check if tables exist
                $tables = [
                    'category', 'product', 'customer', 
                    'orders', 'order_item', 'payment'
                ];
                
                echo '<div class="bg-white border rounded-md p-4">';
                echo '<h3 class="font-semibold text-slate-800 mb-2">Database Tables</h3>';
                
                $allTablesExist = true;
                echo '<div class="space-y-2">';
                foreach ($tables as $table) {
                    // Check if table exists
                    $sql = "SHOW TABLES LIKE ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$table]);
                    $exists = ($stmt->rowCount() > 0);
                    
                    if ($exists) {
                        // Count rows in table
                        $countSql = "SELECT COUNT(*) FROM $table";
                        $countStmt = $conn->query($countSql);
                        $rowCount = $countStmt->fetchColumn();
                        
                        echo "<div class=\"flex items-center gap-2 text-sm\">";
                        echo "<span class=\"text-green-600\">✓</span>";
                        echo "<span class=\"font-medium\">$table</span>";
                        echo "<span class=\"text-xs text-slate-500\">($rowCount rows)</span>";
                        echo "</div>";
                    } else {
                        $allTablesExist = false;
                        echo "<div class=\"flex items-center gap-2 text-sm\">";
                        echo "<span class=\"text-red-600\">✗</span>";
                        echo "<span class=\"font-medium\">$table</span>";
                        echo "<span class=\"text-xs text-red-500\">(table not found)</span>";
                        echo "</div>";
                    }
                }
                echo '</div>';
                
                if (!$allTablesExist) {
                    echo '<div class="mt-4 p-3 bg-yellow-50 rounded text-sm">';
                    echo '<p class="font-medium text-yellow-700">Some tables are missing</p>';
                    echo '<p class="text-yellow-600 mt-1">Run the SQL script to create the database schema:</p>';
                    echo '<div class="bg-slate-100 p-2 rounded mt-2 text-xs font-mono">';
                    echo 'mysql -u root -p snaprent < snaprent_db.sql';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                
            } catch (PDOException $e) {
                // Connection failed
                echo '<div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md">';
                echo '<p class="font-semibold">❌ Database connection failed</p>';
                echo '<p class="text-sm mt-1">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<div class="mt-4 p-3 bg-red-100 rounded text-sm">';
                echo '<p class="font-medium">Troubleshooting steps:</p>';
                echo '<ol class="list-decimal ml-5 space-y-1 mt-2">';
                echo '<li>Make sure your MySQL server is running</li>';
                echo '<li>Check if the database credentials in config/db_connection.php are correct</li>';
                echo '<li>Verify that the "snaprent" database exists in your MySQL server</li>';
                echo '<li>Ensure your MySQL user has proper permissions</li>';
                echo '</ol>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-2">2. File Structure Check</h2>
            
            <div class="bg-white border rounded-md p-4">
                <h3 class="font-semibold text-slate-800 mb-2">Required Files</h3>
                
                <div class="space-y-2">
                    <?php
                    $requiredFiles = [
                        'config/db_connection.php',
                        'api/products.php',
                        'api/orders.php',
                        'api/payments.php',
                        'models/Product.php',
                        'models/Category.php',
                        'models/Order.php',
                        'models/Payment.php',
                        'js/api-adapter.js',
                        'js/db-adapter.js',
                        'app.js',
                        'index.html',
                        'index.php',
                        '.htaccess'
                    ];
                    
                    $allFilesExist = true;
                    foreach ($requiredFiles as $file) {
                        if (file_exists(__DIR__ . '/' . $file)) {
                            echo "<div class=\"flex items-center gap-2 text-sm\">";
                            echo "<span class=\"text-green-600\">✓</span>";
                            echo "<span class=\"font-medium\">$file</span>";
                            echo "</div>";
                        } else {
                            $allFilesExist = false;
                            echo "<div class=\"flex items-center gap-2 text-sm\">";
                            echo "<span class=\"text-red-600\">✗</span>";
                            echo "<span class=\"font-medium\">$file</span>";
                            echo "<span class=\"text-xs text-red-500\">(file not found)</span>";
                            echo "</div>";
                        }
                    }
                    
                    if (!$allFilesExist) {
                        echo '<div class="mt-4 p-3 bg-yellow-50 rounded text-sm">';
                        echo '<p class="font-medium text-yellow-700">Some required files are missing</p>';
                        echo '<p class="text-yellow-600 mt-1">Make sure all necessary files are in their correct locations.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-2">3. API Test</h2>
            
            <div class="bg-white border rounded-md p-4">
                <h3 class="font-semibold text-slate-800 mb-2">API Endpoints</h3>
                
                <div id="api-test-results" class="space-y-2">
                    <p class="text-sm text-slate-600">Testing API endpoints...</p>
                </div>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-2">4. JavaScript Integration</h2>
            
            <div class="bg-white border rounded-md p-4">
                <h3 class="font-semibold text-slate-800 mb-2">JavaScript Compatibility</h3>
                
                <div id="js-test-results" class="space-y-2">
                    <p class="text-sm text-slate-600">Testing JavaScript integration...</p>
                </div>
            </div>
        </section>
        
        <div class="flex justify-center my-8">
            <a href="index.html" class="px-5 py-2.5 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Go to Homepage</a>
        </div>
        
        <footer class="mt-8 pt-4 border-t text-xs text-slate-500">
            <p>SnapRent Multimedia - System Compatibility Check</p>
            <p class="mt-1">Time of test: <?php echo date('Y-m-d H:i:s'); ?></p>
        </footer>
    </div>
    
    <script src="js/api-adapter.js"></script>
    <script src="js/db-adapter.js"></script>
    <script src="app.js"></script>
    <script>
        // Test API endpoints
        async function testApiEndpoints() {
            const apiTestResultsEl = document.getElementById('api-test-results');
            apiTestResultsEl.innerHTML = '';
            
            const endpoints = [
                { name: 'Products API', url: 'api/products.php' },
                { name: 'Categories', url: 'api/products.php?action=categories' },
                { name: 'Orders API', url: 'api/orders.php' },
                { name: 'Payments API', url: 'api/payments.php' }
            ];
            
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint.url);
                    const success = response.ok;
                    
                    const resultEl = document.createElement('div');
                    resultEl.className = 'flex items-center gap-2 text-sm';
                    
                    if (success) {
                        resultEl.innerHTML = `
                            <span class="text-green-600">✓</span>
                            <span class="font-medium">${endpoint.name}</span>
                            <span class="text-xs text-slate-500">(${response.status} ${response.statusText})</span>
                        `;
                    } else {
                        resultEl.innerHTML = `
                            <span class="text-red-600">✗</span>
                            <span class="font-medium">${endpoint.name}</span>
                            <span class="text-xs text-red-500">(${response.status} ${response.statusText})</span>
                        `;
                    }
                    
                    apiTestResultsEl.appendChild(resultEl);
                } catch (error) {
                    const resultEl = document.createElement('div');
                    resultEl.className = 'flex items-center gap-2 text-sm';
                    resultEl.innerHTML = `
                        <span class="text-red-600">✗</span>
                        <span class="font-medium">${endpoint.name}</span>
                        <span class="text-xs text-red-500">(${error.message})</span>
                    `;
                    apiTestResultsEl.appendChild(resultEl);
                }
            }
        }
        
        // Test JavaScript integration
        function testJavaScriptIntegration() {
            const jsTestResultsEl = document.getElementById('js-test-results');
            jsTestResultsEl.innerHTML = '';
            
            const tests = [
                { name: 'App.js loaded', condition: typeof window.App !== 'undefined' },
                { name: 'API adapter loaded', condition: typeof window.SnapRentAPI !== 'undefined' },
                { name: 'DB adapter loaded', condition: typeof window.DbAdapter !== 'undefined' },
                { name: 'Cart functionality', condition: window.App && typeof window.App.loadCart === 'function' }
            ];
            
            for (const test of tests) {
                const resultEl = document.createElement('div');
                resultEl.className = 'flex items-center gap-2 text-sm';
                
                if (test.condition) {
                    resultEl.innerHTML = `
                        <span class="text-green-600">✓</span>
                        <span class="font-medium">${test.name}</span>
                    `;
                } else {
                    resultEl.innerHTML = `
                        <span class="text-red-600">✗</span>
                        <span class="font-medium">${test.name}</span>
                        <span class="text-xs text-red-500">(not available)</span>
                    `;
                }
                
                jsTestResultsEl.appendChild(resultEl);
            }
        }
        
        // Run tests when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            testApiEndpoints();
            testJavaScriptIntegration();
        });
    </script>
</body>
</html>
