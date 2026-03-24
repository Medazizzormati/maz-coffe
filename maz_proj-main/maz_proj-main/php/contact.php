<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M.A.Z Coffee House - Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
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
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
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

    <!-- Contact Page Content -->
    <main>
        <section class="contact-section">
            <div class="container">
                <h2>Contactez-nous</h2>
                <p class="contact-description">Nous serions ravis de répondre à vos questions ou de recevoir vos
                    commentaires.</p>

                <div class="contact-container">
                    <!-- Formulaire de contact -->
                    <div class="contact-form">
                        <form id="contact-form">
                            <div class="form-group">
                                <label for="name">Nom *</label>
                                <input type="text" id="name" name="name" required>
                                <div class="error-message" id="name-error">Veuillez entrer votre nom</div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                                <div class="error-message" id="email-error">Veuillez entrer une adresse email valide
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" required></textarea>
                                <div class="error-message" id="message-error">Veuillez entrer un message</div>
                            </div>

                            <button type="submit" class="submit-btn">Envoyer le message</button>

                            <div class="success-message" id="success-message">
                                Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs
                                délais.
                            </div>
                        </form>
                    </div>

                    <!-- Carte et informations -->
                    <div class="map-container">
                        <h3>Où nous trouver</h3>
                        <div id="map">
                            <!-- Carte Google Maps simulée -->
                            <div style="text-align: center;">
                                <i class="fas fa-map-marked-alt"
                                    style="font-size: 3rem; margin-bottom: 15px; color: #8B4513;"></i>
                                <p>Carte interactive</p>
                                <p>123 Rue du Café, 75000 Sousse</p>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>Informations de contact</h4>
                            <ul>
                                <li><i class="fas fa-map-marker-alt"></i> 123 Rue du Café, 75000 Sousse</li>
                                <li><i class="fas fa-phone"></i> 01 23 45 67 89</li>
                                <li><i class="fas fa-envelope"></i> contact@mazcoffee.com</li>
                                <li><i class="fas fa-clock"></i> Lun-Ven: 7h30-20h, Sam: 8h-21h, Dim: 9h-18h</li>
                            </ul>
                        </div>
                    </div>
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

    <script src="../js/shared.js"></script>
    <script src="../js/contact.js"></script>
</body>
</html>
