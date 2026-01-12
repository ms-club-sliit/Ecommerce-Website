// Authentication JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');

    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('password-strength');
    const strengthText = document.getElementById('strength-text');

    if (passwordInput && strengthMeter) {
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            const strength = calculatePasswordStrength(password);

            // Update strength meter
            strengthMeter.className = 'strength-meter';
            strengthMeter.classList.add(strength.class);
            strengthMeter.style.width = strength.width;

            if (strengthText) {
                strengthText.textContent = strength.text;
                strengthText.className = 'strength-text';
                strengthText.classList.add(strength.class);
            }
        });
    }

    // Form validation
    const signupForm = document.querySelector('.auth-form[action="process_signup.php"]');
    const loginForm = document.querySelector('.auth-form[action="process_login.php"]');

    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.querySelector('input[name="terms"]').checked;

            let errors = [];

            // Validate name
            if (name.length < 2) {
                errors.push('Name must be at least 2 characters long');
            }

            // Validate email
            if (!isValidEmail(email)) {
                errors.push('Please enter a valid email address');
            }

            // Validate phone
            if (phone.length < 10) {
                errors.push('Please enter a valid phone number');
            }

            // Validate password
            if (password.length < 6) {
                errors.push('Password must be at least 6 characters long');
            }

            // Validate password match
            if (password !== confirmPassword) {
                errors.push('Passwords do not match');
            }

            // Validate terms
            if (!terms) {
                errors.push('You must accept the Terms & Conditions');
            }

            if (errors.length > 0) {
                showErrors(errors);
            } else {
                // Submit form
                this.submit();
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            let errors = [];

            // Validate email
            if (!isValidEmail(email)) {
                errors.push('Please enter a valid email address');
            }

            // Validate password
            if (password.length === 0) {
                errors.push('Please enter your password');
            }

            if (errors.length > 0) {
                showErrors(errors);
            } else {
                // Submit form
                this.submit();
            }
        });
    }
});

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;

    if (password.length === 0) {
        return { class: '', width: '0%', text: '' };
    }

    // Length check
    if (password.length >= 6) strength += 1;
    if (password.length >= 10) strength += 1;

    // Character variety checks
    if (/[a-z]/.test(password)) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;

    // Return strength level
    if (strength <= 2) {
        return { class: 'weak', width: '33%', text: 'Weak' };
    } else if (strength <= 4) {
        return { class: 'medium', width: '66%', text: 'Medium' };
    } else {
        return { class: 'strong', width: '100%', text: 'Strong' };
    }
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show errors
function showErrors(errors) {
    // Remove existing error messages
    const existingErrors = document.querySelector('.error-messages');
    if (existingErrors) {
        existingErrors.remove();
    }

    // Create error container
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-messages';

    const errorList = document.createElement('ul');
    errors.forEach(error => {
        const li = document.createElement('li');
        li.textContent = error;
        errorList.appendChild(li);
    });

    errorDiv.appendChild(errorList);

    // Insert before form
    const form = document.querySelector('.auth-form');
    form.parentNode.insertBefore(errorDiv, form);

    // Scroll to errors
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Auto-remove after 5 seconds
    setTimeout(() => {
        errorDiv.style.opacity = '0';
        setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
}

// Show success message
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;

    const form = document.querySelector('.auth-form');
    form.parentNode.insertBefore(successDiv, form);

    setTimeout(() => {
        successDiv.style.opacity = '0';
        setTimeout(() => successDiv.remove(), 300);
    }, 3000);
}

// Display server-side errors if present
window.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('error') === '1') {
        // You can fetch errors from session via AJAX or display a generic message
        showErrors(['An error occurred. Please check your information and try again.']);
    }

    if (urlParams.get('signup') === 'success') {
        showSuccess('Registration successful! Please login with your credentials.');
    }

    if (urlParams.get('login') === 'success') {
        showSuccess('Welcome back! You are now logged in.');
    }

    if (urlParams.get('logout') === 'success') {
        showSuccess('You have been logged out successfully.');
    }
});
