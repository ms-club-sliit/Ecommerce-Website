<?php
/**
 * Update Product Images Script
 * Updates the product images in the database to match existing image files
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Update Product Images</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Update Product Images</h1>";

try {
    $conn = getDBConnection();
    
    // Update product images to match existing files
    $updates = [
        ['id' => 1, 'image' => 'assets/images/product1.png'],  // Ashwagandha Capsules
        ['id' => 2, 'image' => 'assets/images/product2.jpg'],  // Triphala Powder
        ['id' => 3, 'image' => 'assets/images/product3.webp'], // Brahmi Oil
        ['id' => 4, 'image' => 'assets/images/product4.png'],  // Turmeric Capsules
        ['id' => 5, 'image' => 'assets/images/product5.jpg'],  // Neem Face Pack
        ['id' => 6, 'image' => 'assets/images/product6.jpg'],  // Chyawanprash
    ];
    
    echo "<h2>Updating Product Images...</h2>";
    
    $stmt = $conn->prepare("UPDATE products SET image = ? WHERE id = ?");
    
    foreach ($updates as $update) {
        $stmt->bind_param("si", $update['image'], $update['id']);
        if ($stmt->execute()) {
            echo "<div class='success'>✓ Updated product ID {$update['id']} with image: {$update['image']}</div>";
        } else {
            echo "<div class='error'>✗ Failed to update product ID {$update['id']}: " . $conn->error . "</div>";
        }
    }
    
    $stmt->close();
    
    echo "<h2>Update Complete!</h2>";
    echo "<div class='success'>All product images have been updated successfully.</div>";
    echo "<p><a href='products.php'>View Products Page</a> | <a href='admin/products.php'>View Admin Products</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
