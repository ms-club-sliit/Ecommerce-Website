<?php
/**
 * Add Remaining Products Script
 * Adds products 7-12 to match the original products.php page
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Add Remaining Products</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Add Remaining Products</h1>";

try {
    $conn = getDBConnection();
    
    // First, update product 7 image if it exists
    echo "<h2>Updating Product 7 Image...</h2>";
    $stmt = $conn->prepare("UPDATE products SET image = ? WHERE id = 7");
    $image7 = 'assets/images/product7.jpg';
    $stmt->bind_param("s", $image7);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<div class='success'>✓ Updated product ID 7 with image: assets/images/product7.jpg</div>";
    }
    $stmt->close();
    
    // Add remaining products (8-12) based on original products.php
    echo "<h2>Adding Products 8-12...</h2>";
    
    $newProducts = [
        [
            'name' => 'Moringa Energy Powder',
            'category' => 'Supplements',
            'description' => 'Nutrient-rich superfood powder for natural energy and vitality.',
            'price' => 449.00,
            'image' => 'assets/images/product8.jpg',
            'stock' => 45
        ],
        [
            'name' => 'Bhringraj Hair Oil',
            'category' => 'Hair Care',
            'description' => 'Traditional hair growth oil with bhringraj and amla for lustrous hair.',
            'price' => 349.00,
            'image' => 'assets/images/product9.jpg',
            'stock' => 60
        ],
        [
            'name' => 'Tulsi Immunity Tea',
            'category' => 'Immunity',
            'description' => 'Sacred holy basil tea blend to boost immunity and reduce stress.',
            'price' => 249.00,
            'image' => 'assets/images/product10.jpg',
            'stock' => 100
        ],
        [
            'name' => 'Mahanarayan Pain Relief Oil',
            'category' => 'Joint Care',
            'description' => 'Ayurvedic massage oil for joint and muscle pain relief.',
            'price' => 499.00,
            'image' => 'assets/images/product11.jpg',
            'stock' => 35
        ],
        [
            'name' => 'Shatavari Women\'s Wellness',
            'category' => 'Immunity',
            'description' => 'Hormonal balance and reproductive health support for women.',
            'price' => 549.00,
            'image' => 'assets/images/product12.jpg',
            'stock' => 50
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($newProducts as $product) {
        $stmt->bind_param("sssdsi", 
            $product['name'], 
            $product['category'], 
            $product['description'], 
            $product['price'], 
            $product['image'], 
            $product['stock']
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✓ Added product: {$product['name']} with image: {$product['image']}</div>";
        } else {
            echo "<div class='error'>✗ Failed to add product {$product['name']}: " . $conn->error . "</div>";
        }
    }
    
    $stmt->close();
    
    // Show total count
    $totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
    
    echo "<h2>Complete!</h2>";
    echo "<div class='success'>Successfully added remaining products. Total products in database: {$totalProducts}</div>";
    echo "<p><a href='products.php'>View Products Page</a> | <a href='admin/products.php'>View Admin Products</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
