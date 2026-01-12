<?php
/**
 * Database Migration: Add Payment Method Support
 * Run this file once to add payment-related columns to the orders table
 */

require_once 'config/database.php';

try {
    $conn = getDBConnection();
    
    echo "<h2>Database Migration: Payment Methods</h2>";
    
    // Add payment_method column
    $sql1 = "ALTER TABLE orders 
             ADD COLUMN IF NOT EXISTS payment_method ENUM('card', 'paypal', 'cod') DEFAULT 'card' 
             AFTER order_status";
    
    if ($conn->query($sql1) === TRUE) {
        echo "✅ Added payment_method column<br>";
    } else {
        echo "⚠️ payment_method column: " . $conn->error . "<br>";
    }
    
    // Add payment_status column
    $sql2 = "ALTER TABLE orders 
             ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending' 
             AFTER payment_method";
    
    if ($conn->query($sql2) === TRUE) {
        echo "✅ Added payment_status column<br>";
    } else {
        echo "⚠️ payment_status column: " . $conn->error . "<br>";
    }
    
    // Add transaction_id column for PayHere payment ID
    $sql3 = "ALTER TABLE orders 
             ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(255) DEFAULT NULL 
             AFTER payment_status";
    
    if ($conn->query($sql3) === TRUE) {
        echo "✅ Added transaction_id column<br>";
    } else {
        echo "⚠️ transaction_id column: " . $conn->error . "<br>";
    }
    
    // Add payhere_order_id column
    $sql4 = "ALTER TABLE orders 
             ADD COLUMN IF NOT EXISTS payhere_order_id VARCHAR(100) DEFAULT NULL 
             AFTER transaction_id";
    
    if ($conn->query($sql4) === TRUE) {
        echo "✅ Added payhere_order_id column<br>";
    } else {
        echo "⚠️ payhere_order_id column: " . $conn->error . "<br>";
    }
    
    // Add payment_method_type column for card details (VISA, MASTER, etc.)
    $sql5 = "ALTER TABLE orders 
             ADD COLUMN IF NOT EXISTS payment_method_type VARCHAR(50) DEFAULT NULL 
             AFTER payhere_order_id";
    
    if ($conn->query($sql5) === TRUE) {
        echo "✅ Added payment_method_type column<br>";
    } else {
        echo "⚠️ payment_method_type column: " . $conn->error . "<br>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Migration completed successfully!</h3>";
    echo "<p>Your orders table now supports multiple payment methods.</p>";
    echo "<a href='checkout.php'>Go to Checkout</a> | <a href='dashboard.php'>Go to Dashboard</a>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
