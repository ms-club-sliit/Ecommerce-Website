<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | VedaLife - We're Here to Help</title>
    <meta name="description" content="Get in touch with VedaLife. Our wellness experts are ready to guide you on your Ayurvedic journey. Contact us for personalized support.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <script src="assets/js/script.js?v=1.1" defer></script>
    <script src="assets/js/contact.js" defer></script>
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
    <section class="contact-hero">
        <div class="contact-hero-content">
            <h1 class="fade-in-up">Get In Touch</h1>
            <p class="fade-in-up delay-1">We're here to support your wellness journey</p>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info-section">
        <div class="container">
            <div class="contact-info-grid">
                <div class="info-card fade-in-up">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p>123 Wellness Boulevard<br>Kerala, India 682001</p>
                </div>
                <div class="info-card fade-in-up delay-1">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p>+1 (555) 123-4567<br>Mon-Fri: 9AM - 6PM EST</p>
                </div>
                <div class="info-card fade-in-up delay-2">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>hello@vedalife.com<br>support@vedalife.com</p>
                </div>
                <div class="info-card fade-in-up delay-3">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Business Hours</h3>
                    <p>Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
        <div class="container">
            <div class="contact-grid">
                <div class="form-content fade-in-left">
                    <span class="section-label">Send Us a Message</span>
                    <h2>How Can We Help You?</h2>
                    <p>Whether you have questions about our products, need personalized wellness advice, or want to learn more about Ayurveda, our team is here to assist you.</p>
                    
                    <div class="contact-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Personalized wellness consultations</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Product recommendations based on your dosha</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Expert Ayurvedic guidance</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>24-hour response time guarantee</span>
                        </div>
                    </div>

                    <div class="social-connect">
                        <h4>Connect With Us</h4>
                        <div class="social-links">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper fade-in-right">
                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required placeholder="Enter your full name">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a topic</option>
                                <option value="product-inquiry">Product Inquiry</option>
                                <option value="wellness-consultation">Wellness Consultation</option>
                                <option value="order-support">Order Support</option>
                                <option value="partnership">Partnership Opportunities</option>
                                <option value="feedback">Feedback & Suggestions</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Your Message *</label>
                            <textarea id="message" name="message" rows="6" required placeholder="Tell us how we can help you..."></textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="newsletter" name="newsletter">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">Subscribe to our newsletter for wellness tips and exclusive offers</span>
                            </label>
                        </div>

                        <button type="submit" class="submit-btn">
                            <span class="btn-text">Send Message</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Sending...
                            </span>
                        </button>

                        <div class="form-message" id="formMessage"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="map-container">
            <div class="map-overlay">
                <div class="map-info">
                    <h3>Visit Our Wellness Center</h3>
                    <p>Experience our products firsthand and consult with our Ayurvedic experts</p>
                    <a href="#" class="btn btn-primary">Get Directions</a>
                </div>
            </div>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.0449!2d76.2673!3d9.9312!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwNTUnNTIuMyJOIDc2wrAxNicwMi40IkU!5e0!3m2!1sen!2sin!4v1234567890"
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Frequently Asked Questions</span>
                <h2>Quick Answers</h2>
                <p>Find answers to common questions about our products and services</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item fade-in-up">
                    <div class="faq-question">
                        <h4>How long does shipping take?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer fast shipping worldwide. Domestic orders typically arrive within 3-5 business days, while international orders take 7-14 business days. All orders are tracked and insured.</p>
                    </div>
                </div>

                <div class="faq-item fade-in-up delay-1">
                    <div class="faq-question">
                        <h4>Are your products certified organic?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes! All our products are made from 100% certified organic ingredients sourced directly from sustainable farms in Kerala, India. We maintain strict quality control and testing standards.</p>
                    </div>
                </div>

                <div class="faq-item fade-in-up delay-2">
                    <div class="faq-question">
                        <h4>Can I get personalized product recommendations?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Absolutely! Our wellness consultants can help you choose products based on your dosha type and specific health goals. You can also take our free online Dosha Quiz for instant recommendations.</p>
                    </div>
                </div>

                <div class="faq-item fade-in-up delay-3">
                    <div class="faq-question">
                        <h4>What is your return policy?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer a 30-day satisfaction guarantee. If you're not completely satisfied with your purchase, you can return it for a full refund. Please contact our support team to initiate a return.</p>
                    </div>
                </div>

                <div class="faq-item fade-in-up delay-4">
                    <div class="faq-question">
                        <h4>Do you offer wholesale or bulk pricing?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we work with wellness centers, spas, and retailers worldwide. Please contact us through the form above with "Partnership Opportunities" as the subject for wholesale pricing information.</p>
                    </div>
                </div>

                <div class="faq-item fade-in-up delay-5">
                    <div class="faq-question">
                        <h4>How do I know which products are right for me?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Start by taking our Dosha Quiz to understand your Ayurvedic constitution. Based on your results, we'll recommend products tailored to your unique needs. You can also schedule a consultation with our experts.</p>
                    </div>
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
</body>
</html>
