<?php
include 'db.php';
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$db_products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['price'] = (float) $row['price'];
    $row['id'] = (int) $row['id'];
    $row['popularity'] = 50; 
    $row['image'] = empty($row['image']) ? 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400' : '../images/' . $row['image'];
    $db_products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>M.A.Z Coffee House - Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/filter-styles.css" />
  </head>

  <body>
    <!-- Header -->
    <header class="header">
      <div class="container">
        <div class="logo">
          <a href="index.php">
                <img src="../img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House">
            </a>
          <h1>M.A.Z Coffee House</h1>
        </div>
        <nav class="navbar">
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="admin.php">Admin</a></li>
            <!-- ICÔNE PANIER AJOUTÉE -->
            <li class="cart-icon-container" id="cart-nav">
              <i class="fas fa-shopping-basket cart-icon"></i>
              <span class="cart-badge hidden" id="cart-count">0</span>
            </li>
          </ul>
          <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <main id="main-content">
      <!-- Page menu -->
      <section class="menu-section">
        <div class="container">
          <h2>Notre Menu</h2>
          <p class="menu-description">
            Découvrez notre sélection de boissons et gourmandises préparées
            avec soin.
          </p>

          <!-- Layout principal avec produits et filtres -->
          <div class="menu-layout">
            <!-- Conteneur des produits à gauche -->
            <div class="products-section">
              <div class="products-header">
                <h3 id="results-count">Tous les produits</h3>
              </div>
              <div class="products-container" id="products-container">
                <!-- Les produits seront générés par JavaScript -->
              </div>
            </div>

            <!-- Panneau de filtrage à droite -->
            <aside class="filter-sidebar">
              <div class="filter-header">
                <h3><i class="fas fa-filter"></i> Filtres</h3>
                <button class="reset-filters-btn" id="reset-filters">
                  <i class="fas fa-redo"></i> Réinitialiser
                </button>
              </div>

              <!-- Filtre par catégorie -->
              <div class="filter-section">
                <h4>Catégories</h4>
                <div class="category-filters">
                  <div
                    class="category-filter-item active"
                    data-category="all"
                  >
                    <i class="fas fa-th"></i>
                    <span>Tout</span>
                    <span class="category-count" id="count-all">12</span>
                  </div>
                  <div
                    class="category-filter-item"
                    data-category="boissons-chaudes"
                  >
                    <i class="fas fa-mug-hot"></i>
                    <span>Boissons chaudes</span>
                    <span class="category-count" id="count-boissons-chaudes"
                      >4</span
                    >
                  </div>
                  <div
                    class="category-filter-item"
                    data-category="boissons-froides"
                  >
                    <i class="fas fa-glass-whiskey"></i>
                    <span>Boissons froides</span>
                    <span class="category-count" id="count-boissons-froides"
                      >2</span
                    >
                  </div>
                  <div
                    class="category-filter-item"
                    data-category="patisseries"
                  >
                    <i class="fas fa-birthday-cake"></i>
                    <span>Pâtisseries</span>
                    <span class="category-count" id="count-patisseries"
                      >3</span
                    >
                  </div>
                  <div
                    class="category-filter-item"
                    data-category="sandwiches"
                  >
                    <i class="fas fa-hamburger"></i>
                    <span>Sandwiches</span>
                    <span class="category-count" id="count-sandwiches"
                      >3</span
                    >
                  </div>
                </div>
              </div>

              <!-- Filtre par prix -->
              <div class="filter-section">
                <h4>Prix</h4>
                <div class="price-filter">
                  <div class="price-range-display">
                    <span id="min-price-display">0€</span>
                    <span>-</span>
                    <span id="max-price-display">10€</span>
                  </div>
                  <div class="price-inputs">
                    <input
                      type="range"
                      id="min-price"
                      min="0"
                      max="10"
                      step="0.5"
                      value="0"
                    />
                    <input
                      type="range"
                      id="max-price"
                      min="0"
                      max="10"
                      step="0.5"
                      value="10"
                    />
                  </div>
                </div>
              </div>

              <!-- Tri -->
              <div class="filter-section">
                <h4>Trier par</h4>
                <select id="sort-select" class="sort-select">
                  <option value="default">Par défaut</option>
                  <option value="price-asc">Prix croissant</option>
                  <option value="price-desc">Prix décroissant</option>
                  <option value="popularity">Popularité</option>
                </select>
              </div>
            </aside>
          </div>

        
      </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-column">
            <h3>M.A.Z Coffee House</h3>
            <p>
              Un lieu chaleureux pour déguster des boissons et pâtisseries de
              qualité.
            </p>
            <div class="social-links">
              <a href="#"><i class="fab fa-facebook"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
          </div>
          <div class="footer-column">
            <h3>Horaires d'ouverture</h3>
            <ul>
              <li>Lundi - Vendredi: 7h30 - 20h</li>
              <li>Samedi: 8h - 21h</li>
              <li>Dimanche: 9h - 18h</li>
            </ul>
          </div>
          <div class="footer-column">
            <h3>Contact</h3>
            <ul>
              <li>
                <i class="fas fa-map-marker-alt"></i> 123 Rue du Café, 75000
                sousse
              </li>
              <li><i class="fas fa-phone"></i> 01 23 45 67 89</li>
              <li><i class="fas fa-envelope"></i> contact@MAZ.com</li>
            </ul>
          </div>
        </div>
        <div class="copyright">
          <p>&copy; 2025 M.A.Z Coffee House. Tous droits réservés.</p>
          <p>Projet académique - Polytech Sousse 2025-2026</p>
        </div>
      </div>
    </footer>

    <!-- PANNEAU PANIER COULISSANT POUR LA PAGE MENU -->
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
      </div>

      <div class="cart-footer">
        <div class="cart-total">
          <span>Total:</span>
          <span class="cart-total-amount" id="cart-total-amount">0.00 €</span>
        </div>
        <button class="checkout-button" id="checkout-button">
          <i class="fas fa-credit-card"></i> Commander
        </button>
        <button class="clear-cart-button" id="clear-cart-button">
          <i class="fas fa-trash"></i> Vider le panier
        </button>
      </div>
    </div>

    <script src="../js/shared.js"></script>
    <script>
        // Override the static products with the database products
        products = <?php echo json_encode($db_products); ?>;
    </script>
    <script src="../js/menu.js"></script>
  </body>
</html>
