// ===== FONCTIONS POUR LA PAGE MENU =====
function displayProducts(productsToDisplay) {
    const container = document.getElementById('products-container');
    if (!container) return;

    container.innerHTML = '';

    // Mettre à jour le compteur de résultats
    const resultsCount = document.getElementById('results-count');
    if (resultsCount) {
        resultsCount.textContent = `${productsToDisplay.length} produit${productsToDisplay.length > 1 ? 's' : ''}`;
    }

    if (productsToDisplay.length === 0) {
        container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #666;">Aucun produit trouvé dans cette catégorie.</p>';
        return;
    }

    productsToDisplay.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <div class="product-image" onclick="this.nextElementSibling.querySelector('.product-description').style.display = this.nextElementSibling.querySelector('.product-description').style.display === 'none' ? 'block' : 'none'" style="cursor: pointer;" title="Cliquez pour afficher/masquer la description">
                <img src="${product.image}" alt="${product.name}" loading="lazy">
            </div>
            <div class="product-content">
                <span class="product-category">${getCategoryLabel(product.category)}</span>
                <h3 class="product-title">${product.name}</h3>
                <p class="product-description" style="display: none; transition: all 0.3s;">${product.description}</p>
                <div class="product-footer">
                    <span class="product-price">${product.price.toFixed(2)} DT</span>
                    <button class="add-to-cart" data-id="${product.id}">
                        <i class="fas fa-cart-plus"></i> Ajouter
                    </button>
                </div>
            </div>
        `;
        container.appendChild(productCard);
    });

    // Ajouter les événements pour les boutons "Ajouter au panier"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            addToCart(productId);
        });
    });
}

function initFilters() {
    // Mettre à jour les compteurs de catégories
    updateCategoryCounts();

    // Gestion des filtres par catégorie
    const categoryFilters = document.querySelectorAll('.category-filter-item');
    
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function () {
            // Mettre à jour le filtre actif
            categoryFilters.forEach(item => item.classList.remove('active'));
            this.classList.add('active');

            // Appliquer les filtres
            applyFilters();
        });
    });

    // Gestion du filtre par prix
    const minPriceSlider = document.getElementById('min-price');
    const maxPriceSlider = document.getElementById('max-price');
    const minPriceDisplay = document.getElementById('min-price-display');
    const maxPriceDisplay = document.getElementById('max-price-display');

    if (minPriceSlider && maxPriceSlider) {
        // Mettre à jour les affichages
        function updatePriceDisplays() {
            if (minPriceDisplay) {
                minPriceDisplay.textContent = `${minPriceSlider.value}DT`;
            }
            if (maxPriceDisplay) {
                maxPriceDisplay.textContent = `${maxPriceSlider.value}DT`;
            }
            applyFilters();
        }

        minPriceSlider.addEventListener('input', updatePriceDisplays);
        maxPriceSlider.addEventListener('input', updatePriceDisplays);
        
        // Initialiser les affichages
        updatePriceDisplays();
    }

    // Réinitialiser les filtres
    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            // Réinitialiser les filtres de catégorie
            categoryFilters.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-category') === 'all') {
                    item.classList.add('active');
                }
            });

            // Réinitialiser les sliders de prix
            if (minPriceSlider && maxPriceSlider) {
                minPriceSlider.value = 0;
                maxPriceSlider.value = 10;
                updatePriceDisplays();
            }

            // Réinitialiser le tri
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                sortSelect.value = 'default';
            }

            // Appliquer les filtres
            applyFilters();
        });
    }
}

function updateCategoryCounts() {
    // Compter les produits par catégorie
    const counts = {
        'all': products.length,
        'boissons-chaudes': products.filter(p => p.category === 'boissons-chaudes').length,
        'boissons-froides': products.filter(p => p.category === 'boissons-froides').length,
        'patisseries': products.filter(p => p.category === 'patisseries').length,
        'sandwiches': products.filter(p => p.category === 'sandwiches').length
    };

    // Mettre à jour les affichages
    Object.keys(counts).forEach(category => {
        const element = document.getElementById(`count-${category}`);
        if (element) {
            element.textContent = counts[category];
        }
    });
}

function initSorting() {
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', applyFilters);
    }
}

function applyFilters() {
    // Récupérer la catégorie active
    const activeFilter = document.querySelector('.category-filter-item.active');
    const category = activeFilter ? activeFilter.getAttribute('data-category') : 'all';

    // Récupérer les valeurs de prix
    const minPrice = parseFloat(document.getElementById('min-price')?.value || 0);
    const maxPrice = parseFloat(document.getElementById('max-price')?.value || 10);

    // Récupérer l'ordre de tri
    const sortValue = document.getElementById('sort-select')?.value || 'default';

    // Filtrer les produits
    let filteredProducts = products;

    // Filtrer par catégorie
    if (category !== 'all') {
        filteredProducts = filteredProducts.filter(product => product.category === category);
    }

    // Filtrer par prix
    filteredProducts = filteredProducts.filter(product => 
        product.price >= minPrice && product.price <= maxPrice
    );

    // Trier les produits
    filteredProducts = sortProducts(filteredProducts, sortValue);

    // Afficher les produits filtrés
    displayProducts(filteredProducts);
}

function sortProducts(productsArray, sortBy) {
    const sortedArray = [...productsArray];

    switch (sortBy) {
        case 'price-asc':
            return sortedArray.sort((a, b) => a.price - b.price);
        case 'price-desc':
            return sortedArray.sort((a, b) => b.price - a.price);
        case 'popularity':
            return sortedArray.sort((a, b) => b.popularity - a.popularity);
        default:
            return sortedArray;
    }
}

function initCartSection() {
    // Mettre à jour l'affichage initial du panier
    updateCartDisplay();

    // Événements pour les boutons du panier
    const checkoutBtn = document.getElementById('checkout-btn');
    const clearBtn = document.getElementById('clear-btn');
    
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', checkout);
    }
    
    if (clearBtn) {
        clearBtn.addEventListener('click', clearCart);
    }
}

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');

    if (!cartItemsContainer || !cartTotalElement) return;

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart">Votre panier est vide</p>';
        cartTotalElement.textContent = '0.00 DT';
        return;
    }

    // Calculer le total
    let total = 0;

    // Afficher les éléments du panier
    cartItemsContainer.innerHTML = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">${item.price.toFixed(2)} DT</div>
            </div>
            <div class="cart-item-actions">
                <button class="quantity-btn decrease" data-id="${item.id}" aria-label="Diminuer la quantité">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="cart-item-quantity">${item.quantity}</span>
                <button class="quantity-btn increase" data-id="${item.id}" aria-label="Augmenter la quantité">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="remove-item" data-id="${item.id}" aria-label="Supprimer l'article">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        cartItemsContainer.appendChild(cartItem);
    });

    // Mettre à jour le total
    cartTotalElement.textContent = `${total.toFixed(2)} DT`;

    // Ajouter les événements pour les boutons du panier
    document.querySelectorAll('.decrease').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            updateCartItemQuantity(productId, -1);
            updateCartDisplay();
        });
    });

    document.querySelectorAll('.increase').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            updateCartItemQuantity(productId, 1);
            updateCartDisplay();
        });
    });

    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            removeFromCart(productId);
            updateCartDisplay();
        });
    });
}

// ===== INITIALISATION DE LA PAGE MENU =====
function initMenuPage() {
    // Afficher tous les produits initialement
    displayProducts(products);

    // Initialiser les filtres
    initFilters();

    // Initialiser le tri
    initSorting();

    // Initialiser la section du panier
    initCartSection();
}

// Initialiser la page menu au chargement
document.addEventListener('DOMContentLoaded', initMenuPage);
