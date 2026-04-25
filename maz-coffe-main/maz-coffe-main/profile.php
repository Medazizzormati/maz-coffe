<?php
session_start();
include 'db.php';
require_once 'auth_check.php';
require_once 'auth_logic.php';

// Ensure user is strictly logged in
confirmLoggedIn();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    
    // Default image if they don't upload a new one
    $profile_image = $_SESSION['profile_image'] ?? 'default_avatar.jpg';

    // Image Upload Handling
    if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_image_file']['type'];
        $file_size = $_FILES['profile_image_file']['size'];

        if (in_array($file_type, $allowed_types) && $file_size < 5 * 1024 * 1024) {
            $ext = pathinfo($_FILES['profile_image_file']['name'], PATHINFO_EXTENSION);
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = 'images/' . $new_filename;

            if (move_uploaded_file($_FILES['profile_image_file']['tmp_name'], $upload_path)) {
                $profile_image = $new_filename;
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $error = "Fichier non supporté ou trop volumineux (Max 5Mo, PNG/JPG/WebP/GIF seulement).";
        }
    }

    if (empty($error)) {
        // Validation: Unique Email Check (Excluding current user)
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $user_id");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Cette adresse e-mail est déjà utilisée par un autre compte.";
        } else {
            // Update Database
            $query = "UPDATE users SET username='$username', email='$email', profile_image='$profile_image' WHERE id=$user_id";
            if (mysqli_query($conn, $query)) {
                $success = "Vos informations ont été mises à jour avec succès !";
                
                // Update Session variables instantly
                $_SESSION['username'] = $username;
                $_SESSION['profile_image'] = $profile_image;
                
                // Re-fetch to be completely sure everything is clean for rendering below
            } else {
                $error = "Erreur de base de données : " . mysqli_error($conn);
            }
        }
    }
}

// Fetch current user details directly from DB for the form
$res = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - M.A.Z Coffee House</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css?v=3.1">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D4A76A;
        }
        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .profile-header-text { text-align: center; margin-bottom: 30px; }
        .profile-header-text h2 { color: var(--primary-color); font-size: 1.8rem; margin: 0; }
        
        .profile-pic-preview-container {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            position: relative;
        }
        .profile-pic-preview-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--secondary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .file-upload-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
        .file-upload-wrapper input[type="file"] {
            display: none;
        }
        .file-upload-label {
            background-color: #f1f1f1;
            color: #555;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }
        .file-upload-label:hover {
            background-color: #e2e2e2;
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
        .form-group input:focus { outline: none; border-color: var(--secondary-color); }
        
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
        .btn-auth:hover { background-color: #D2691E; }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #888;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .btn-back:hover { color: var(--primary-color); }
        .error-msg { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success-msg { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="images/img/M.A.Z.png" id="photo" alt="Logo"></a>
                <h1>M.A.Z Coffee House</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin.php">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-auth-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="profile-container">
            <div class="profile-header-text">
                <h2>Modifier Mon Profil</h2>
            </div>

            <?php if($success): ?>
                <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="profile-pic-preview-container">
                    <img id="image_preview" src="images/<?php echo htmlspecialchars($user_data['profile_image'] ?? 'default_avatar.jpg'); ?>" alt="Profil Image" onerror="this.src='images/default_avatar.jpg';">
                </div>
                
                <div class="file-upload-wrapper">
                    <label for="profile_image_file" class="file-upload-label">
                        <i class="fas fa-camera"></i> Changer la photo
                    </label>
                    <input type="file" id="profile_image_file" name="profile_image_file" accept=".jpg, .jpeg, .png, .gif, .webp" onchange="previewImage(this)">
                </div>

                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Adresse E-mail</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <button type="submit" name="update_profile" class="btn-auth">Mettre à jour</button>
                
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à l'administration</a>
                <?php else: ?>
                    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
                <?php endif; ?>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image_preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>


