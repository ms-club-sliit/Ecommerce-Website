<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    if (empty($name) || empty($category) || empty($price)) {
        $error = 'Name, category, and price are required';
    } else {
        $imagePath = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = '../uploads/products/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('product_') . '.' . $extension;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = 'uploads/products/' . $filename;
                } else {
                    $error = 'Failed to upload image';
                }
            } else {
                $error = 'Invalid image type. Only JPG, PNG, and WEBP are allowed';
            }
        }
        
        if (empty($error)) {
            $conn = getDBConnection();
            $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdsi", $name, $category, $description, $price, $imagePath, $stock);
            
            if ($stmt->execute()) {
                $success = 'Product added successfully!';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Error adding product: ' . $conn->error;
            }
            
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - VedaLife Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="logo">
                <i class="fas fa-leaf"></i>
                <span>VedaLife Admin</span>
            </div>
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
                <a href="products.php" class="admin-nav-item active">
                    <i class="fas fa-box"></i>
                    Product Management
                </a>
                <div class="nav-section-title">Reports</div>
                <a href="export_users_pdf.php" class="admin-nav-item">
                    <i class="fas fa-file-pdf"></i>
                    Export Users PDF
                </a>
                <a href="export_products_pdf.php" class="admin-nav-item">
                    <i class="fas fa-file-pdf"></i>
                    Export Products PDF
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <h1>Add New Product</h1>
                <div class="admin-user-menu">
                    <div class="admin-user-info">
                        <div class="admin-user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </div>
                    <a href="../logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <a href="products.php" style="margin-left: 15px;">View all products</a>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="admin-form" onsubmit="return validateProductForm()">
                    <h2 style="margin-bottom: 20px;">Product Details</h2>
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-tag"></i> Product Name *
                        </label>
                        <input type="text" id="name" name="name" class="form-control" 
                               placeholder="Enter product name" required>
                    </div>

                    <div class="form-group">
                        <label for="category">
                            <i class="fas fa-folder"></i> Category *
                        </label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="Immunity">Immunity</option>
                            <option value="Digestion">Digestion</option>
                            <option value="Hair Care">Hair Care</option>
                            <option value="Skin Care">Skin Care</option>
                            <option value="Joint Care">Joint Care</option>
                            <option value="Energy">Energy</option>
                            <option value="Sleep">Sleep</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <textarea id="description" name="description" class="form-control" 
                                  placeholder="Enter product description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">
                            <i class="fas fa-rupee-sign"></i> Price (₹) *
                        </label>
                        <input type="number" id="price" name="price" class="form-control" 
                               placeholder="0.00" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">
                            <i class="fas fa-boxes"></i> Stock Quantity
                        </label>
                        <input type="number" id="stock" name="stock" class="form-control" 
                               placeholder="0" min="0" value="0">
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i> Product Image
                        </label>
                        <input type="file" id="image" name="image" class="form-control" 
                               accept="image/*" onchange="previewImage(this)">
                        <div id="imagePreview" class="image-preview"></div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add Product
                        </button>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
