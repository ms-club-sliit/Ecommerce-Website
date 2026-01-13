<?php
session_start();
require_once 'config/database.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Fetch product from database
$conn = getDBConnection();

if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// If no product found or no ID provided, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit;
}

$conn->close();

// Set default values for optional fields if they don't exist in database
$product['long_description'] = $product['description'] ?? 'Discover the benefits of this premium Ayurvedic product.';
$product['benefits'] = !empty($product['benefits']) ? explode('|', $product['benefits']) : [
    'Premium quality ingredients',
    'Traditional Ayurvedic formulation',
    'Supports overall wellness',
    'Natural and organic'
];
$product['ingredients'] = $product['ingredients'] ?? 'Natural Ayurvedic herbs and ingredients';
$product['usage'] = $product['usage'] ?? 'Use as directed by your healthcare practitioner.';
$product['size'] = $product['size'] ?? 'Standard size';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | VedaLife</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js?v=1.2" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 8rem 2rem 4rem;
        }

        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb a {
            color: var(--color-primary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--color-accent);
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .product-image-section {
            position: sticky;
            top: 6rem;
            height: fit-content;
        }

        .product-main-image {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem;
        }

        .product-main-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .product-main-image:hover img {
            transform: scale(1.05);
        }

        .product-info-section {
            animation: fadeInUp 0.6s ease;
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

        .product-category-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-detail-title {
            font-size: 2.5rem;
            color: var(--color-text);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .product-detail-price {
            font-size: 2rem;
            color: var(--color-primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .product-short-desc {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .product-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .btn-add-cart {
            flex: 1;
            padding: 1.2rem 2rem;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-back {
            padding: 1.2rem 2rem;
            background: white;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-back:hover {
            background: var(--color-primary);
            color: white;
        }

        .product-details-tabs {
            border-top: 2px solid #eee;
            padding-top: 2rem;
        }

        .detail-section {
            margin-bottom: 2.5rem;
        }

        .detail-section h3 {
            font-size: 1.5rem;
            color: var(--color-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-section h3 i {
            color: var(--color-primary);
        }

        .detail-section p {
            line-height: 1.8;
            color: #555;
            margin-bottom: 1rem;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
        }

        .benefits-list li {
            padding: 0.8rem 0;
            padding-left: 2rem;
            position: relative;
            color: #555;
            line-height: 1.6;
        }

        .benefits-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--color-primary);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .info-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid var(--color-primary);
            margin-bottom: 1.5rem;
        }

        .info-box h4 {
            color: var(--color-primary);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .info-box p {
            margin: 0;
            color: #555;
        }

        @media (max-width: 768px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .product-image-section {
                position: relative;
                top: 0;
            }

            .product-detail-title {
                font-size: 2rem;
            }

            .product-actions {
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
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <div class="auth-links">
                            <span style="color: var(--color-primary); font-weight: 600;">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </span>
                            <a href="logout.php" class="auth-btn" style="background: var(--color-accent); color: white; border-color: var(--color-accent);">Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="login.php" class="auth-btn">Login</a>
                            <a href="signup.php" class="auth-btn">Sign Up</a>
                        </div>
                    <?php endif; ?>
                    <div class="cart-icon" id="cartBtn">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count">0</span>
                    </div>
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
                <p style="text-align: center; color: #888; margin-top: 2rem;">Your cart is empty.</p>
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total</span>
                    <span id="cartTotal">Rs. 0.00</span>
                </div>
                <button class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Product Detail Section -->
    <div class="product-detail-container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="products.php">Shop</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="product-detail-grid">
            <div class="product-image-section">
                <div class="product-main-image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>

            <div class="product-info-section">
                <span class="product-category-badge"><?php echo htmlspecialchars($product['category']); ?></span>
                <h1 class="product-detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-detail-price">Rs. <?php echo number_format($product['price'], 2); ?></div>
                <p class="product-short-desc"><?php echo htmlspecialchars($product['description']); ?></p>

                <div class="product-actions">
                    <button class="btn-add-cart add-to-cart" 
                            data-id="<?php echo $product['id']; ?>"
                            data-title="<?php echo htmlspecialchars($product['name']); ?>"
                            data-price="<?php echo $product['price']; ?>"
                            data-image="<?php echo htmlspecialchars($product['image']); ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <a href="products.php" class="btn-back"> 
                        <i class="fas fa-arrow-left"></i> Back to Shop
                    </a>
                </div>

                <div class="product-details-tabs">
                    <div class="detail-section">
                        <h3><i class="fas fa-info-circle"></i> About This Product</h3>
                        <p><?php echo htmlspecialchars($product['long_description']); ?></p>
                    </div>

                    <div class="detail-section">
                        <h3><i class="fas fa-star"></i> Key Benefits</h3>
                        <ul class="benefits-list">
                            <?php foreach ($product['benefits'] as $benefit): ?>
                                <li><?php echo htmlspecialchars($benefit); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="detail-section">
                        <div class="info-box">
                            <h4><i class="fas fa-leaf"></i> Ingredients</h4>
                            <p><?php echo htmlspecialchars($product['ingredients']); ?></p>
                        </div>

                        <div class="info-box">
                            <h4><i class="fas fa-prescription-bottle"></i> How to Use</h4>
                            <p><?php echo htmlspecialchars($product['usage']); ?></p>
                        </div>

                        <div class="info-box">
                            <h4><i class="fas fa-box"></i> Size</h4>
                            <p><?php echo htmlspecialchars($product['size']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
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
</body>
</html>
