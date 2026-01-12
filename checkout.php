<?php 
session_start();

// Restrict access to registered users only
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Store the intended destination for redirect after login
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php?error=login_required');
    exit;
}

// Get user info if logged in
$user_name = '';
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? '';
}

// Get success/error messages
$success_message = $_SESSION['checkout_success'] ?? '';
$error_message = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_success'], $_SESSION['checkout_error']);

// Check for URL success parameter
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Order submitted successfully! Thank you for your payment.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | VedaLife - Payment Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout.css">
    <script src="assets/js/script.js?v=1.1" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 1rem;
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
                <div class="cart-icon" id="cartBtn">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count">0</span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Cart Modal -->
    <div class="cart-modal-overlay" id="cartOverlay">
        <div class="cart-modal" id="cartModal">
            <div class="cart-header">
                <h2>Your Bag</h2>
                <button class="close-cart" id="closeCart">&times;</button>
            </div>
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be populated by JS -->
                <p style="text-align: center; color: #888; margin-top: 2rem;">Your cart is empty.</p>
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total</span>
                    <span id="cartTotal">$0.00</span>
                </div>
                <button class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Checkout Form Section -->
    <div class="checkout-container">
        <div class="checkout-wrapper">
            <h1 class="checkout-title">Upload Payment Details</h1>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form id="checkoutForm" class="checkout-form" action="process_checkout.php" method="POST" enctype="multipart/form-data">
                
                <!-- User Name -->
                <div class="form-group">
                    <label for="userName">User Name</label>
                    <input type="text" id="userName" name="userName" placeholder="Enter your full name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                </div>

                <!-- Bank Account Number -->
                <div class="form-group">
                    <label for="accountNumber">Bank Account Number</label>
                    <input type="text" id="accountNumber" name="accountNumber" placeholder="0" required>
                </div>

                <!-- Bank Selection -->
                <div class="form-group">
                    <label for="bank">Bank</label>
                    <select id="bank" name="bank" required>
                        <option value="">Select a Bank</option>
                        <option value="bank1">Bank of America</option>
                        <option value="bank2">Chase Bank</option>
                        <option value="bank3">Wells Fargo</option>
                        <option value="bank4">Citibank</option>
                        <option value="bank5">HSBC</option>
                        <option value="bank6">Other</option>
                    </select>
                </div>

                <!-- Branch -->
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <input type="text" id="branch" name="branch" placeholder="Enter branch name" required>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" id="amount" name="amount" placeholder="0" required>
                </div>

                <!-- Confirmation Status -->
                <div class="form-group">
                    <label>Confirmation Status</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="confirmationStatus" value="yes" required>
                            <span>Yes</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="confirmationStatus" value="no" required>
                            <span>No</span>
                        </label>
                    </div>
                </div>

                <!-- Upload Bank Slip -->
                <div class="form-group">
                    <label for="bankSlip">Upload Bank Slip</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="bankSlip" name="bankSlip" accept="image/png, image/jpeg, image/jpg" hidden>
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                                <i class="fas fa-plus"></i>
                            </div>
                            <p class="upload-text">
                                <span class="upload-link">Upload a file</span> or drag and drop
                            </p>
                            <p class="upload-info">PNG, JPG up to 5MB</p>
                        </div>
                        <div class="file-preview" id="filePreview" style="display: none;">
                            <img id="previewImage" src="" alt="Preview">
                            <button type="button" class="remove-file" id="removeFile">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Submit Payment</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>VedaLife</h3>
                    <p>Bringing ancient wisdom to modern wellness. Our products are 100% organic, ethically sourced, and crafted with care.</p>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="products.php">Shop All</a></li>
                        <li><a href="about.php">Our Story</a></li>
                        <li><a href="#">Ayurvedic Dosha Quiz</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <p>Email: hello@vedalife.com</p>
                    <p>Phone: +1 (555) 123-4567</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2026 VedaLife Ayurveda. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/checkout.js"></script>
</body>
</html>
