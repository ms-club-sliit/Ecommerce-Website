<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Get user details from session
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

// Get additional user details from database
require_once 'config/database.php';
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user's orders
$orders_stmt = $conn->prepare("SELECT id, user_name, amount, bank, order_status, bank_slip_path, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
$orders_stmt->close();

// Get total orders count
$count_stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_orders = $count_result->fetch_assoc()['total_orders'];
$count_stmt->close();

$conn->close();

// Calculate account age
$created_date = new DateTime($user['created_at']);
$now = new DateTime();
$account_age = $created_date->diff($now);

// Get success message if redirected from checkout
$order_success = isset($_GET['order_success']) && $_GET['order_success'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | VedaLife - Premium Ayurvedic Wellness</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-section {
            min-height: 100vh;
            background: #ffffff;
            padding: 6rem 0 4rem;
            position: relative;
            overflow: hidden;
        }


        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            color: #1A3C34;
        }

        .dashboard-header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            animation: fadeInDown 0.8s ease;
            color: #1A3C34;
        }

        .dashboard-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            animation: fadeInUp 0.8s ease 0.2s both;
            color: #333;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .dashboard-card {
            background: #ffffff;
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid #e0e0e0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
            background: #ffffff;
            border-color: #1A3C34;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .profile-card {
            grid-column: 1 / -1;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1A3C34 0%, #D4AF37 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
            border: 4px solid #1A3C34;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .profile-card h2 {
            color: #1A3C34;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .profile-card p {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .card-icon {
            font-size: 2.5rem;
            color: #1A3C34;
            margin-bottom: 1rem;
            display: block;
        }

        .card-title {
            color: #1A3C34;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .card-value {
            color: #1A3C34;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: none;
        }

        .card-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e8e8e8;
            transform: translateX(5px);
        }

        .info-item i {
            font-size: 1.5rem;
            color: #1A3C34;
            width: 30px;
            text-align: center;
        }

        .info-item-content {
            flex: 1;
        }

        .info-item-label {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }

        .info-item-value {
            color: #1A3C34;
            font-size: 1rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .dashboard-btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: white;
            color: #1A3C34;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-logout:hover {
            background: rgba(255, 59, 48, 0.8);
            border-color: rgba(255, 59, 48, 0.8);
            transform: translateY(-3px);
        }

        /* Edit Profile Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .modal-header h2 {
            color: #1A3C34;
            font-size: 1.8rem;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 2rem;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            background: rgba(26, 60, 52, 0.1);
            color: #1A3C34;
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1A3C34;
            box-shadow: 0 0 0 3px rgba(26, 60, 52, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 0.3rem;
            color: #666;
            font-size: 0.85rem;
        }

        .password-divider {
            margin: 2rem 0;
            padding: 1rem 0;
            border-top: 2px dashed #e0e0e0;
            border-bottom: 2px dashed #e0e0e0;
        }

        .password-divider p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            text-align: center;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .modal-btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-save {
            background: linear-gradient(135deg, #1A3C34 0%, #D4AF37 100%);
            color: white;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 60, 52, 0.4);
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #666;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            display: none;
        }

        .alert.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-edit {
            background: rgba(255, 255, 255, 0.9);
            color: #1A3C34;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-color: black;
        }

        .btn-edit:hover {
            background: #155724;
            color: white;
            border-color: black;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .dashboard-btn {
                width: 100%;
                justify-content: center;
            }

            .modal-content {
                padding: 1.5rem;
            }

            .modal-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">Veda<span>Life</span></a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="products.php">Shop</a>
                    <a href="about.php">About</a>
                    <a href="contact.php">Contact</a>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div class="auth-links">
                        <a href="dashboard.php" class="auth-btn" style="background: var(--color-primary); color: white;">
                            <i class="fas fa-user"></i> Dashboard
                        </a>
                        <a href="logout.php" class="auth-btn" style="background: var(--color-accent); color: white; border-color: var(--color-accent);">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <section class="dashboard-section">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Welcome Back, <?php echo htmlspecialchars($user['name']); ?>! 🌿</h1>
                <p>Your VedaLife Wellness Dashboard</p>
            </div>

            <div class="dashboard-grid">
                <!-- Profile Card -->
                <div class="dashboard-card profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
                    
                    <div class="action-buttons">
                        <button onclick="openEditModal()" class="dashboard-btn btn-edit">
                            <i class="fas fa-edit"></i>
                            Edit Profile
                        </button>
                        <a href="products.php" class="dashboard-btn btn-primary">
                            <i class="fas fa-shopping-bag"></i>
                            Continue Shopping
                        </a>
                        
                    </div>
                </div>

                <!-- Account Stats -->
                <div class="dashboard-card">
                    <i class="fas fa-calendar-check card-icon"></i>
                    <div class="card-title">Member Since</div>
                    <div class="card-value"><?php echo $created_date->format('M Y'); ?></div>
                    <div class="card-label">
                        <?php 
                        if ($account_age->days == 0) {
                            echo "Joined today!";
                        } elseif ($account_age->days < 30) {
                            echo $account_age->days . " days ago";
                        } elseif ($account_age->m < 12) {
                            echo $account_age->m . " month" . ($account_age->m > 1 ? "s" : "") . " ago";
                        } else {
                            echo $account_age->y . " year" . ($account_age->y > 1 ? "s" : "") . " ago";
                        }
                        ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <i class="fas fa-box card-icon"></i>
                    <div class="card-title">Total Orders</div>
                    <div class="card-value"><?php echo $total_orders; ?></div>
                    <div class="card-label"><?php echo $total_orders > 0 ? 'Thank you for your orders!' : 'Start your wellness journey'; ?></div>
                </div>

                <div class="dashboard-card">
                    <i class="fas fa-heart card-icon"></i>
                    <div class="card-title">Wishlist Items</div>
                    <div class="card-value">0</div>
                    <div class="card-label">Save your favorites</div>
                </div>

                <!-- Account Information -->
                <div class="dashboard-card" style="grid-column: 1 / -1;">
                    <h3 style="color: #1A3C34; margin-bottom: 1.5rem; font-size: 1.5rem;">
                        <i class="fas fa-user-circle"></i> Account Information
                    </h3>
                    
                    <div class="info-item">
                        <i class="fas fa-id-card"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">User ID</div>
                            <div class="info-item-value">#<?php echo str_pad($user_id, 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Full Name</div>
                            <div class="info-item-value"><?php echo htmlspecialchars($user['name']); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Email Address</div>
                            <div class="info-item-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Phone Number</div>
                            <div class="info-item-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Account Created</div>
                            <div class="info-item-value"><?php echo $created_date->format('F d, Y \a\t g:i A'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- My Orders Section -->
                <div class="dashboard-card" style="grid-column: 1 / -1;">
                    <h3 style="color: #1A3C34; margin-bottom: 1.5rem; font-size: 1.5rem;">
                        <i class="fas fa-shopping-cart"></i> My Orders
                    </h3>
                    
                    <?php if ($order_success): ?>
                        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                            <i class="fas fa-check-circle"></i> Order submitted successfully! Your payment details have been received.
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($orders) > 0): ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Order ID</th>
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Amount</th>
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Bank</th>
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Status</th>
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Date</th>
                                        <th style="padding: 1rem; text-align: left; color: #1A3C34; font-weight: 600;">Bank Slip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr style="border-bottom: 1px solid #e0e0e0;">
                                            <td style="padding: 1rem; color: #333;">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td style="padding: 1rem; color: #1A3C34; font-weight: 600;">$<?php echo number_format($order['amount'], 2); ?></td>
                                            <td style="padding: 1rem; color: #333;"><?php echo htmlspecialchars($order['bank']); ?></td>
                                            <td style="padding: 1rem;">
                                                <?php
                                                $status_colors = [
                                                    'pending' => 'background: #ffc107; color: #000;',
                                                    'confirmed' => 'background: #17a2b8; color: white;',
                                                    'completed' => 'background: #28a745; color: white;',
                                                    'cancelled' => 'background: #dc3545; color: white;'
                                                ];
                                                $status_style = $status_colors[$order['order_status']] ?? 'background: #6c757d; color: white;';
                                                ?>
                                                <span style="<?php echo $status_style; ?> padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; text-transform: capitalize;">
                                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 1rem; color: #333;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td style="padding: 1rem;">
                                                <?php if ($order['bank_slip_path']): ?>
                                                    <a href="<?php echo htmlspecialchars($order['bank_slip_path']); ?>" target="_blank" style="color: #D4AF37; text-decoration: none;">
                                                        <i class="fas fa-file-image"></i> View
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #999;">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; color: #666;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; color: #999;"></i>
                            <p style="font-size: 1.1rem; margin-bottom: 1rem;">No orders yet</p>
                            <a href="checkout.php" style="display: inline-block; padding: 0.8rem 1.5rem; background: #D4AF37; color: #1A3C34; border-radius: 25px; text-decoration: none; font-weight: 600;">
                                <i class="fas fa-plus"></i> Place Your First Order
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>
            
            <div id="alertMessage" class="alert"></div>
            
            <form id="editProfileForm">
                <div class="form-group">
                    <label for="edit_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="edit_name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" id="edit_phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required pattern="[0-9]{10}" maxlength="10">
                    <small>Enter 10-digit phone number</small>
                </div>
                
                <div class="password-divider">
                    <p><i class="fas fa-lock"></i> Change Password (Optional)</p>
                </div>
                
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-key"></i> Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <small>Required to save any changes</small>
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" id="new_password" name="new_password" minlength="6">
                    <small>Leave blank to keep current password</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="6">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="modal-btn btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="modal-btn btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="copyright">
                <p>&copy; 2026 VedaLife Ayurveda. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Modal functions
        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('editProfileForm').reset();
            hideAlert();
            // Restore original values
            document.getElementById('edit_name').value = "<?php echo htmlspecialchars($user['name']); ?>";
            document.getElementById('edit_email').value = "<?php echo htmlspecialchars($user['email']); ?>";
            document.getElementById('edit_phone').value = "<?php echo htmlspecialchars($user['phone']); ?>";
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Alert functions
        function showAlert(message, type) {
            const alert = document.getElementById('alertMessage');
            alert.textContent = message;
            alert.className = 'alert show alert-' + type;
        }

        function hideAlert() {
            const alert = document.getElementById('alertMessage');
            alert.className = 'alert';
        }

        // Form submission
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate passwords match if new password is provided
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                showAlert('New passwords do not match', 'error');
                return;
            }
            
            // Validate phone number
            const phone = document.getElementById('edit_phone').value;
            if (!/^[0-9]{10}$/.test(phone)) {
                showAlert('Phone number must be exactly 10 digits', 'error');
                return;
            }
            
            // Submit form via AJAX
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.btn-save');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Update displayed information
                    if (data.data) {
                        // Update all name displays
                        document.querySelectorAll('.profile-card h2').forEach(el => {
                            el.textContent = data.data.name;
                        });
                        
                        // Update email and phone displays
                        const emailElements = document.querySelectorAll('.info-item-value');
                        emailElements.forEach(el => {
                            if (el.textContent.includes('@')) {
                                el.textContent = data.data.email;
                            }
                        });
                        
                        // Update profile card email and phone
                        const profileInfo = document.querySelectorAll('.profile-card p');
                        profileInfo[0].innerHTML = '<i class="fas fa-envelope"></i> ' + data.data.email;
                        profileInfo[1].innerHTML = '<i class="fas fa-phone"></i> ' + data.data.phone;
                        
                        // Update info items
                        const infoItems = document.querySelectorAll('.info-item');
                        infoItems.forEach(item => {
                            const label = item.querySelector('.info-item-label');
                            const value = item.querySelector('.info-item-value');
                            
                            if (label && value) {
                                if (label.textContent === 'Full Name') {
                                    value.textContent = data.data.name;
                                } else if (label.textContent === 'Email Address') {
                                    value.textContent = data.data.email;
                                } else if (label.textContent === 'Phone Number') {
                                    value.textContent = data.data.phone;
                                }
                            }
                        });
                    }
                    
                    // Close modal after 2 seconds
                    setTimeout(() => {
                        closeEditModal();
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            });
        });
    </script>
</body>
</html>
