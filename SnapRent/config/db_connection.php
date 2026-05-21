<?php
/**
 * Database Connection Configuration
 * SnapRent Multimedia
 */

// Database credentials
$db_host = "localhost";      // Database host (usually localhost)
$db_name = "snaprent";       // Database name
$db_user = "root";           // Database username (default: root)
$db_pass = "";               // Database password (default: empty)

// Create connection
function getConnection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    try {
        $conn = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass
        );
        
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Return the connection
        return $conn;
    } catch(PDOException $e) {
        // Handle connection error
        throw $e;
    }
}

/**
 * Function to execute SQL query and return result
 */
function executeQuery($sql, $params = []) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        throw $e;
    }
}

/**
 * Function to fetch all results from a query
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Function to fetch a single row from a query
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Function to insert a record and return the last inserted ID
 */
function insert($sql, $params = []) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $conn->lastInsertId();
    } catch(PDOException $e) {
        throw $e;
    }
}

/**
 * Function to update a record and return the number of rows affected
 */
function update($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}

/**
 * Function to delete a record and return the number of rows affected
 */
function delete($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
?>
