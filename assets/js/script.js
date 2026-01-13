// Cart Functionality
document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const cartCount = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartBtn = document.getElementById('cartBtn');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartModal = document.getElementById('cartModal');
    const closeCartBtn = document.getElementById('closeCart');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalElement = document.getElementById('cartTotal');

    // State
    let cart = JSON.parse(localStorage.getItem('ayurvedaCart')) || [];
    
    // Clean up any invalid items from localStorage on init
    cart = cart.filter(item => {
        return item && 
               item.id && 
               item.title && 
               item.price !== null && 
               item.price !== undefined && 
               !isNaN(parseFloat(item.price)) && 
               item.image;
    });
    
    // Save cleaned cart
    if (cart.length > 0) {
        localStorage.setItem('ayurvedaCart', JSON.stringify(cart));
    }

    // Init
    updateCartCount();

    // Event Listeners
    if (cartBtn) {
        cartBtn.addEventListener('click', openCart);
    }

    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', closeCart);
    }

    if (cartOverlay) {
        cartOverlay.addEventListener('click', (e) => {
            if (e.target === cartOverlay) closeCart();
        });
    }

    addToCartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            // Use currentTarget to always get the button element, not the clicked child element
            const clickedButton = e.currentTarget;

            let productData = null;

            // Check if button has data attributes directly (viewproduct.php or index.php)
            if (clickedButton.dataset.id && (clickedButton.dataset.title || clickedButton.dataset.name)) {
                const id = clickedButton.dataset.id;
                const title = clickedButton.dataset.title || clickedButton.dataset.name;
                const price = parseFloat(clickedButton.dataset.price);
                const image = clickedButton.dataset.image;

                if (id && title && !isNaN(price) && image) {
                    productData = { id, title, price, image };
                }
            } else {
                // Otherwise, look for product-card parent (products.php)
                const card = clickedButton.closest('.product-card');
                if (card) {
                    const id = card.dataset.id || clickedButton.dataset.id;
                    const titleElement = card.querySelector('.product-title');
                    const priceElement = card.querySelector('.product-price');
                    const imageElement = card.querySelector('img');

                    if (titleElement && priceElement && imageElement) {
                        const title = titleElement.innerText;
                        const priceText = priceElement.innerText;
                        const price = parseFloat(priceText.replace(/[₹$Rs.,\s]/g, '').trim());
                        const image = imageElement.src;

                        if (id && title && !isNaN(price) && image) {
                            productData = { id, title, price, image };
                        }
                    }
                }
            }

            if (productData) {
                addToCart(productData);

                // Visual feedback
                const originalText = clickedButton.innerText;
                clickedButton.innerText = "Added!";
                clickedButton.style.backgroundColor = "var(--color-secondary)";
                clickedButton.style.color = "var(--color-primary)";

                setTimeout(() => {
                    clickedButton.innerText = originalText;
                    clickedButton.style.backgroundColor = "";
                    clickedButton.style.color = "";
                }, 1000);

                openCart();
            } else {
                console.error('Failed to add product to cart: Invalid product data');
            }
        });
    });

    // Checkout button event listener
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            window.location.href = 'checkout.php';
        });
    }

    // Functions
    function openCart() {
        renderCartItems();
        cartOverlay.style.display = 'flex';
        // Small delay to allow display flex to apply before adding active class for transition
        setTimeout(() => {
            cartModal.classList.add('active');
        }, 10);
    }

    function closeCart() {
        cartModal.classList.remove('active');
        setTimeout(() => {
            cartOverlay.style.display = 'none';
        }, 300);
    }

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ ...product, quantity: 1 });
        }

        saveCart();
        updateCartCount();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        saveCart();
        renderCartItems();
        updateCartCount();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(id);
            } else {
                saveCart();
                renderCartItems();
                updateCartCount();
            }
        }
    }

    function saveCart() {
        localStorage.setItem('ayurvedaCart', JSON.stringify(cart));
    }

    function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCount) cartCount.innerText = totalItems;
    }

    function renderCartItems() {
        if (!cartItemsContainer || !cartTotalElement) {
            console.error('Cart elements not found');
            return;
        }

        cartItemsContainer.innerHTML = '';
        let total = 0;

        // Filter out any invalid items (null price or missing data)
        cart = cart.filter(item => item && item.price && !isNaN(item.price) && item.id && item.title);
        saveCart();

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align: center; color: #888; margin-top: 2rem;">Your cart is empty.</p>';
            cartTotalElement.innerText = 'Rs. 0.00';
        } else {
            cart.forEach(item => {
                const itemPrice = parseFloat(item.price) || 0;
                const itemQuantity = parseInt(item.quantity) || 1;
                const itemTotal = itemPrice * itemQuantity;
                total += itemTotal;

                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.innerHTML = `
                    <img src="${item.image || 'assets/images/placeholder.jpg'}" alt="${item.title}" onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="cart-item-info">
                        <h4 class="cart-item-title">${item.title}</h4>
                        <span class="cart-item-price">Rs. ${itemPrice.toFixed(2)}</span>
                        <div class="cart-item-controls">
                            <div class="quantity-controls">
                                <button class="decrease-qty" data-id="${item.id}">-</button>
                                <span class="quantity-display">${itemQuantity}</span>
                                <button class="increase-qty" data-id="${item.id}">+</button>
                            </div>
                            <button class="remove-item" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);
            });

            cartTotalElement.innerText = 'Rs. ' + total.toFixed(2);
        }

        // Attach event listeners to new elements
        document.querySelectorAll('.decrease-qty').forEach(btn => {
            btn.addEventListener('click', (e) => updateQuantity(e.target.dataset.id, -1));
        });

        document.querySelectorAll('.increase-qty').forEach(btn => {
            btn.addEventListener('click', (e) => updateQuantity(e.target.dataset.id, 1));
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Handle the button itself or the icon inside
                const id = e.currentTarget.dataset.id;
                if (id) removeFromCart(id);
            });
        });
    }
});
