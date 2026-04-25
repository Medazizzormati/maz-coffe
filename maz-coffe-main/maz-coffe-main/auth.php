<?php
// auth.php
include 'auth_logic.php';

$error = '';
$success = '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login'; // 'login' or 'signup'

// Handle Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = loginUser($email, $password);
    if ($role) {
        if ($role === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Identifiants invalides ou compte inexistant.";
        $mode = 'login';
    }
}

// Handle Signup
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
        $mode = 'signup';
    } else {
        if (registerUser($username, $email, $password)) {
            $success = "Compte créé ! Vous pouvez maintenant vous connecter.";
            $mode = 'login';
        } else {
            $error = "Erreur : L'email est peut-être déjà utilisé.";
            $mode = 'signup';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - M.A.Z Coffee House</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css?v=2.0">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D4A76A;
        }
        .auth-container {
            max-width: 450px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        /* Toggle Switch Styling */
        .auth-toggle {
            display: flex;
            background: #f0f0f0;
            border-radius: 50px;
            margin-bottom: 30px;
            position: relative;
            padding: 5px;
        }
        .auth-toggle button {
            flex: 1;
            padding: 12px 0;
            border: none;
            background: none;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            z-index: 1;
            transition: color 0.3s;
            color: #666;
        }
        .auth-toggle .slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 5px);
            height: calc(100% - 10px);
            background: var(--primary-color);
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 4px 10px rgba(139, 69, 19, 0.3);
        }
        .auth-toggle button.active {
            color: #fff;
        }
        
        /* Form Visibility Transitions */
        #signup-fields {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.4s ease-in-out;
        }
        .signup-mode #signup-fields {
            max-height: 400px;
            opacity: 1;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 8px rgba(212, 167, 106, 0.2);
        }
        .btn-auth {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-auth:hover {
            background-color: #D2691E;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        .error-msg { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }
        .success-msg { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }
        
        .auth-header-text { text-align: center; margin-bottom: 20px; }
        .auth-header-text h2 { color: var(--primary-color); font-size: 1.8rem; margin: 0; }
    </style>
</head>
<body class="<?php echo $mode === 'signup' ? 'signup-mode' : ''; ?>">
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="images/img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House"></a>
                <h1>M.A.Z Coffee House</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Accueil</a></li>
                    <li><a href="menu.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
                    <li><a href="contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                    <li>
                        <div class="navbar-pill-toggle">
                            <a href="auth.php?mode=login" class="<?php echo ($mode == 'login' || !$mode) ? 'active' : ''; ?>">Connexion</a>
                            <a href="auth.php?mode=signup" class="<?php echo ($mode == 'signup') ? 'active' : ''; ?>">Inscription</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="auth-container">
            <div class="auth-header-text">
                <h2 id="form-title"><?php echo $mode === 'signup' ? 'Créer un compte' : 'Se connecter'; ?></h2>
            </div>

            <div class="auth-toggle">
                <div class="slider" style="<?php echo $mode === 'signup' ? 'left: calc(50% + 0px);' : 'left: 5px;'; ?>"></div>
                <button type="button" id="btn-login" class="<?php echo $mode === 'login' ? 'active' : ''; ?>" onclick="setMode('login')">Connexion</button>
                <button type="button" id="btn-signup" class="<?php echo $mode === 'signup' ? 'active' : ''; ?>" onclick="setMode('signup')">Inscription</button>
            </div>
            
            <?php if($success): ?>
                <div class="success-msg"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($msg): ?>
                <div class="success-msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="auth.php" method="POST" id="auth-form">
                <!-- Signup Only Fields -->
                <div id="signup-fields">
                    <div class="form-group">
                        <label for="username">Nom complet</label>
                        <input type="text" name="username" id="username" placeholder="Votre nom" <?php echo $mode === 'signup' ? 'required' : ''; ?>>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" name="email" id="email" required placeholder="ex: cafe@example.com" autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required placeholder="********" autocomplete="current-password">
                </div>

                <!-- Signup Only Confidence Field -->
                <div id="confirm-field" style="<?php echo $mode === 'login' ? 'display: none;' : ''; ?>">
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="********" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" name="<?php echo $mode === 'signup' ? 'signup' : 'login'; ?>" id="submit-btn" class="btn-auth">
                    <?php echo $mode === 'signup' ? "S'inscrire" : "Connexion"; ?>
                </button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        function setMode(mode) {
            const body = document.body;
            const slider = document.querySelector('.slider');
            const title = document.getElementById('form-title');
            const submitBtn = document.getElementById('submit-btn');
            const signupFields = document.getElementById('signup-fields');
            const confirmField = document.getElementById('confirm-field');
            const btnLogin = document.getElementById('btn-login');
            const btnSignup = document.getElementById('btn-signup');
            const usernameInput = document.getElementById('username');
            const confirmInput = document.getElementById('confirm_password');

            if (mode === 'signup') {
                body.classList.add('signup-mode');
                slider.style.left = 'calc(50% + 0px)';
                title.innerText = 'Créer un compte';
                submitBtn.innerText = "S'inscrire";
                submitBtn.name = 'signup';
                confirmField.style.display = 'block';
                usernameInput.required = true;
                btnLogin.classList.remove('active');
                btnSignup.classList.add('active');
            } else {
                body.classList.remove('signup-mode');
                slider.style.left = '5px';
                title.innerText = 'Se connecter';
                submitBtn.innerText = 'Connexion';
                submitBtn.name = 'login';
                confirmField.style.display = 'none';
                usernameInput.required = false;
                btnLogin.classList.add('active');
                btnSignup.classList.remove('active');
            }
        }
    </script>
</body>
</html>


