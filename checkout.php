<?php 
session_start();

// Prevent caching of checkout page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://www.payhere.lk https://sandbox.payhere.lk https://www.google-analytics.com https://cdnjs.cloudflare.com https://maxcdn.bootstrapcdn.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://maxcdn.bootstrapcdn.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; connect-src 'self' https://www.payhere.lk https://sandbox.payhere.lk; frame-src 'self' https://www.payhere.lk https://sandbox.payhere.lk;">
    <title>Checkout | VedaLife - Payment Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout.css">
    <script src="assets/js/script.js?v=1.1" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- PayHere JavaScript SDK -->
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
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
            <h1 class="checkout-title">Checkout</h1>
            
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
                
                <!-- Payment Method Selection -->
                <div class="form-group">
                    <label>Select Payment Method</label>
                    <div class="payment-methods">
                        <label class="payment-method-card">
                            <input type="radio" name="paymentMethod" value="card" checked>
                            <div class="payment-method-content">
                                <div class="payment-icons">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                </div>
                                <span>Credit/Debit Card</span>
                                <p class="payment-desc">Pay securely with Visa or Mastercard via PayHere</p>
                            </div>
                        </label>
                        
                        <label class="payment-method-card">
                            <input type="radio" name="paymentMethod" value="cod">
                            <div class="payment-method-content">
                                <div class="payment-icons">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <span>Cash on Delivery</span>
                                <p class="payment-desc">Pay when you receive your order</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- User Name -->
                <div class="form-group">
                    <label for="userName">Full Name</label>
                    <input type="text" id="userName" name="userName" placeholder="Enter your full name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+94 771234567" required>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" placeholder="Enter your delivery address" rows="3" required></textarea>
                </div>

                <!-- City -->
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Enter your city" required>
                </div>

                <!-- Country -->
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="Sri Lanka" required>
                </div>

                <!-- Bank Details - Only for Bank Transfer (Legacy support) -->
                <div id="bankDetailsSection" style="display: none;">
                    <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: #2c5530;">Bank Transfer Details</h3>
                    
                    <!-- Bank Account Number -->
                    <div class="form-group">
                        <label for="accountNumber">Bank Account Number</label>
                        <input type="text" id="accountNumber" name="accountNumber" placeholder="0">
                    </div>

                    <!-- Bank Selection -->
                    <div class="form-group">
                        <label for="bank">Bank</label>
                        <select id="bank" name="bank">
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
                        <input type="text" id="branch" name="branch" placeholder="Enter branch name">
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
                </div>
                
                <!-- Amount -->
                <div class="form-group">
                    <label for="amount">Order Amount (LKR)</label>
                    <input type="text" id="amount" name="amount" placeholder="0.00" required>
                </div>

                <!-- Confirmation Status - Only for COD -->
                <div class="form-group" id="confirmationSection" style="display: none;">
                    <label>Confirm Cash on Delivery</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="confirmationStatus" value="yes">
                            <span>I confirm I will pay cash on delivery</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span id="btnText">Proceed to Payment</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
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
    
    <script>
    // Payment method toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodInputs = document.querySelectorAll('input[name="paymentMethod"]');
        const bankDetailsSection = document.getElementById('bankDetailsSection');
        const confirmationSection = document.getElementById('confirmationSection');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const checkoutForm = document.getElementById('checkoutForm');
        
        // Bank details fields
        const accountNumber = document.getElementById('accountNumber');
        const bank = document.getElementById('bank');
        const branch = document.getElementById('branch');
        const bankSlip = document.getElementById('bankSlip');
        
        function updateFormBasedOnPayment() {
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            
            // Reset required attributes
            accountNumber.removeAttribute('required');
            bank.removeAttribute('required');
            branch.removeAttribute('required');
            
            if (selectedMethod === 'card') {
                bankDetailsSection.style.display = 'none';
                confirmationSection.style.display = 'none';
                btnText.textContent = 'Pay with Card';
            } else if (selectedMethod === 'cod') {
                bankDetailsSection.style.display = 'none';
                confirmationSection.style.display = 'block';
                btnText.textContent = 'Place Order';
            }
        }
        
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', updateFormBasedOnPayment);
        });
        
        // Initialize on page load
        updateFormBasedOnPayment();
        
        // PayHere Integration
        payhere.onCompleted = function onCompleted(orderId) {
            console.log("Payment completed. OrderID:" + orderId);
            // Redirect to success page
            window.location.href = 'checkout.php?success=1&order_id=' + orderId;
        };

        payhere.onDismissed = function onDismissed() {
            console.log("Payment dismissed");
            alert('Payment was cancelled. Please try again.');
        };

        payhere.onError = function onError(error) {
            console.log("Error:" + error);
            alert('Payment error occurred: ' + error);
        };
        
        // Handle form submission
        checkoutForm.addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            
            if (selectedMethod === 'card') {
                e.preventDefault();
                
                // Validate form data
                const userName = document.getElementById('userName').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const address = document.getElementById('address').value.trim();
                const city = document.getElementById('city').value.trim();
                const country = document.getElementById('country').value.trim();
                const amount = document.getElementById('amount').value.trim();
                
                if (!userName || !email || !phone || !address || !city || !country || !amount) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // Create FormData and send to server to create order
                const formData = new FormData(checkoutForm);
                formData.append('create_payhere_order', '1');
                
                // Show loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Processing...';
                
                fetch('process_checkout.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Configure PayHere payment
                        var payment = {
                            "sandbox": true, // Set to false for production
                            "merchant_id": "1233568", // Replace with your Merchant ID
                            "return_url": undefined,
                            "cancel_url": undefined,
                            "notify_url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/payhere_notify.php'; ?>",
                            "order_id": data.payhere_order_id,
                            "items": "VedaLife Order #" + data.order_id,
                            "amount": parseFloat(amount).toFixed(2),
                            "currency": "LKR",
                            "hash": data.hash,
                            "first_name": userName.split(' ')[0],
                            "last_name": userName.split(' ').slice(1).join(' ') || userName.split(' ')[0],
                            "email": email,
                            "phone": phone,
                            "address": address,
                            "city": city,
                            "country": country
                        };
                        
                        // Show PayHere payment popup
                        payhere.startPayment(payment);
                        
                        // Re-enable button
                        submitBtn.disabled = false;
                        btnText.textContent = 'Pay with Card';
                    } else {
                        alert('Error: ' + data.message);
                        submitBtn.disabled = false;
                        btnText.textContent = 'Pay with Card';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    btnText.textContent = 'Pay with Card';
                });
                
            } else if (selectedMethod === 'paypal') {
                e.preventDefault();
                alert('PayPal integration coming soon! Please select another payment method.');
                
            } else if (selectedMethod === 'cod') {
                // Allow normal form submission for COD
                const confirmation = document.querySelector('input[name="confirmationStatus"]:checked');
                if (!confirmation) {
                    e.preventDefault();
                    alert('Please confirm that you will pay cash on delivery');
                }
            }
        });
    });
    </script>
</body>
</html>
