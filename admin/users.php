<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$conn = getDBConnection();
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - VedaLife Admin</title>
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
                <a href="users.php" class="admin-nav-item active">
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
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <h1>User Management</h1>
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
                <!-- Users Table -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>All Users (<?php echo $users->num_rows; ?>)</h2>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="searchInput" placeholder="Search users..." 
                                   style="padding: 8px 15px; border: 1px solid #dfe6e9; border-radius: 5px;"
                                   onkeyup="filterTable('searchInput', 'usersTable')">
                            <a href="export_users_pdf.php" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <table class="admin-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users->num_rows > 0): ?>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button onclick="deleteItem('user', <?php echo $user['id']; ?>)" 
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #95a5a6;">
                                        No users found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
