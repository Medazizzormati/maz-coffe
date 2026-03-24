<?php
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
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/filter-styles.css">
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
                    <img src="../img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House">
                </a>
                <h1>M.A.Z Coffee House</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Accueil</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <!-- Ajout du lien vers Admin -->
                    <li><a href="admin.php">Admin</a></li>
                    
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
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>M.A.Z Coffee House</h3>
                    <p>Un lieu chaleureux pour déguster des boissons et pâtisseries de qualité.</p>
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
                        <li><i class="fas fa-map-marker-alt"></i> 123 Rue du Café, 75000 Sousse</li>
                        <li><i class="fas fa-phone"></i> 01 23 45 67 89</li>
                        <li><i class="fas fa-envelope"></i> contact@mazcoffee.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 M.A.Z Coffee House. Tous droits réservés.</p>
                <p>Projet académique - Polytech Sousse 2025-2026</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../js/shared.js"></script>
    <script src="../js/index.js"></script>
</body>
</html>
