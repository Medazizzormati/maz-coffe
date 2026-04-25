<?php session_start(); ?>
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
    <link rel="stylesheet" href="css/styles.css?v=2.0">
</head>

<body>
    <!-- Header -->
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
                        <li><a href="logout.php" class="nav-auth-btn" title="Déconnexion"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    <?php else: ?>
                        <li>
                            <div class="navbar-pill-toggle">
                                <a href="auth.php?mode=login" class="<?php echo (!isset($_GET['mode']) || $_GET['mode'] == 'login') ? 'active' : ''; ?>">Connexion</a>
                                <a href="auth.php?mode=signup" class="<?php echo (isset($_GET['mode']) && $_GET['mode'] == 'signup') ? 'active' : ''; ?>">Inscription</a>
                            </div>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ICÔNE PANIER AJOUTÉE -->
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
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51737.56157077884!2d10.584347573618395!3d35.83424195191599!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x130275759ac9d16d%3A0x6790906dc266e7!2sSousse!5e0!3m2!1sfr!2stn!4v1712060000000!5m2!1sfr!2stn" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
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
    <?php include 'footer.php'; ?>

    <script src="js/shared.js"></script>
    <script src="js/contact.js"></script>
</body>
</html>

