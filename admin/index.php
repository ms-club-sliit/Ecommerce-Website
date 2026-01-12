<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$conn = getDBConnection();

// Get statistics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

// Check if orders table exists before querying
$tablesResult = $conn->query("SHOW TABLES LIKE 'orders'");
if ($tablesResult->num_rows > 0) {
    $totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
    // Check if total_amount column exists
    $columnsResult = $conn->query("SHOW COLUMNS FROM orders LIKE 'total_amount'");
    if ($columnsResult->num_rows > 0) {
        $totalRevenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
    } else {
        $totalRevenue = 0;
    }
} else {
    $totalOrders = 0;
    $totalRevenue = 0;
}

// Get recent users
$recentUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Get low stock products
$lowStock = $conn->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VedaLife</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="index.php" class="admin-nav-item active">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
                <a href="products.php" class="admin-nav-item">
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
                <div class="nav-section-title">Quick Actions</div>
                <a href="../products.php" class="admin-nav-item shop-link" target="_blank">
                    <i class="fas fa-shopping-bag"></i>
                    Visit Shop
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <h1>Dashboard Overview</h1>
                <div class="admin-user-menu">
                    <a href="../products.php" class="visit-shop-btn" target="_blank">
                        <i class="fas fa-shopping-bag"></i> Visit Shop
                    </a>
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
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card info">
                        <div class="stat-header">
                            <div class="stat-title">Total Users</div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                        <div class="stat-label">Registered users</div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-title">Total Products</div>
                            <div class="stat-icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($totalProducts); ?></div>
                        <div class="stat-label">Products in catalog</div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-title">Total Orders</div>
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($totalOrders); ?></div>
                        <div class="stat-label">Orders placed</div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-header">
                            <div class="stat-title">Total Revenue</div>
                            <div class="stat-icon">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                        </div>
                        <div class="stat-value">₹<?php echo number_format($totalRevenue, 2); ?></div>
                        <div class="stat-label">Total earnings</div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>Recent Users</h2>
                        <a href="users.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentUsers->num_rows > 0): ?>
                                <?php while ($user = $recentUsers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px; color: #95a5a6;">
                                        No users found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Low Stock Products -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>Low Stock Alert</h2>
                        <a href="products.php" class="btn btn-warning btn-sm">Manage Products</a>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($lowStock->num_rows > 0): ?>
                                <?php while ($product = $lowStock->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if ($product['image']): ?>
                                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #ecf0f1; border-radius: 5px;"></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td>
                                            <?php if ($product['stock'] == 0): ?>
                                                <span class="badge badge-danger">Out of Stock</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Low Stock</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #95a5a6;">
                                        All products are well stocked!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 style="margin-bottom: 15px;">Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="add_product.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add New Product
                            </a>
                            <a href="users.php" class="btn btn-primary">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="export_products_pdf.php" class="btn btn-warning">
                                <i class="fas fa-file-pdf"></i> Export Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
