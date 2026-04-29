<?php
session_start();
include 'db.php';
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$db_products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['price'] = (float) $row['price'];
    $row['id'] = (int) $row['id'];
    $row['popularity'] = 50; 
    $row['image'] = empty($row['image']) ? 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400' : (strpos($row['image'], 'http') === 0 ? $row['image'] : '../images/' . $row['image']);
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
    <link rel="stylesheet" href="../css/styles.css?v=2.0" />
    <link rel="stylesheet" href="../css/filter-styles.css" />
  </head>

  <body>
    <!-- Header -->
    <header class="header">
      <div class="container">
        <div class="logo">
          <a href="index.php">
                <img src="../images/img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House">
            </a>
          <h1>M.A.Z Coffee House</h1>
        </div>
        <nav class="navbar">
          <ul class="nav-links">
            <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Accueil</a></li>
            <li><a href="menu.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
            <li><a href="contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="profile.php" class="nav-auth-btn" style="background:#D4A76A; margin-right: 10px;" title="Mon Profil"><i class="fas fa-user-edit"></i> Mon Profil</a></li>
                <li><a href="logout.php" class="nav-auth-btn" title="Déconnexion" style="padding: 10px 15px;"><i class="fas fa-sign-out-alt"></i></a></li>
            <?php else: ?>
                <li>
                    <div class="navbar-pill-toggle">
                        <a href="auth.php?mode=login" class="<?php echo (!isset($_GET['mode']) || $_GET['mode'] == 'login') ? 'active' : ''; ?>">Connexion</a>
                        <a href="auth.php?mode=signup" class="<?php echo (isset($_GET['mode']) && $_GET['mode'] == 'signup') ? 'active' : ''; ?>">Inscription</a>
                    </div>
                </li>
            <?php endif; ?>
            
            <li id="theme-toggle" style="margin-left: 15px; cursor: pointer;">
              <i class="fas fa-moon" style="font-size: 1.3rem; color: var(--primary-color);"></i>
            </li>
            <li class="cart-icon-container" id="cart-nav" style="margin-left: 15px;">
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
                    <span id="min-price-display">0DT</span>
                    <span>-</span>
                    <span id="max-price-display">10DT</span>
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
    <?php include 'footer.php'; ?>

    <!-- PANNEAU PANIER COULISSANT -->
    <?php include 'cart_panel.php'; ?>

    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
    <script src="../js/theme.js"></script>
    <script src="../js/shared.js"></script>
    <script>
        // Override the static products with the database products
        products = <?php echo json_encode($db_products); ?>;
    </script>
    <script src="../js/menu.js"></script>
  </body>
</html>
