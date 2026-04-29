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
            $upload_path = '../images/' . $new_filename;

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

// Fetch user orders
$orders_res = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id OR user_email='{$user_data['email']}' ORDER BY created_at DESC");
$orders = [];
while ($row = mysqli_fetch_assoc($orders_res)) {
    $orders[] = $row;
}

// Fetch user messages
$messages_res = mysqli_query($conn, "SELECT * FROM contact_messages WHERE user_id=$user_id OR email='{$user_data['email']}' ORDER BY created_at DESC");
$messages = [];
while ($row = mysqli_fetch_assoc($messages_res)) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - M.A.Z Coffee House</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css?v=3.1">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D4A76A;
        }
        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: var(--card-bg);
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
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-color); }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--input-bg);
            color: var(--text-color);
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

        /* Order History Styles */
        .orders-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .orders-section h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.4rem;
        }
        .orders-table-wrapper {
            overflow-x: auto;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .orders-table th, .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .orders-table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: #555;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-completed, .status-success { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-error, .status-failed { background: #f8d7da; color: #721c24; }
        
        .no-orders {
            text-align: center;
            padding: 20px;
            color: #888;
            font-style: italic;
        }

        /* Message History Styles */
        .messages-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .message-card {
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .message-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: #888;
        }
        .message-body {
            font-size: 1rem;
            color: #444;
            margin-bottom: 15px;
            white-space: pre-wrap;
        }
        .admin-reply-box {
            background: #f0f7ff;
            border-left: 4px solid #4a90e2;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .admin-reply-box strong {
            display: block;
            font-size: 0.8rem;
            color: #4a90e2;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .reply-content {
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="../images/img/M.A.Z.png" id="photo" alt="Logo"></a>
                <h1>M.A.Z Coffee House</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li id="theme-toggle" style="margin-left: 15px; cursor: pointer;">
                        <i class="fas fa-moon" style="font-size: 1.3rem; color: var(--primary-color);"></i>
                    </li>
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
                    <img id="image_preview" src="../images/<?php echo htmlspecialchars($user_data['profile_image'] ?? 'default_avatar.jpg'); ?>" alt="Profil Image" onerror="this.src='../images/default_avatar.jpg';">
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

            <!-- Order History Section -->
            <div class="orders-section">
                <h3><i class="fas fa-history"></i> Mon Historique de Commandes</h3>
                <?php if (empty($orders)): ?>
                    <div class="no-orders">
                        <i class="fas fa-info-circle"></i> Vous n'avez pas encore passé de commande.
                    </div>
                <?php else: ?>
                    <div class="orders-table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td><small><?php echo htmlspecialchars($order['payment_ref']); ?></small></td>
                                        <td><strong><?php echo number_format($order['amount'], 2); ?> DT</strong></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Messages Section -->
            <div class="messages-section">
                <h3><i class="fas fa-envelope"></i> Mes Messages & Réponses</h3>
                <?php if (empty($messages)): ?>
                    <div class="no-orders">
                        <i class="fas fa-info-circle"></i> Vous n'avez pas encore envoyé de message.
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <div class="message-card">
                            <div class="message-header">
                                <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y H:i', strtotime($m['created_at'])); ?></span>
                                <?php if ($m['admin_reply']): ?>
                                    <span style="color: #4a90e2; font-weight: 700;"><i class="fas fa-check-circle"></i> Répondu</span>
                                <?php else: ?>
                                    <span style="color: #D4A76A; font-weight: 700;"><i class="fas fa-clock"></i> En attente</span>
                                <?php endif; ?>
                            </div>
                            <div class="message-body"><?php echo htmlspecialchars($m['message']); ?></div>
                            
                            <?php if ($m['admin_reply']): ?>
                                <div class="admin-reply-box">
                                    <strong>Réponse de l'administrateur :</strong>
                                    <div class="reply-content"><?php echo htmlspecialchars($m['admin_reply']); ?></div>
                                    <div style="font-size: 0.75rem; color: #999; margin-top: 10px;">Le <?php echo date('d/m/Y H:i', strtotime($m['replied_at'])); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="../js/theme.js"></script>
    <script src="../js/shared.js"></script>
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



