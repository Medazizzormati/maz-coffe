<?php
session_start();
include 'db.php';
require_once 'auth_check.php';
require_once 'auth_logic.php';

// Ensure user is admin
if (!isAdminLoggedIn()) {
    header("Location: admin.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Prevent deleting ANY admin account, including your own
    $check_role = mysqli_query($conn, "SELECT role FROM users WHERE id=$id");
    $user_target = mysqli_fetch_assoc($check_role);

    if ($user_target && $user_target['role'] === 'admin') {
        header("Location: admin_users.php?msg=" . urlencode("Action impossible : Les comptes Administrateurs sont protégés !"));
        exit;
    }

    $success = mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    
    header("Location: admin_users.php?msg=" . urlencode("Utilisateur supprimé !"));
    exit;
}

// Fetch dashboard statistics
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$total_products = mysqli_fetch_assoc($res)['count'];

$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages");
$total_messages = mysqli_fetch_assoc($res)['count'];

$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$total_users = mysqli_fetch_assoc($res)['count'];

// Fetch all users
$users_list = [];
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs - M.A.Z Coffee House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css?v=3.1">
</head>
<body>

    <!-- DASHBOARD VIEW -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="../images/img/M.A.Z.png" id="photo" alt="Logo"></a>
                <h1>Administration</h1>
            </div>
            <nav class="navbar">
                <div class="navbar-pill-toggle">
                    <a href="index.php" style="padding: 8px 20px !important;">Aller au Site</a>
                    <a href="logout.php" class="active" style="background-color: #e74c3c !important; padding: 8px 15px !important;"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-dashboard-container">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="admin-profile-block">
                    <img src="../images/<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'default_avatar.jpg'); ?>" alt="Admin Profile" onerror="this.src='../images/default_avatar.jpg';">
                    <div class="admin-profile-info">
                        <strong>
                            <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                            <a href="profile.php" style="color: #4a90e2; font-size: 0.8em; margin-left: 6px; text-decoration: none;" title="Modifier le profil"><i class="fas fa-edit"></i></a>
                        </strong>
                        <span>Administrateur</span>
                    </div>
                </div>
                <hr style="border: none; border-top: 1px solid #eee; margin: 15px 0;">
                <a href="admin.php" class="admin-nav-item">
                    <i class="fas fa-box"></i> Produits
                </a>
                <a href="admin_messages.php" class="admin-nav-item">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                <a href="admin_users.php" class="admin-nav-item active">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-external-link-alt"></i> Voir le Site
                </a>
                <hr style="border: none; border-top: 1px solid #eee; margin: 15px 0;">
                <a href="logout.php" class="admin-nav-item" style="color: #e74c3c;">
                    <i class="fas fa-power-off"></i> Déconnexion
                </a>
            </aside>

            <!-- Main Content -->
            <section class="admin-content">
                <?php if(isset($_GET['msg'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 25px; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="label">Produits</span>
                        <span class="value" id="stat-products"><?php echo $total_products; ?></span>
                    </div>
                    <div class="stat-card" style="border-left-color: #4a90e2;">
                        <span class="label">Messages Reçus</span>
                        <span class="value" id="stat-messages"><?php echo $total_messages; ?></span>
                    </div>
                    <div class="stat-card" style="border-left-color: #D4A76A;">
                        <span class="label">Utilisateurs</span>
                        <span class="value" id="stat-users"><?php echo $total_users; ?></span>
                    </div>
                </div>

                <div class="admin-main-card">
                    <div class="admin-header-row">
                        <h2>Gestion des Utilisateurs</h2>
                    </div>

                    <?php if(count($users_list) > 0): ?>
                    <div class="premium-table-wrapper">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Nom d'utilisateur</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Date de Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users_list as $u): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #333;"><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td style="color: #666;"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <?php if($u['role'] === 'admin'): ?>
                                            <span style="background:#fff3cd; color:#856404; padding: 3px 8px; border-radius: 20px; font-size:0.8rem; font-weight: bold;">Admin</span>
                                        <?php else: ?>
                                            <span style="background:#eef5ff; color:#4a90e2; padding: 3px 8px; border-radius: 20px; font-size:0.8rem; font-weight: bold;">Utilisateur</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #999; font-size: 0.9rem;">
                                        <?php echo date('d M Y - H:i', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <?php if($u['role'] !== 'admin'): ?>
                                                <a href="admin_users.php?delete_id=<?php echo $u['id']; ?>" class="action-btn delete-btn" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #ccc; font-size: 0.9em; font-style: italic;"><i class="fas fa-shield-alt"></i> Protégé</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px 0; color: #999;">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px; color: #eee;"></i>
                            <p style="font-size: 1.1rem;">Aucun utilisateur trouvé.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <script src="../js/shared.js"></script>
    <script src="../js/admin.js?v=1.1"></script>
</body>
</html>



