// Product Filter and Search Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Get all DOM elements
    const searchInput = document.getElementById('searchInput');
    const categoryBtns = document.querySelectorAll('.category-btn');
    const priceFilter = document.getElementById('priceFilter');
    const sortSelect = document.getElementById('sortSelect');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const productsGrid = document.querySelector('.products-grid');
    const noResults = document.getElementById('noResults');
    const resultsCount = document.getElementById('resultsCount');
    const totalCount = document.getElementById('totalCount');

    // Get all product cards
    let allProducts = Array.from(document.querySelectorAll('.product-card'));

    // Set total count
    totalCount.textContent = allProducts.length;

    // Current filter state
    let currentFilters = {
        search: '',
        category: 'all',
        priceRange: 'all',
        sort: 'default'
    };

    // Search functionality
    searchInput.addEventListener('input', function (e) {
        currentFilters.search = e.target.value.toLowerCase();
        applyFilters();
    });

    // Category filter functionality
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove active class from all buttons
            categoryBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Update filter
            currentFilters.category = this.dataset.category;
            applyFilters();
        });
    });

    // Price filter functionality
    priceFilter.addEventListener('change', function () {
        currentFilters.priceRange = this.value;
        applyFilters();
    });

    // Sort functionality
    sortSelect.addEventListener('change', function () {
        currentFilters.sort = this.value;
        applyFilters();
    });

    // Clear all filters
    clearFiltersBtn.addEventListener('click', function () {
        // Reset all filters
        searchInput.value = '';
        categoryBtns.forEach(btn => btn.classList.remove('active'));
        categoryBtns[0].classList.add('active'); // Activate "All"
        priceFilter.value = 'all';
        sortSelect.value = 'default';

        currentFilters = {
            search: '',
            category: 'all',
            priceRange: 'all',
            sort: 'default'
        };

        applyFilters();
    });

    // Main filter function
    function applyFilters() {
        let filteredProducts = [...allProducts];

        // Apply search filter
        if (currentFilters.search) {
            filteredProducts = filteredProducts.filter(product => {
                const title = product.querySelector('.product-title').textContent.toLowerCase();
                const description = product.querySelector('.product-description').textContent.toLowerCase();
                return title.includes(currentFilters.search) || description.includes(currentFilters.search);
            });
        }

        // Apply category filter
        if (currentFilters.category !== 'all') {
            filteredProducts = filteredProducts.filter(product => {
                const category = product.querySelector('.product-category').textContent;
                return category === currentFilters.category;
            });
        }

        // Apply price range filter
        if (currentFilters.priceRange !== 'all') {
            const [min, max] = currentFilters.priceRange.split('-').map(Number);
            filteredProducts = filteredProducts.filter(product => {
                const priceText = product.querySelector('.product-price').textContent;
                const price = parseFloat(priceText.replace('$', ''));
                return price >= min && price <= max;
            });
        }

        // Apply sorting
        if (currentFilters.sort !== 'default') {
            filteredProducts = sortProducts(filteredProducts, currentFilters.sort);
        }

        // Update display
        displayProducts(filteredProducts);
    }

    // Sort products
    function sortProducts(products, sortType) {
        const sorted = [...products];

        switch (sortType) {
            case 'price-low':
                sorted.sort((a, b) => {
                    const priceA = parseFloat(a.querySelector('.product-price').textContent.replace('$', ''));
                    const priceB = parseFloat(b.querySelector('.product-price').textContent.replace('$', ''));
                    return priceA - priceB;
                });
                break;
            case 'price-high':
                sorted.sort((a, b) => {
                    const priceA = parseFloat(a.querySelector('.product-price').textContent.replace('$', ''));
                    const priceB = parseFloat(b.querySelector('.product-price').textContent.replace('$', ''));
                    return priceB - priceA;
                });
                break;
            case 'name-asc':
                sorted.sort((a, b) => {
                    const nameA = a.querySelector('.product-title').textContent.toLowerCase();
                    const nameB = b.querySelector('.product-title').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                });
                break;
            case 'name-desc':
                sorted.sort((a, b) => {
                    const nameA = a.querySelector('.product-title').textContent.toLowerCase();
                    const nameB = b.querySelector('.product-title').textContent.toLowerCase();
                    return nameB.localeCompare(nameA);
                });
                break;
        }

        return sorted;
    }

    // Display filtered products
    function displayProducts(products) {
        // Hide all products first
        allProducts.forEach(product => {
            product.style.display = 'none';
        });

        // Show filtered products
        if (products.length > 0) {
            products.forEach(product => {
                product.style.display = 'block';
            });

            // Reorder products in the grid
            products.forEach(product => {
                productsGrid.appendChild(product);
            });

            // Hide no results message
            noResults.style.display = 'none';
            productsGrid.style.display = 'grid';
        } else {
            // Show no results message
            noResults.style.display = 'flex';
            productsGrid.style.display = 'none';
        }

        // Update results count
        resultsCount.textContent = products.length;

        // Add animation
        products.forEach((product, index) => {
            product.style.animation = 'none';
            setTimeout(() => {
                product.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.05}s`;
            }, 10);
        });
    }
});
