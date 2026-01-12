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

            // Check if button has data attributes directly (viewproduct.php)
            if (clickedButton.dataset.id && clickedButton.dataset.title) {
                const id = clickedButton.dataset.id;
                const title = clickedButton.dataset.title;
                const price = parseFloat(clickedButton.dataset.price);
                const image = clickedButton.dataset.image;

                addToCart({ id, title, price, image });
            } else {
                // Otherwise, look for product-card parent (products.php)
                const card = clickedButton.closest('.product-card');
                if (card) {
                    const id = card.dataset.id;
                    const title = card.querySelector('.product-title').innerText;
                    const priceText = card.querySelector('.product-price').innerText;
                    const price = parseFloat(priceText.replace('$', ''));
                    const image = card.querySelector('img').src;

                    addToCart({ id, title, price, image });
                }
            }

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
        cartItemsContainer.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align: center; color: #888; margin-top: 2rem;">Your cart is empty.</p>';
        } else {
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.innerHTML = `
                    <img src="${item.image}" alt="${item.title}">
                    <div class="cart-item-info">
                        <h4 class="cart-item-title">${item.title}</h4>
                        <span class="cart-item-price">$${item.price.toFixed(2)}</span>
                        <div class="cart-item-controls">
                            <div class="quantity-controls">
                                <button class="decrease-qty" data-id="${item.id}">-</button>
                                <span style="margin: 0 10px;">${item.quantity}</span>
                                <button class="increase-qty" data-id="${item.id}">+</button>
                            </div>
                            <button class="remove-item" data-id="${item.id}" style="border:none; background:none; color:red; cursor:pointer;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);
            });
        }

        cartTotalElement.innerText = '$' + total.toFixed(2);

        // Attach event listeners to new elements
        document.querySelectorAll('.decrease-qty').forEach(btn => {
            btn.addEventListener('click', (e) => updateQuantity(e.target.dataset.id, -1));
        });

        document.querySelectorAll('.increase-qty').forEach(btn => {
            btn.addEventListener('click', (e) => updateQuantity(e.target.dataset.id, 1));
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            // Handle the button itself or the icon inside
            const id = btn.dataset.id || btn.closest('button').dataset.id;
            btn.addEventListener('click', () => removeFromCart(id));
        });
    }
});
