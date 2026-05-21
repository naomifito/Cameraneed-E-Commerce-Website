<?php
/**
 * Database Connection Test File
 * SnapRent Multimedia
 * 
 * This file tests the connection to your MySQL database.
 * Access this file in your browser to check if the connection is working properly.
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
    <title>SnapRent Database Connection Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-slate-900">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-blue-900 mb-4">SnapRent Database Connection Test</h1>
        
        <?php
        try {
            // Try to establish connection
            $conn = getConnection();
            
            echo '<div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-md mb-6">';
            echo '<p class="font-semibold">✅ Connection successful!</p>';
            echo '<p class="text-sm mt-1">Successfully connected to the MySQL database.</p>';
            echo '</div>';
            
            // Check if tables exist
            $tables = [
                'category', 'product', 'customer', 
                'orders', 'order_item', 'payment'
            ];
            
            echo '<div class="bg-white border rounded-md p-4">';
            echo '<h2 class="font-semibold text-slate-800 mb-2">Database Structure Check</h2>';
            
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
                    echo "<div class=\"flex items-center gap-2 text-sm\">";
                    echo "<span class=\"text-red-600\">✗</span>";
                    echo "<span class=\"font-medium\">$table</span>";
                    echo "<span class=\"text-xs text-red-500\">(table not found)</span>";
                    echo "</div>";
                }
            }
            echo '</div>';
            
            echo '<p class="text-sm text-slate-500 mt-4 border-t pt-2">If any tables are missing, make sure you have run the snaprent_db.sql file in your database.</p>';
            echo '</div>';
            
            // Test a simple query to validate we can read data
            $sql = "SELECT COUNT(*) as category_count FROM category";
            try {
                $result = fetchOne($sql);
                echo '<div class="mt-4 bg-blue-50 border border-blue-200 p-4 rounded-md">';
                echo '<p class="font-semibold text-blue-700">Sample Query Result</p>';
                echo '<p class="text-sm text-blue-600 mt-1">Category count: ' . htmlspecialchars($result['category_count']) . '</p>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="mt-4 bg-yellow-50 border border-yellow-200 p-4 rounded-md">';
                echo '<p class="font-semibold text-yellow-700">Query Test Failed</p>';
                echo '<p class="text-sm text-yellow-600 mt-1">Could not execute sample query.</p>';
                echo '</div>';
            }
            
        } catch (PDOException $e) {
            // Connection failed
            echo '<div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md">';
            echo '<p class="font-semibold">❌ Connection failed</p>';
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
        
        <div class="mt-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-800">Next Steps</h2>
            
            <div class="bg-white rounded-md border p-4">
                <h3 class="font-medium text-slate-800">1. Import the Database Schema</h3>
                <p class="text-sm text-slate-600 mt-1">If you haven't already, import the SQL schema file:</p>
                <div class="bg-slate-50 p-3 rounded mt-2 text-sm font-mono">
                    mysql -u root -p snaprent < snaprent_db.sql
                </div>
                <p class="text-xs text-slate-500 mt-2">Alternatively, you can use phpMyAdmin or another MySQL client to import the file.</p>
            </div>
            
            <div class="bg-white rounded-md border p-4">
                <h3 class="font-medium text-slate-800">2. Use the API</h3>
                <p class="text-sm text-slate-600 mt-1">Include the API adapter in your HTML files:</p>
                <div class="bg-slate-50 p-3 rounded mt-2 text-sm font-mono">
                    &lt;script src="js/api-adapter.js"&gt;&lt;/script&gt;
                </div>
                <p class="text-xs text-slate-500 mt-2">Add this before your main app.js file to allow API communication.</p>
            </div>
        </div>
        
        <footer class="mt-8 pt-4 border-t text-xs text-slate-500">
            <p>SnapRent Multimedia - Database Connection Test</p>
            <p class="mt-1">Time of test: <?php echo date('Y-m-d H:i:s'); ?></p>
        </footer>
    </div>
</body>
</html>
