<?php
session_start();

// Product data array
$products = [
    1 => [
        'id' => 1,
        'name' => 'Ashwagandha Vitality Elixir',
        'category' => 'Supplements',
        'price' => 29.99,
        'image' => 'assets/images/product1.png',
        'description' => 'Premium organic Ashwagandha root extract for stress relief and energy.',
        'long_description' => 'Our Ashwagandha Vitality Elixir is crafted from the finest organic Ashwagandha roots, carefully harvested and processed to preserve their natural potency. This ancient adaptogenic herb has been used for centuries in Ayurvedic medicine to combat stress, boost energy levels, and promote overall wellness. Each serving delivers a concentrated dose of withanolides, the active compounds responsible for Ashwagandha\'s remarkable benefits.',
        'benefits' => [
            'Reduces stress and anxiety naturally',
            'Boosts energy and stamina',
            'Supports healthy cortisol levels',
            'Enhances mental clarity and focus',
            'Promotes restful sleep'
        ],
        'ingredients' => 'Organic Ashwagandha Root Extract (Withania somnifera), Vegetable Glycerin, Purified Water',
        'usage' => 'Take 1-2 droppers daily, preferably in the morning or as directed by your healthcare practitioner.',
        'size' => '2 fl oz (60ml)'
    ],
    2 => [
        'id' => 2,
        'name' => 'Turmeric Golden Glow Oil',
        'category' => 'Skincare',
        'price' => 45.50,
        'image' => 'assets/images/product2.jpg',
        'description' => 'Radiance-boosting face oil infused with pure turmeric and saffron.',
        'long_description' => 'Experience the golden touch of ancient beauty secrets with our Turmeric Golden Glow Oil. This luxurious face oil combines the anti-inflammatory properties of turmeric with the brightening effects of saffron to reveal your skin\'s natural radiance. Rich in antioxidants and essential fatty acids, this elixir nourishes deeply while promoting an even, glowing complexion.',
        'benefits' => [
            'Brightens and evens skin tone',
            'Reduces inflammation and redness',
            'Fights signs of aging',
            'Deeply moisturizes without greasiness',
            'Enhances natural radiance'
        ],
        'ingredients' => 'Organic Jojoba Oil, Turmeric Extract, Saffron, Vitamin E, Sandalwood Essential Oil',
        'usage' => 'Apply 3-4 drops to clean, damp skin. Gently massage in upward circular motions. Use morning and night.',
        'size' => '1 fl oz (30ml)'
    ],
    3 => [
        'id' => 3,
        'name' => 'Triphala Digestive Balance',
        'category' => 'Supplements',
        'price' => 22.00,
        'image' => 'assets/images/product3.webp',
        'description' => 'Ancient herbal blend for healthy digestion and detoxification.',
        'long_description' => 'Triphala, meaning "three fruits," is one of the most revered formulas in Ayurvedic medicine. Our Triphala Digestive Balance combines Amalaki, Bibhitaki, and Haritaki in perfect proportion to support digestive health, gentle detoxification, and overall vitality. This time-tested formula works harmoniously with your body to promote regularity and optimal nutrient absorption.',
        'benefits' => [
            'Supports healthy digestion',
            'Promotes gentle detoxification',
            'Enhances nutrient absorption',
            'Maintains regularity',
            'Rich in antioxidants'
        ],
        'ingredients' => 'Organic Amalaki (Emblica officinalis), Organic Bibhitaki (Terminalia bellirica), Organic Haritaki (Terminalia chebula)',
        'usage' => 'Take 2 capsules before bed with warm water or as directed by your healthcare practitioner.',
        'size' => '90 capsules'
    ],
    4 => [
        'id' => 4,
        'name' => 'Brahmi Focus Tincture',
        'category' => 'Wellness',
        'price' => 34.00,
        'image' => 'assets/images/product4.png',
        'description' => 'Sharpen your mind and memory with our potent Brahmi extract.',
        'long_description' => 'Brahmi, also known as Bacopa monnieri, has been treasured in Ayurveda for its remarkable cognitive-enhancing properties. Our Brahmi Focus Tincture is a concentrated liquid extract designed to support mental clarity, memory, and focus. Perfect for students, professionals, and anyone seeking to optimize their cognitive performance naturally.',
        'benefits' => [
            'Enhances memory and learning',
            'Improves focus and concentration',
            'Supports cognitive function',
            'Reduces mental fatigue',
            'Promotes calm alertness'
        ],
        'ingredients' => 'Organic Brahmi Leaf Extract (Bacopa monnieri), Organic Alcohol, Purified Water',
        'usage' => 'Take 1-2 droppers twice daily, preferably with meals.',
        'size' => '2 fl oz (60ml)'
    ],
    5 => [
        'id' => 5,
        'name' => 'Kumkumadi Night Serum',
        'category' => 'Skincare',
        'price' => 89.99,
        'image' => 'assets/images/product5.jpg',
        'description' => 'Luxury night revival serum with 24k gold bhasma and herbs.',
        'long_description' => 'Indulge in the ultimate luxury with our Kumkumadi Night Serum, a precious blend inspired by ancient Ayurvedic beauty rituals. This exquisite serum features saffron, 24k gold bhasma, and 16 rare herbs that work synergistically to rejuvenate your skin while you sleep. Wake up to visibly brighter, smoother, and more youthful-looking skin.',
        'benefits' => [
            'Reduces fine lines and wrinkles',
            'Brightens dark spots and pigmentation',
            'Improves skin texture and tone',
            'Deeply nourishes and repairs',
            'Enhances skin luminosity'
        ],
        'ingredients' => 'Saffron, 24k Gold Bhasma, Sandalwood, Lotus, Vetiver, Manjistha, Sesame Oil, Almond Oil',
        'usage' => 'Apply 4-5 drops to cleansed face and neck before bed. Gently massage until absorbed.',
        'size' => '1 fl oz (30ml)'
    ],
    6 => [
        'id' => 6,
        'name' => 'Chyawanprash Immunity Jam',
        'category' => 'Wellness',
        'price' => 18.50,
        'image' => 'assets/images/product6.jpg',
        'description' => 'Traditional amla-based jam to supercharge your immune system.',
        'long_description' => 'Chyawanprash is a legendary Ayurvedic formulation dating back thousands of years. Our authentic recipe combines amla (Indian gooseberry) with over 40 herbs, spices, and honey to create a delicious jam that supports immunity, vitality, and longevity. Rich in vitamin C and antioxidants, this time-honored tonic is perfect for the whole family.',
        'benefits' => [
            'Boosts immune system',
            'Rich in vitamin C and antioxidants',
            'Increases energy and stamina',
            'Supports respiratory health',
            'Promotes overall vitality'
        ],
        'ingredients' => 'Organic Amla, Honey, Ghee, Ashwagandha, Shatavari, Cardamom, Cinnamon, and 35+ Traditional Herbs',
        'usage' => 'Take 1-2 teaspoons daily, preferably in the morning with warm milk or water.',
        'size' => '16 oz (454g)'
    ],
    7 => [
        'id' => 7,
        'name' => 'Neem Purifying Face Wash',
        'category' => 'Skincare',
        'price' => 16.99,
        'image' => 'assets/images/product7.jpg',
        'description' => 'Deep cleansing face wash with neem and tulsi for clear, healthy skin.',
        'long_description' => 'Purify and refresh your skin with our Neem Purifying Face Wash. This gentle yet effective cleanser harnesses the antibacterial properties of neem and the purifying power of tulsi to deeply cleanse pores, remove impurities, and balance oil production. Perfect for all skin types, especially acne-prone and oily skin.',
        'benefits' => [
            'Deep cleanses pores',
            'Controls excess oil',
            'Prevents acne and breakouts',
            'Soothes inflammation',
            'Leaves skin fresh and balanced'
        ],
        'ingredients' => 'Neem Extract, Tulsi (Holy Basil), Aloe Vera, Turmeric, Coconut-based Cleansers',
        'usage' => 'Apply to damp face, massage gently in circular motions, and rinse thoroughly. Use morning and night.',
        'size' => '5 fl oz (150ml)'
    ],
    8 => [
        'id' => 8,
        'name' => 'Moringa Energy Powder',
        'category' => 'Supplements',
        'price' => 27.50,
        'image' => 'assets/images/product8.jpg',
        'description' => 'Nutrient-rich superfood powder for natural energy and vitality.',
        'long_description' => 'Discover the power of the "miracle tree" with our pure Moringa Energy Powder. Packed with vitamins, minerals, and amino acids, moringa is one of nature\'s most complete superfoods. Our powder is made from carefully dried and ground moringa leaves, preserving their exceptional nutritional profile to fuel your body naturally.',
        'benefits' => [
            'Boosts natural energy levels',
            'Rich in vitamins A, C, and E',
            'Contains all 9 essential amino acids',
            'Supports healthy inflammation response',
            'Promotes overall wellness'
        ],
        'ingredients' => '100% Organic Moringa Leaf Powder (Moringa oleifera)',
        'usage' => 'Mix 1-2 teaspoons into smoothies, juice, or water daily.',
        'size' => '8 oz (227g)'
    ],
    9 => [
        'id' => 9,
        'name' => 'Bhringraj Hair Oil',
        'category' => 'Hair Care',
        'price' => 24.99,
        'image' => 'assets/images/product9.jpg',
        'description' => 'Traditional hair growth oil with bhringraj and amla for lustrous hair.',
        'long_description' => 'Revitalize your hair with our traditional Bhringraj Hair Oil, known as the "king of herbs" for hair. This potent oil blend combines bhringraj with amla, brahmi, and nourishing carrier oils to promote hair growth, prevent premature graying, and restore natural shine. Regular use strengthens hair from root to tip.',
        'benefits' => [
            'Promotes hair growth',
            'Prevents premature graying',
            'Reduces hair fall',
            'Nourishes scalp and roots',
            'Adds shine and luster'
        ],
        'ingredients' => 'Bhringraj Extract, Amla, Brahmi, Coconut Oil, Sesame Oil, Hibiscus',
        'usage' => 'Massage into scalp and hair. Leave for at least 1 hour or overnight. Wash with mild shampoo. Use 2-3 times weekly.',
        'size' => '4 fl oz (120ml)'
    ],
    10 => [
        'id' => 10,
        'name' => 'Tulsi Immunity Tea',
        'category' => 'Wellness',
        'price' => 14.99,
        'image' => 'assets/images/product10.jpg',
        'description' => 'Sacred holy basil tea blend to boost immunity and reduce stress.',
        'long_description' => 'Experience the sacred healing power of Tulsi with our Immunity Tea blend. Also known as Holy Basil, Tulsi is revered in Ayurveda as the "Queen of Herbs" for its remarkable adaptogenic and immune-supporting properties. This soothing tea blend combines three varieties of Tulsi for maximum benefit and delightful flavor.',
        'benefits' => [
            'Strengthens immune system',
            'Reduces stress and anxiety',
            'Supports respiratory health',
            'Rich in antioxidants',
            'Promotes mental clarity'
        ],
        'ingredients' => 'Organic Rama Tulsi, Organic Krishna Tulsi, Organic Vana Tulsi, Natural Flavors',
        'usage' => 'Steep 1 tea bag in hot water for 5-7 minutes. Enjoy 2-3 cups daily.',
        'size' => '20 tea bags'
    ],
    11 => [
        'id' => 11,
        'name' => 'Mahanarayan Pain Relief Oil',
        'category' => 'Wellness',
        'price' => 32.00,
        'image' => 'assets/images/product11.jpg',
        'description' => 'Ayurvedic massage oil for joint and muscle pain relief.',
        'long_description' => 'Find natural relief with our authentic Mahanarayan Oil, a classical Ayurvedic formulation featuring over 30 herbs. This warming massage oil penetrates deep to soothe sore muscles, stiff joints, and everyday aches. Perfect for athletes, active individuals, or anyone seeking natural pain management.',
        'benefits' => [
            'Relieves muscle and joint pain',
            'Reduces stiffness and inflammation',
            'Improves flexibility and mobility',
            'Warming and soothing',
            'Supports post-workout recovery'
        ],
        'ingredients' => 'Sesame Oil, Ashwagandha, Shatavari, Bala, Dashmool, and 25+ Traditional Herbs',
        'usage' => 'Warm oil slightly and massage into affected areas. Use daily or as needed.',
        'size' => '8 fl oz (240ml)'
    ],
    12 => [
        'id' => 12,
        'name' => 'Shatavari Women\'s Wellness',
        'category' => 'Supplements',
        'price' => 36.50,
        'image' => 'assets/images/product12.jpg',
        'description' => 'Hormonal balance and reproductive health support for women.',
        'long_description' => 'Shatavari, meaning "she who possesses a hundred husbands," is the premier Ayurvedic herb for women\'s health. Our Shatavari Women\'s Wellness supplement supports hormonal balance, reproductive health, and overall vitality throughout all stages of a woman\'s life. This gentle yet powerful adaptogen nourishes and rejuvenates.',
        'benefits' => [
            'Supports hormonal balance',
            'Promotes reproductive health',
            'Eases PMS and menopause symptoms',
            'Enhances vitality and energy',
            'Supports healthy lactation'
        ],
        'ingredients' => 'Organic Shatavari Root Extract (Asparagus racemosus), Vegetable Cellulose Capsule',
        'usage' => 'Take 2 capsules daily with meals or as directed by your healthcare practitioner.',
        'size' => '60 capsules'
    ]
];

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Check if product exists
if (!isset($products[$product_id])) {
    $product_id = 1; // Default to first product if invalid ID
}

$product = $products[$product_id];
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
                    <span id="cartTotal">$0.00</span>
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
                <div class="product-detail-price">$<?php echo number_format($product['price'], 2); ?></div>
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
