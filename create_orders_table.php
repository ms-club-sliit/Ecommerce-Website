<?php
/**
 * Create Orders Table
 * Run this file once to create the orders table in the database
 */

require_once 'config/database.php';

try {
    $conn = getDBConnection();
    
    // SQL to create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        user_name VARCHAR(255) NOT NULL,
        account_number VARCHAR(100) NOT NULL,
        bank VARCHAR(100) NOT NULL,
        branch VARCHAR(255) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        confirmation_status ENUM('yes', 'no') NOT NULL DEFAULT 'no',
        bank_slip_path VARCHAR(500) DEFAULT NULL,
        order_status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Orders table created successfully!<br>";
        echo "You can now use the checkout system.<br>";
        echo "<a href='checkout.php'>Go to Checkout</a> | <a href='dashboard.php'>Go to Dashboard</a>";
    } else {
        echo "❌ Error creating table: " . $conn->error;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
