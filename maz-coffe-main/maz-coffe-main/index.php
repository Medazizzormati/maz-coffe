<?php
session_start();
// index.php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Café Local - Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css?v=2.0">
    <link rel="stylesheet" href="css/filter-styles.css">
    <style>
        .dynamic-products-section {
            padding: 40px 0;
            background-color: #f9f9f9;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <!-- Header avec icône panier -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House">
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
        <!-- Bannière principale avec slideshow -->
        <section class="banner">
            <div class="slideshow-container">
                <div class="slide active">
                    <div class="slide-content">
                        <h2>Venez savourer un moment de tranquillité</h2>
                        <p>Un café d'exception dans un cadre chaleureux</p>
                    </div>
                </div>
                <!-- SLIDE 2 -->
                <div class="slide">
                    <div class="slide-content">
                        <h2>Des arômes qui éveillent les sens</h2>
                        <p>Dégustez notre sélection de cafés torréfiés sur place</p>
                    </div>
                </div>
                <!-- SLIDE 3 -->
                <div class="slide">
                    <div class="slide-content">
                        <h2>Une expérience gourmande Inoubliable</h2>
                        <p>Accompagnez votre boisson de nos pâtisseries faites maison</p>
                    </div>
                </div>
                
                <!-- BOUTONS DE NAVIGATION DU SLIDER -->
                <div class="dots-container">
                    <span class="dot active" data-index="0"></span>
                    <span class="dot" data-index="1"></span>
                    <span class="dot" data-index="2"></span>
                </div>
            </div>
        </section>

        <!-- Présentation du café -->
        <section class="presentation" id="presentation">
            <div class="container">
                <div class="presentation-content">
                    <div class="presentation-text">
                        <h2>Bienvenue au Café Local</h2>
                        <p>Fondé en 2020, notre café est bien plus qu'un simple endroit pour boire un café. C'est un
                            lieu de rencontre, de détente et de partage. Nous sélectionnons soigneusement nos grains
                            de café auprès de producteurs éthiques et préparons chaque boisson avec passion.</p>
                        <p>Notre espace a été conçu pour offrir une atmosphère chaleureuse et conviviale, idéale
                            pour travailler, lire ou simplement discuter entre amis.</p>
                    </div>
                    <div class="presentation-image">
                        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                            alt="Intérieur du café">
                    </div>
                </div>
            </div>
        </section>

        <!-- Nos valeurs -->
        <section class="values">
            <div class="container">
                <h2>Nos valeurs</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-seedling"></i>
                        <h3>Produits locaux</h3>
                        <p>Nous travaillons avec des producteurs locaux pour vous offrir des produits frais et de saison.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h3>Service amical</h3>
                        <p>Notre équipe est toujours souriante et à l'écoute pour vous offrir la meilleure expérience.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-users"></i>
                        <h3>Ambiance conviviale</h3>
                        <p>Notre espace est conçu pour être accueillant et chaleureux, un vrai lieu de vie du quartier.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section commande/CTA -->
        <section class="order-section">
            <div class="container">
                <div class="order-content">
                    <h2>Découvrez nos délices</h2>
                    <p>Explorez notre menu varié de boissons chaudes, pâtisseries maison et sandwiches gourmands.</p>
                </div>
            </div>
        </section>



    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- PANNEAU PANIER COULISSANT -->
    <?php include 'cart_panel.php'; ?>

    <!-- Scripts -->
    <script src="js/shared.js"></script>
    <script src="js/index.js"></script>
</body>
</html>


