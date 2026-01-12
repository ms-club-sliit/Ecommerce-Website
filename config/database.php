<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vedalife_ecommerce');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Create database if it doesn't exist
function initializeDatabase() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        $conn->select_db(DB_NAME);
        
        // Create users table
        $createTableSQL = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX(email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->query($createTableSQL) === TRUE) {
            // Add remember_token column if it doesn't exist (for existing tables)
            $result = $conn->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
            if ($result->num_rows == 0) {
                $conn->query("ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) DEFAULT NULL");
            }
            return true;
        } else {
            error_log("Error creating table: " . $conn->error);
            return false;
        }
    } else {
        error_log("Error creating database: " . $conn->error);
        return false;
    }
    
    $conn->close();
}

// Initialize database on first load
initializeDatabase();
?>
