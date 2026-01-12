<?php
/**
 * Admin Database Initialization Script
 * Creates admins and products tables with default admin user
 * Safe to run multiple times
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Admin Database Initialization</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Admin Database Initialization</h1>";

try {
    $conn = getDBConnection();
    
    // Create admins table
    echo "<h2>Creating Admins Table...</h2>";
    $createAdminsTable = "CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($createAdminsTable) === TRUE) {
        echo "<div class='success'>✓ Admins table created successfully</div>";
    } else {
        throw new Exception("Error creating admins table: " . $conn->error);
    }
    
    // Insert default admin if not exists
    echo "<h2>Creating Default Admin User...</h2>";
    $adminEmail = 'admin@example.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $adminName = 'System Administrator';
    
    // Check if admin already exists
    $checkAdmin = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $checkAdmin->bind_param("s", $adminEmail);
    $checkAdmin->execute();
    $result = $checkAdmin->get_result();
    
    if ($result->num_rows == 0) {
        $insertAdmin = $conn->prepare("INSERT INTO admins (email, password, name) VALUES (?, ?, ?)");
        $insertAdmin->bind_param("sss", $adminEmail, $adminPassword, $adminName);
        
        if ($insertAdmin->execute()) {
            echo "<div class='success'>✓ Default admin user created successfully</div>";
            echo "<div class='info'><strong>Admin Credentials:</strong><br>Email: admin@example.com<br>Password: admin123</div>";
        } else {
            throw new Exception("Error creating admin user: " . $conn->error);
        }
        $insertAdmin->close();
    } else {
        echo "<div class='info'>ℹ Admin user already exists</div>";
    }
    $checkAdmin->close();
    
    // Create products table
    echo "<h2>Creating Products Table...</h2>";
    $createProductsTable = "CREATE TABLE IF NOT EXISTS products (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        category VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255),
        stock INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX(category),
        INDEX(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($createProductsTable) === TRUE) {
        echo "<div class='success'>✓ Products table created successfully</div>";
    } else {
        throw new Exception("Error creating products table: " . $conn->error);
    }
    
    // Insert sample products if table is empty
    echo "<h2>Checking for Sample Products...</h2>";
    $countProducts = $conn->query("SELECT COUNT(*) as count FROM products");
    $productCount = $countProducts->fetch_assoc()['count'];
    
    if ($productCount == 0) {
        echo "<div class='info'>Inserting sample products...</div>";
        
        $sampleProducts = [
            ['Ashwagandha Capsules', 'Immunity', 'Premium quality Ashwagandha root extract capsules for stress relief and vitality', 599.00, 'assets/images/ashwagandha.jpg', 50],
            ['Triphala Powder', 'Digestion', 'Traditional Ayurvedic blend for digestive health and detoxification', 299.00, 'assets/images/triphala.jpg', 100],
            ['Brahmi Oil', 'Hair Care', 'Pure Brahmi oil for hair growth and mental clarity', 399.00, 'assets/images/brahmi-oil.jpg', 75],
            ['Turmeric Capsules', 'Immunity', 'Organic turmeric with curcumin for anti-inflammatory support', 449.00, 'assets/images/turmeric.jpg', 60],
            ['Neem Face Pack', 'Skin Care', 'Natural neem powder for clear and glowing skin', 249.00, 'assets/images/neem.jpg', 80],
            ['Chyawanprash', 'Immunity', 'Traditional immunity booster with 40+ herbs', 699.00, 'assets/images/chyawanprash.jpg', 40]
        ];
        
        $insertProduct = $conn->prepare("INSERT INTO products (name, category, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleProducts as $product) {
            $insertProduct->bind_param("sssdsi", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
            $insertProduct->execute();
        }
        
        $insertProduct->close();
        echo "<div class='success'>✓ Sample products inserted successfully</div>";
    } else {
        echo "<div class='info'>ℹ Products table already contains {$productCount} products</div>";
    }
    
    echo "<h2>Database Initialization Complete!</h2>";
    echo "<div class='success'>All tables have been created successfully. You can now proceed to use the admin dashboard.</div>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
