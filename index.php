<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VedaLife | Premium Ayurvedic Wellness</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js?v=1.3" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <!-- Cart items will be populated by JS -->
                <p style="text-align: center; color: #888; margin-top: 2rem;">Your cart is empty.</p>
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total</span>
                    <span id="cartTotal">Rs. 0.00</span>
                </div>
                <button href="checkout.php" class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <section class="hero">
        <div class="hero-content">
            <h1>Harmony with Nature</h1>
            <p>Discover the ancient healing power of Ayurveda with our premium, handcrafted herbal remedies.</p>
            <a href="products.php" class="btn btn-primary">Explore Collection</a>
        </div>
    </section>

    <!-- About Section -->
    <div class="container" style="margin-bottom: 5rem;">
        <div style="text-align: center; max-width: 900px; margin: 0 auto;">
            <h2 class="section-title">Welcome to VedaLife</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin-bottom: 2rem;">
                At VedaLife, we bridge ancient Ayurvedic wisdom with modern wellness needs. Our carefully curated collection features 100% organic, handcrafted herbal products sourced from the pristine farms of Kerala. Each product is meticulously prepared using traditional methods passed down through generations, ensuring maximum potency and authenticity.
            </p>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text);">
                From immunity-boosting supplements to natural skincare solutions, our diverse range addresses various wellness needs. We believe in the power of nature to heal, restore, and rejuvenate. Every product undergoes rigorous quality checks and is free from harmful chemicals, making them safe for the whole family. Join thousands of satisfied customers who have embraced the Ayurvedic lifestyle with VedaLife.
            </p>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="container" style="margin-bottom: 5rem;">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <?php
            require_once 'config/database.php';
            $conn = getDBConnection();
            
            // Fetch 3 random featured products
            $sql = "SELECT * FROM products ORDER BY RAND() LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                            <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            <button class="add-to-cart" 
                                    data-id="<?php echo $product['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-price="<?php echo $product['price']; ?>"
                                    data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                Add to Cart
                            </button>
                            <a href="viewproduct.php?id=<?php echo $product['id']; ?>" class="view-product" style="display: inline-block; text-align: center;">View Product</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="text-align: center; grid-column: 1/-1; color: #888;">No products available at the moment.</p>';
            }
            $conn->close();
            ?>
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="products.php" class="btn btn-primary">View All Products</a>
        </div>
    </div>

    <!-- Customer Testimonials Section -->
    <div class="container" style="margin-bottom: 5rem;">
        <h2 class="section-title">What Our Customers Say</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); border-left: 4px solid var(--color-secondary);">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; color: var(--color-secondary);">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p style="font-style: italic; color: var(--color-text); margin-bottom: 1.5rem; line-height: 1.7;">
                    "I've been using VedaLife's Ashwagandha capsules for three months now, and the difference in my stress levels is remarkable. The quality is outstanding, and I love that everything is organic and natural!"
                </p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">P</div>
                    <div>
                        <h4 style="color: var(--color-primary); margin-bottom: 0.2rem;">Priya Sharma</h4>
                        <p style="font-size: 0.85rem; color: #888;">Verified Customer</p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); border-left: 4px solid var(--color-secondary);">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; color: var(--color-secondary);">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p style="font-style: italic; color: var(--color-text); margin-bottom: 1.5rem; line-height: 1.7;">
                    "The Triphala powder has transformed my digestive health. Fast shipping, excellent packaging, and authentic Ayurvedic products. VedaLife is now my go-to wellness store!"
                </p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">R</div>
                    <div>
                        <h4 style="color: var(--color-primary); margin-bottom: 0.2rem;">Rajesh Kumar</h4>
                        <p style="font-size: 0.85rem; color: #888;">Verified Customer</p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); border-left: 4px solid var(--color-secondary);">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; color: var(--color-secondary);">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p style="font-style: italic; color: var(--color-text); margin-bottom: 1.5rem; line-height: 1.7;">
                    "Amazing products! The Brahmi oil has done wonders for my hair, and my family loves the Chyawanprash. Great customer service and genuine Ayurvedic formulations. Highly recommended!"
                </p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">A</div>
                    <div>
                        <h4 style="color: var(--color-primary); margin-bottom: 0.2rem;">Anjali Desai</h4>
                        <p style="font-size: 0.85rem; color: #888;">Verified Customer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container" style="margin-bottom: 5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: center;">
            <div>
                <i class="fas fa-leaf" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 1rem;"></i>
                <h3>100% Organic</h3>
                <p>Sourced directly from certified organic farms in Kerala.</p>
            </div>
            <div>
                <i class="fas fa-hand-holding-heart" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 1rem;"></i>
                <h3>Handcrafted</h3>
                <p>Made in small batches to ensure maximum potency and quality.</p>
            </div>
            <div>
                <i class="fas fa-shipping-fast" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 1rem;"></i>
                <h3>Fast Shipping</h3>
                <p>Delivery within 3-5 business days across the globe.</p>
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
