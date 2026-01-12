<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$error = '';
$success = '';
$product = null;

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$productId = intval($_GET['id']);
$conn = getDBConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    if (empty($name) || empty($category) || empty($price)) {
        $error = 'Name, category, and price are required';
    } else {
        // Get current product data
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentProduct = $result->fetch_assoc();
        $stmt->close();
        
        $imagePath = $currentProduct['image'];
        
        // Handle image upload if new image provided
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
                    // Delete old image if exists
                    if ($imagePath && file_exists('../' . $imagePath)) {
                        unlink('../' . $imagePath);
                    }
                    $imagePath = 'uploads/products/' . $filename;
                } else {
                    $error = 'Failed to upload image';
                }
            } else {
                $error = 'Invalid image type. Only JPG, PNG, and WEBP are allowed';
            }
        }
        
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("sssdsii", $name, $category, $description, $price, $imagePath, $stock, $productId);
            
            if ($stmt->execute()) {
                $success = 'Product updated successfully!';
            } else {
                $error = 'Error updating product: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Get product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - VedaLife Admin</title>
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
                <h1>Edit Product</h1>
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
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="admin-form" onsubmit="return validateProductForm()">
                    <h2 style="margin-bottom: 20px;">Edit Product Details</h2>
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-tag"></i> Product Name *
                        </label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category">
                            <i class="fas fa-folder"></i> Category *
                        </label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="Immunity" <?php echo $product['category'] == 'Immunity' ? 'selected' : ''; ?>>Immunity</option>
                            <option value="Digestion" <?php echo $product['category'] == 'Digestion' ? 'selected' : ''; ?>>Digestion</option>
                            <option value="Hair Care" <?php echo $product['category'] == 'Hair Care' ? 'selected' : ''; ?>>Hair Care</option>
                            <option value="Skin Care" <?php echo $product['category'] == 'Skin Care' ? 'selected' : ''; ?>>Skin Care</option>
                            <option value="Joint Care" <?php echo $product['category'] == 'Joint Care' ? 'selected' : ''; ?>>Joint Care</option>
                            <option value="Energy" <?php echo $product['category'] == 'Energy' ? 'selected' : ''; ?>>Energy</option>
                            <option value="Sleep" <?php echo $product['category'] == 'Sleep' ? 'selected' : ''; ?>>Sleep</option>
                            <option value="Other" <?php echo $product['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">
                            <i class="fas fa-rupee-sign"></i> Price (₹) *
                        </label>
                        <input type="number" id="price" name="price" class="form-control" 
                               value="<?php echo $product['price']; ?>" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">
                            <i class="fas fa-boxes"></i> Stock Quantity
                        </label>
                        <input type="number" id="stock" name="stock" class="form-control" 
                               value="<?php echo $product['stock']; ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i> Product Image
                        </label>
                        <?php if ($product['image']): ?>
                            <div class="image-preview">
                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Current image">
                                <p style="font-size: 12px; color: #7f8c8d; margin-top: 5px;">Current image (upload new to replace)</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" class="form-control" 
                               accept="image/*" onchange="previewImage(this)">
                        <div id="imagePreview" class="image-preview"></div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Product
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
