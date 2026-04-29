// ===== VARIABLES GLOBALES =====
let cart = [];
let slideInterval;

// ===== DONNÉES DES PRODUITS =====
var products = [
    {
        id: 1,
        name: "Expresso",
        category: "boissons-chaudes",
        description: "Un café court et intense, parfait pour un coup de boost.",
        price: 2.50,
        image: "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 9
    },
    {
        id: 2,
        name: "Cappuccino",
        category: "boissons-chaudes",
        description: "Café expresso avec du lait mousseux, saupoudré de cacao.",
        price: 3.80,
        image: "https://images.unsplash.com/photo-1570197788417-0e82375c9371?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 8
    },
    {
        id: 3,
        name: "Thé Vert Matcha",
        category: "boissons-chaudes",
        description: "Thé vert japonais moulu, riche en antioxydants.",
        price: 4.20,
        image: "https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 7
    },
    {
        id: 4,
        name: "Chocolat Viennois",
        category: "boissons-chaudes",
        description: "Chocolat chaud généreux surmonté de crème fouettée.",
        price: 4.50,
        image: "https://images.unsplash.com/photo-1544787219-7f47ccb76574?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 6
    },
    {
        id: 5,
        name: "Smoothie Fruits Rouges",
        category: "boissons-froides",
        description: "Mélange de fraises, framboises et myrtilles fraîches.",
        price: 5.20,
        image: "https://images.unsplash.com/photo-1502741224143-90386d7f8c82?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 8
    },
    {
        id: 6,
        name: "Limonade Maison",
        category: "boissons-froides",
        description: "Limonade fraîchement pressée avec des citrons bio.",
        price: 3.90,
        image: "https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 7
    },
    {
        id: 7,
        name: "Croissant",
        category: "patisseries",
        description: "Croissant au beurre feuilleté, croustillant à l'extérieur.",
        price: 2.80,
        image: "https://images.unsplash.com/photo-1555507036-ab794f27d2e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 9
    },
    {
        id: 8,
        name: "Tarte au Citron",
        category: "patisseries",
        description: "Tartelette au citron meringuée, acidulée et douce.",
        price: 4.50,
        image: "https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 8
    },
    {
        id: 9,
        name: "Muffin Chocolat",
        category: "patisseries",
        description: "Muffin moelleux avec des pépites de chocolat noir.",
        price: 3.20,
        image: "https://images.unsplash.com/photo-1576866209830-589e1bfbaa4d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 7
    },
    {
        id: 10,
        name: "Sandwich Jambon-Beurre",
        category: "sandwiches",
        description: "Jambon de qualité et beurre doux sur baguette fraîche.",
        price: 5.80,
        image: "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 8
    },
    {
        id: 11,
        name: "Panini Poulet Avocat",
        category: "sandwiches",
        description: "Poulet grillé, avocat et fromage fondant dans un panini.",
        price: 7.20,
        image: "https://images.unsplash.com/photo-1606755962773-d324e0a13086?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 9
    },
    {
        id: 12,
        name: "Salade César",
        category: "sandwiches",
        description: "Salade fraîche avec poulet grillé, parmesan et croûtons.",
        price: 8.50,
        image: "https://images.unsplash.com/photo-1546793665-c74683f339c1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
        popularity: 7
    }
];

// ===== FONCTIONS UTILITAIRES COMMUNES =====
function showNotification(message) {
    // Vérifier s'il y a déjà une notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Créer une notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #D4A76A;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        font-weight: 600;
        animation: slideInRight 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 350px;
    `;

    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Supprimer la notification après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);

    // Ajouter les animations CSS si elles n'existent pas déjà
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

function getCategoryLabel(category) {
    const labels = {
        'boissons-chaudes': 'Boissons Chaudes',
        'boissons-froides': 'Boissons Froides',
        'patisseries': 'Pâtisseries',
        'sandwiches': 'Sandwiches'
    };
    return labels[category] || category;
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (!cartCount) return;

    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);

    if (totalItems > 0) {
        cartCount.textContent = totalItems;
        cartCount.classList.remove('hidden');
    } else {
        cartCount.classList.add('hidden');
    }
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    // Vérifier si le produit est déjà dans le panier
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1
        });
    }

    // Sauvegarder dans le localStorage
    localStorage.setItem('cafeCart', JSON.stringify(cart));

    // Mettre à jour le compteur
    updateCartCount();

    // Afficher une notification
    showNotification(`${product.name} ajouté au panier`);
}

function updateCartItemQuantity(productId, change) {
    const itemIndex = cart.findIndex(item => item.id === productId);
    if (itemIndex === -1) return;

    cart[itemIndex].quantity += change;

    // Si la quantité devient 0, supprimer l'article
    if (cart[itemIndex].quantity <= 0) {
        cart.splice(itemIndex, 1);
    }

    // Sauvegarder dans le localStorage
    localStorage.setItem('cafeCart', JSON.stringify(cart));
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);

    // Sauvegarder dans le localStorage
    localStorage.setItem('cafeCart', JSON.stringify(cart));

    // Afficher une notification
    showNotification('Article supprimé du panier');
}

async function checkout() {
    // Check if user is logged in
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert('Veuillez vous connecter pour passer une commande.');
        window.location.href = 'auth.php?mode=login';
        return;
    }

    if (cart.length === 0) {
        alert('Votre panier est vide. Ajoutez des produits avant de commander.');
        return;
    }

    // Afficher un message de chargement (Reload/Loading state)
    const checkoutBtn = document.getElementById('checkout-button');
    const originalBtnContent = checkoutBtn.innerHTML;
    checkoutBtn.disabled = true;
    checkoutBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Chargement...';
    checkoutBtn.style.opacity = '0.7';

    try {
        const response = await fetch('konnect_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                cart: cart
            })
        });

        const result = await response.json();

        if (result.payUrl) {
            // Message de succès et redirection immédiate
            checkoutBtn.innerHTML = '<i class="fas fa-check"></i> Redirection...';
            checkoutBtn.style.backgroundColor = '#28a745';
            
            // Vider le panier localement
            cart = [];
            localStorage.removeItem('cafeCart');
            updateCartCount();
            if (typeof updateCartPanel === 'function') updateCartPanel();

            // Rediriger vers Konnect avec une petite temporisation pour assurer la stabilité
            setTimeout(() => {
                window.location.assign(result.payUrl);
            }, 100);
        } else {
            alert('Erreur lors de l\'initialisation du paiement: ' + (result.error || 'Erreur inconnue'));
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = originalBtnContent;
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la connexion au service de paiement.');
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML = originalBtnContent;
    }
}

function clearCart() {
    if (cart.length === 0) return;

    cart = [];
    localStorage.removeItem('cafeCart');
    updateCartCount();
    showNotification('Panier vidé');
}

// ===== FONCTIONS DU PANIER COULISSANT =====
function initCartPanel() {
    const cartIcon = document.querySelector('.cart-icon-container');
    const cartPanel = document.getElementById('cart-panel');
    const cartOverlay = document.getElementById('cart-overlay');
    const closeCartBtn = document.getElementById('close-cart');
    const checkoutBtn = document.getElementById('checkout-button');
    const clearCartBtn = document.getElementById('clear-cart-button');

    // Ouvrir le panier
    if (cartIcon) {
        cartIcon.addEventListener('click', openCartPanel);
    }

    // Fermer le panier
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', closeCartPanel);
    }

    if (cartOverlay) {
        cartOverlay.addEventListener('click', closeCartPanel);
    }

    // Commander
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', checkoutFromPanel);
    }

    // Vider le panier
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCartFromPanel);
    }
}

function openCartPanel() {
    const cartPanel = document.getElementById('cart-panel');
    const cartOverlay = document.getElementById('cart-overlay');

    if (cartPanel && cartOverlay) {
        cartPanel.classList.add('active');
        cartOverlay.classList.add('active');
        updateCartPanel();
    }
}

function closeCartPanel() {
    const cartPanel = document.getElementById('cart-panel');
    const cartOverlay = document.getElementById('cart-overlay');

    if (cartPanel && cartOverlay) {
        cartPanel.classList.remove('active');
        cartOverlay.classList.remove('active');
    }
}

// ... (le reste du code shared.js reste le même, sauf pour cette partie)...

function updateCartPanel() {
    const cartBody = document.getElementById('cart-body');
    const cartTotalAmount = document.getElementById('cart-total-amount');

    if (!cartBody) return;

    if (cart.length === 0) {
        cartBody.innerHTML = `
            <div class="empty-cart-message" id="empty-cart-message">
                <i class="fas fa-shopping-basket"></i>
                <p>Votre panier est vide</p>
                <p class="small-text">Ajoutez des produits depuis notre menu !</p>
            </div>
        `;
        cartTotalAmount.textContent = '0.00 DT';
        return;
    }

    // Calculer le total
    let total = 0;

    // Générer le contenu du panier
    let cartHTML = '<div class="cart-items-list">';

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        // Trouver l'image du produit
        const product = products.find(p => p.id === item.id);
        const productImage = product ? product.image : 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400';

        cartHTML += `
            <div class="cart-item" data-id="${item.id}">
                <img src="${productImage}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${item.price.toFixed(2)} DT</div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn decrease" data-id="${item.id}" aria-label="Diminuer la quantité">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantity-number">${item.quantity}</span>
                        <button class="quantity-btn increase" data-id="${item.id}" aria-label="Augmenter la quantité">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="remove-item" data-id="${item.id}" aria-label="Supprimer l'article">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    cartHTML += '</div>';

    cartBody.innerHTML = cartHTML;
    cartTotalAmount.textContent = `${total.toFixed(2)} DT`;

    // Ajouter les événements pour les boutons dans le panier
    document.querySelectorAll('.decrease').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            updateCartItemQuantity(productId, -1);
            updateCartPanel();
            updateCartCount();
        });
    });

    document.querySelectorAll('.increase').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            updateCartItemQuantity(productId, 1);
            updateCartPanel();
            updateCartCount();
        });
    });

    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function () {
            const productId = parseInt(this.getAttribute('data-id'));
            removeFromCart(productId);
            updateCartPanel();
            updateCartCount();
        });
    });
}
async function checkoutFromPanel() {
    if (cart.length === 0) {
        alert('Votre panier est vide. Ajoutez des produits avant de commander.');
        return;
    }

    await checkout();
}

function clearCartFromPanel() {
    if (cart.length === 0) {
        alert('Le panier est déjà vide.');
        return;
    }

    if (confirm('Voulez-vous vraiment vider votre panier ?')) {
        clearCart();
        updateCartPanel();
        closeCartPanel();
    }
}

// ===== INITIALISATION COMMUNE =====
function initCommon() {
    // Menu mobile
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }

    // Fermer le menu mobile quand on clique sur un lien
    document.querySelectorAll('.nav-links a').forEach(item => {
        item.addEventListener('click', function () {
            if (window.innerWidth <= 768 && hamburger && navLinks) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    });

    // Charger le panier depuis le localStorage
    const savedCart = localStorage.getItem('cafeCart');
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
            updateCartCount();
        } catch (e) {
            console.error('Erreur lors du chargement du panier:', e);
            localStorage.removeItem('cafeCart');
        }
    }

    // Initialiser le panier coulissant s'il existe sur la page
    const cartPanel = document.getElementById('cart-panel');
    if (cartPanel) {
        initCartPanel();
    }
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', initCommon);
