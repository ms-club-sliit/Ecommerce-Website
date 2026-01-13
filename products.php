<?php 
session_start(); 
require_once 'config/database.php';

// Fetch products from database
$conn = getDBConnection();
$productsQuery = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = [];
while ($row = $productsQuery->fetch_assoc()) {
    $products[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products | VedaLife - Premium Ayurvedic Wellness</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js?v=1.1" defer></script>
    <script src="assets/js/product-filter.js" defer></script>
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
                <button class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container" id="products" style="padding-top: 6rem; margin-bottom: 4rem;">
        <h2 class="section-title">Our Collection</h2>
        
        <!-- Filter Controls -->
        <div class="filter-controls">
            <div class="filter-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search products...">
                </div>
                <div class="filter-actions">
                    <select id="sortSelect" class="filter-select">
                        <option value="default">Sort By</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="name-asc">Name: A-Z</option>
                        <option value="name-desc">Name: Z-A</option>
                    </select>
                    <button id="clearFilters" class="clear-btn">Clear All</button>
                </div>
            </div>
            
            <div class="filter-row">
                <div class="category-filters">
                    <button class="category-btn active" data-category="all">All</button>
                    <button class="category-btn" data-category="Supplements">Supplements</button>
                    <button class="category-btn" data-category="Skincare">Skincare</button>
                    <button class="category-btn" data-category="Wellness">Wellness</button>
                    <button class="category-btn" data-category="Hair Care">Hair Care</button>
                </div>
                <select id="priceFilter" class="filter-select">
                    <option value="all">All Prices</option>
                    <option value="0-6000">Under Rs. 6,000</option>
                    <option value="6000-9000">Rs. 6,000 - 9,000</option>
                    <option value="9000-15000">Rs. 9,000 - 15,000</option>
                    <option value="15000-999999">Over Rs. 15,000</option>
                </select>
            </div>
            
            <div class="results-count">
                Showing <span id="resultsCount"><?php echo count($products); ?></span> of <span id="totalCount"><?php echo count($products); ?></span> products
            </div>
        </div>
        
        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="assets/images/placeholder.jpg" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description" style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?><?php echo strlen($product['description']) > 100 ? '...' : ''; ?>
                            </p>
                            <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            <button class="add-to-cart" 
                                    data-id="<?php echo $product['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-price="<?php echo $product['price']; ?>"
                                    data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                Add to Cart
                            </button>
                            <button class="view-product" onclick="window.location.href='viewproduct.php?id=<?php echo $product['id']; ?>'">View Product</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>No Products Available</h3>
                    <p style="color: #666;">Check back soon for new products!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <h3>No products found</h3>
            <p>Try adjusting your filters or search terms</p>
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
