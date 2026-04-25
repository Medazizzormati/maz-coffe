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

// Handle Delete Message
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $success = mysqli_query($conn, "DELETE FROM contact_messages WHERE id=$id");
    
    // If AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        // Fetch new count for live update
        $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages");
        $new_count = mysqli_fetch_assoc($res)['count'];
        echo json_encode(['status' => 'success', 'message' => 'Message supprimé !', 'new_count' => $new_count]);
        exit;
    }
    
    header("Location: admin_messages.php?msg=" . urlencode("Message supprimé !"));
    exit;
}

// Fetch dashboard statistics
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$total_products = mysqli_fetch_assoc($res)['count'];

$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages");
$total_messages = mysqli_fetch_assoc($res)['count'];

// Fetch all messages
$messages = [];
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Clients - M.A.Z Coffee House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css?v=3.1">
    <style>
        /* Specific Message UI Styles */
        .msg-drawer-content {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #333;
            background: #fdfdfd;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #D4A76A;
            margin-top: 20px;
            white-space: pre-wrap; /* Keeps line breaks from textarea */
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
        }
        .msg-info-group {
            margin-bottom: 20px;
        }
        .msg-info-group label {
            color: #999;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 5px;
        }
        .msg-info-group .data {
            color: #8B4513;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .view-btn {
            background: #eef5ff;
            color: #4a90e2;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        .view-btn:hover {
            background: #4a90e2;
            color: #fff;
        }
    </style>
</head>
<body>

    <!-- DASHBOARD VIEW -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="images/img/M.A.Z.png" id="photo" alt="Logo"></a>
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
                    <img src="images/<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'default_avatar.jpg'); ?>" alt="Admin Profile" onerror="this.src='images/default_avatar.jpg';">
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
                <a href="admin_messages.php" class="admin-nav-item active">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                <a href="admin_users.php" class="admin-nav-item">
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
                </div>

                <div class="admin-main-card">
                    <div class="admin-header-row">
                        <h2>Boîte de Réception</h2>
                    </div>

                    <?php if(count($messages) > 0): ?>
                    <div class="premium-table-wrapper">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($messages as $m): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #333;"><?php echo htmlspecialchars($m['name']); ?></td>
                                    <td style="color: #666;"><?php echo htmlspecialchars($m['email']); ?></td>
                                    <td style="color: #999; font-size: 0.9rem;">
                                        <?php echo date('d M Y - H:i', strtotime($m['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="view-btn" onclick='openMessageDrawer(<?php echo json_encode($m); ?>)'>
                                                <i class="fas fa-eye"></i> Voir message
                                            </button>
                                            <a href="admin_messages.php?delete_id=<?php echo $m['id']; ?>" class="action-btn delete-btn" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px 0; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; color: #eee;"></i>
                            <p style="font-size: 1.1rem;">Aucun message reçu pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- MESSAGE DRAWER OVERLAY -->
    <div class="admin-form-overlay" id="msgOverlay" onclick="closeDrawer()"></div>
    <div class="admin-drawer" id="msgDrawer">
        <div class="drawer-header">
            <h3>Détails du Message</h3>
            <span class="close-drawer" onclick="closeDrawer()">&times;</span>
        </div>
        
        <!-- Viewer Content -->
        <div class="drawer-body">
            <div class="msg-info-group">
                <label>Nom de l'expéditeur</label>
                <div class="data" id="v_name">...</div>
            </div>
            
            <div class="msg-info-group">
                <label>Adresse E-mail</label>
                <div class="data" id="v_email">...</div>
            </div>
            
            <div class="msg-info-group">
                <label>Date de réception</label>
                <div class="data" id="v_date" style="color: #666; font-size: 0.95rem;">...</div>
            </div>

            <div class="msg-info-group" style="margin-top: 30px;">
                <label>Message</label>
                <div class="msg-drawer-content" id="v_content">...</div>
            </div>
            
            <div style="margin-top: 40px; text-align: right;">
                <a href="#" id="v_mailto" class="btn-admin-add" style="text-decoration: none; display: inline-block;">
                    <i class="fas fa-reply"></i> Répondre par Email
                </a>
            </div>
        </div>
    </div>

    <script>
        function openMessageDrawer(data) {
            const drawer = document.getElementById('msgDrawer');
            const overlay = document.getElementById('msgOverlay');
            
            // Populating the UI with the JSON Data
            document.getElementById('v_name').innerText = data.name;
            document.getElementById('v_email').innerText = data.email;
            
            // Format Data gently
            const dateObj = new Date(data.created_at);
            document.getElementById('v_date').innerText = dateObj.toLocaleString('fr-FR');
            
            document.getElementById('v_content').innerText = data.message; // Preserves newlines thanks to white-space: pre-wrap
            
            // Setup Reply button
            document.getElementById('v_mailto').href = 'mailto:' + data.email + '?subject=Re: Contact M.A.Z Coffee House';

            // Show drawer
            drawer.classList.add('open');
            overlay.classList.add('active');
            document.body.classList.add('admin-overlay-active');
        }

        function closeDrawer() {
            document.getElementById('msgDrawer').classList.remove('open');
            document.getElementById('msgOverlay').classList.remove('active');
            document.body.classList.remove('admin-overlay-active');
        }
    </script>
    <script src="js/shared.js"></script>
    <script src="js/admin.js?v=1.1"></script>
</body>
</html>


