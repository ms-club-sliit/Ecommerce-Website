<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | VedaLife - Ancient Wisdom, Modern Wellness</title>
    <meta name="description" content="Discover VedaLife's journey in bringing authentic Ayurvedic wisdom to modern wellness. Learn about our commitment to organic, handcrafted herbal remedies.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <script src="assets/js/script.js?v=1.1" defer></script>
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

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-hero-content">
            <h1 class="fade-in-up">Our Story</h1>
            <p class="fade-in-up delay-1">Where Ancient Wisdom Meets Modern Wellness</p>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-image fade-in-left">
                    <div class="image-wrapper">
                        <img src="assets/images/mission.jpg" alt="VedaLife Mission">
                        <div class="image-overlay"></div>
                    </div>
                </div>
                <div class="mission-content fade-in-right">
                    <span class="section-label">Our Mission</span>
                    <h2>Bringing Balance to Your Life</h2>
                    <p>At VedaLife, we believe that true wellness comes from harmony between mind, body, and spirit. Our mission is to make the timeless wisdom of Ayurveda accessible to everyone, offering pure, potent, and ethically sourced herbal remedies that honor both tradition and science.</p>
                    <p>Every product we create is a testament to our commitment to authenticity, sustainability, and your well-being. We work directly with organic farms in Kerala, India, ensuring that each herb is harvested at its peak potency and processed with care.</p>
                    <div class="mission-stats">
                        <div class="stat-item">
                            <span class="stat-number">5000+</span>
                            <span class="stat-label">Happy Customers</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Organic Ingredients</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Ayurvedic Products</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">What We Stand For</span>
                <h2>Our Core Values</h2>
                <p>The principles that guide everything we do</p>
            </div>
            <div class="values-grid">
                <div class="value-card fade-in-up">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>100% Organic</h3>
                    <p>We source only certified organic herbs from sustainable farms, ensuring purity and environmental responsibility in every product.</p>
                </div>
                <div class="value-card fade-in-up delay-1">
                    <div class="value-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Handcrafted Excellence</h3>
                    <p>Each remedy is carefully prepared in small batches using traditional methods to preserve maximum potency and healing properties.</p>
                </div>
                <div class="value-card fade-in-up delay-2">
                    <div class="value-icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <h3>Science-Backed</h3>
                    <p>We combine ancient Ayurvedic wisdom with modern scientific research to create products that are both traditional and effective.</p>
                </div>
                <div class="value-card fade-in-up delay-3">
                    <div class="value-icon">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <h3>Ethically Sourced</h3>
                    <p>We partner directly with farming communities, ensuring fair wages and sustainable practices that benefit both people and planet.</p>
                </div>
                <div class="value-card fade-in-up delay-4">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Customer First</h3>
                    <p>Your wellness journey is our priority. We provide personalized support and guidance to help you find the perfect remedies.</p>
                </div>
                <div class="value-card fade-in-up delay-5">
                    <div class="value-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Quality Assured</h3>
                    <p>Every batch undergoes rigorous testing for purity, potency, and safety, meeting the highest international standards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Journey Section -->
    <section class="journey-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Journey</span>
                <h2>From Kerala to Your Home</h2>
            </div>
            <div class="timeline">
                <div class="timeline-item fade-in-up">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2018</span>
                        <h3>The Beginning</h3>
                        <p>Founded by Dr. Priya Sharma, an Ayurvedic practitioner with a vision to make authentic herbal remedies accessible to all.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in-up delay-1">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2019</span>
                        <h3>Farm Partnerships</h3>
                        <p>Established direct relationships with organic farms in Kerala, ensuring ethical sourcing and premium quality herbs.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in-up delay-2">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2021</span>
                        <h3>Product Expansion</h3>
                        <p>Launched our complete wellness line, including skincare, supplements, and specialized Ayurvedic formulations.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in-up delay-3">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2023</span>
                        <h3>Global Recognition</h3>
                        <p>Received international certifications and expanded our reach to wellness seekers across 25 countries.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in-up delay-4">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2026</span>
                        <h3>Continuing the Legacy</h3>
                        <p>Today, we serve thousands of customers worldwide, staying true to our mission of holistic wellness through Ayurveda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Meet Our Team</span>
                <h2>The Healers Behind VedaLife</h2>
                <p>Passionate experts dedicated to your wellness journey</p>
            </div>
            <div class="team-grid">
                <div class="team-card fade-in-up">
                    <div class="team-image">
                        <img src="assets/images/drpriya.jpg" alt="Dr. Priya Sharma">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h3>Dr. Priya Sharma</h3>
                        <span class="team-role">Founder & Chief Ayurvedic Officer</span>
                        <p>20+ years of experience in traditional Ayurvedic medicine and herbal formulation.</p>
                    </div>
                </div>
                <div class="team-card fade-in-up delay-1">
                    <div class="team-image">
                        <img src="assets/images/Rejesh.jpg" alt="Rajesh Kumar">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h3>Rajesh Kumar</h3>
                        <span class="team-role">Head of Sourcing</span>
                        <p>Expert in organic farming and sustainable herb cultivation with deep roots in Kerala.</p>
                    </div>
                </div>
                <div class="team-card fade-in-up delay-2">
                    <div class="team-image">
                        <img src="assets/images/Maya.jpg" alt="Maya Patel">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h3>Maya Patel</h3>
                        <span class="team-role">Wellness Consultant</span>
                        <p>Certified nutritionist specializing in Ayurvedic dietary principles and holistic health.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Begin Your Wellness Journey</h2>
                <p>Experience the transformative power of authentic Ayurveda. Join thousands who have discovered natural healing with VedaLife.</p>
                <div class="cta-buttons">
                    <a href="index.php#products" class="btn btn-primary">Explore Products</a>
                    <a href="#" class="btn btn-secondary">Take Dosha Quiz</a>
                </div>
            </div>
        </div>
    </section>

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
                        <li><a href="index.php#products">Shop All</a></li>
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

    <script src="assets/js/about.js"></script>
</body>
</html>
