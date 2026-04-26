<!-- cart_panel.php -->
<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-panel" id="cart-panel">
    <div class="cart-header">
        <h2><i class="fas fa-shopping-basket"></i> Mon Panier</h2>
        <button class="close-cart" id="close-cart">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="cart-body" id="cart-body">
        <!-- Le contenu du panier sera injecté ici par JavaScript -->
        <div class="empty-cart-message">
            <i class="fas fa-shopping-basket"></i>
            <p>Chargement du panier...</p>
        </div>
    </div>

    <div class="cart-footer">
        <div class="cart-total">
            <span>Total:</span>
            <span class="cart-total-amount" id="cart-total-amount">0.00 DT</span>
        </div>

        <button class="checkout-button" id="checkout-button">
            <i class="fas fa-credit-card"></i> Commander (Konnect)
        </button>
        <button class="clear-cart-button" id="clear-cart-button">
            <i class="fas fa-trash"></i> Vider le panier
        </button>
    </div>
</div>

