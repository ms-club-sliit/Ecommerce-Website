<?php
// Migration script to add remember_token column to existing users table
require_once 'config/database.php';

$conn = getDBConnection();

// Check if remember_token column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'remember_token'");

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) DEFAULT NULL";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Successfully added remember_token column to users table!";
    } else {
        echo "❌ Error adding column: " . $conn->error;
    }
} else {
    echo "ℹ️ Column remember_token already exists in users table.";
}

$conn->close();
?>
