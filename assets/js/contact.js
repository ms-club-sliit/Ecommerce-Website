// Contact Page JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // FAQ Accordion
    initFAQAccordion();

    // Contact Form Handling
    initContactForm();

    // Scroll Animations
    initScrollAnimations();

    // Smooth Scroll for Scroll Indicator
    initSmoothScroll();
});

// FAQ Accordion Functionality
function initFAQAccordion() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', () => {
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });

            // Toggle current item
            item.classList.toggle('active');
        });
    });
}

// Contact Form Handling
function initContactForm() {
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    const submitBtn = form.querySelector('.submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Get form data
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value,
            newsletter: document.getElementById('newsletter').checked
        };

        // Validate form
        if (!validateForm(formData)) {
            showMessage('Please fill in all required fields correctly.', 'error');
            return;
        }

        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        submitBtn.disabled = true;

        // Simulate form submission (replace with actual API call)
        try {
            await simulateFormSubmission(formData);

            // Success
            showMessage('Thank you for contacting us! We\'ll get back to you within 24 hours.', 'success');
            form.reset();

            // Track newsletter signup
            if (formData.newsletter) {
                console.log('Newsletter subscription added for:', formData.email);
            }

        } catch (error) {
            // Error
            showMessage('Oops! Something went wrong. Please try again or email us directly.', 'error');
        } finally {
            // Reset button state
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            submitBtn.disabled = false;
        }
    });
}

// Form Validation
function validateForm(data) {
    // Check required fields
    if (!data.name || !data.email || !data.subject || !data.message) {
        return false;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
        return false;
    }

    // Validate message length
    if (data.message.length < 10) {
        return false;
    }

    return true;
}

// Show Form Message
function showMessage(message, type) {
    const formMessage = document.getElementById('formMessage');
    formMessage.textContent = message;
    formMessage.className = 'form-message ' + type;

    // Scroll to message
    formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide success message after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            formMessage.style.display = 'none';
        }, 5000);
    }
}

// Simulate Form Submission (Replace with actual backend call)
function simulateFormSubmission(data) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            // Simulate 95% success rate
            if (Math.random() > 0.05) {
                console.log('Form submitted:', data);
                resolve();
            } else {
                reject(new Error('Submission failed'));
            }
        }, 1500);
    });
}

// Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all animated elements
    const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        observer.observe(el);
    });
}

// Smooth Scroll for Scroll Indicator
function initSmoothScroll() {
    const scrollIndicator = document.querySelector('.scroll-indicator');

    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function () {
            const contactInfoSection = document.querySelector('.contact-info-section');
            if (contactInfoSection) {
                contactInfoSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
}

// Input Focus Effects
document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(input => {
    input.addEventListener('focus', function () {
        this.parentElement.classList.add('focused');
    });

    input.addEventListener('blur', function () {
        this.parentElement.classList.remove('focused');
    });
});

// Phone Number Formatting (Optional Enhancement)
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
            } else {
                value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
            }
        }
        e.target.value = value;
    });
}

// Character Counter for Message (Optional Enhancement)
const messageTextarea = document.getElementById('message');
if (messageTextarea) {
    const charCountDiv = document.createElement('div');
    charCountDiv.className = 'char-count';
    charCountDiv.style.cssText = 'text-align: right; font-size: 0.85rem; color: #888; margin-top: 0.3rem;';
    messageTextarea.parentElement.appendChild(charCountDiv);

    messageTextarea.addEventListener('input', function () {
        const length = this.value.length;
        charCountDiv.textContent = `${length} characters`;

        if (length < 10) {
            charCountDiv.style.color = '#E85D04';
        } else {
            charCountDiv.style.color = '#1A3C34';
        }
    });
}

// Add active class to current nav link
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-links a').forEach(link => {
    if (link.getAttribute('href') === currentPage) {
        link.classList.add('active');
    }
});

// Map Interaction Enhancement
const mapIframe = document.querySelector('.map-container iframe');
if (mapIframe) {
    // Prevent scroll zoom on map until clicked
    mapIframe.style.pointerEvents = 'none';

    mapIframe.addEventListener('click', function () {
        this.style.pointerEvents = 'auto';
    });

    // Reset when mouse leaves
    document.querySelector('.map-container').addEventListener('mouseleave', function () {
        mapIframe.style.pointerEvents = 'none';
    });
}
