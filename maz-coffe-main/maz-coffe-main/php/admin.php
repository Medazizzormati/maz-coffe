<?php
session_start();
include_once 'db.php';
require_once 'auth_check.php';
require_once 'auth_logic.php';

// --- LOGIC HANDLERS ---

// 1. Handle Admin Login
if (isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = loginUser($email, $password);
    
    if ($role === 'admin') {
        header("Location: admin.php");
        exit;
    } else {
        $error = "Accès refusé ou identifiants incorrects.";
    }
}

// 2. Handle Add Product
if (isset($_POST['add_product']) && isAdminLoggedIn()) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $imageFile = $_FILES['image']['name'];
    $newImageName = '';

    if ($imageFile) {
        $uploadDir = '../images/';
        $imageExt = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
        $newImageName = time() . '_' . rand(1000, 9999) . '.' . $imageExt;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImageName);
    }

    $query = "INSERT INTO products (name, image, category, description, price) VALUES ('$name', '$newImageName', '$category', '$description', '$price')";
    if (mysqli_query($conn, $query)) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            // Fetch updated total for live update
            $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
            $new_count = mysqli_fetch_assoc($res)['count'];
            
            // Fetch new product list for UI refresh (or return just the new product data)
            $new_id = mysqli_insert_id($conn);
            $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $new_id");
            $new_product = mysqli_fetch_assoc($res);
            
            echo json_encode(['status' => 'success', 'message' => 'Produit ajouté !', 'new_count' => $new_count, 'product' => $new_product]);
            exit;
        }
        header("Location: admin.php?msg=Produit ajouté !");
        exit;
    } else {
        $error = "Erreur: " . mysqli_error($conn);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $error]);
            exit;
        }
    }
}

// 3. Handle Update Product
if (isset($_POST['update_product']) && isAdminLoggedIn()) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $imageFile = $_FILES['image']['name'];
    if ($imageFile) {
        $uploadDir = '../images/';
        $imageExt = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
        $newImageName = time() . '_' . rand(1000, 9999) . '.' . $imageExt;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImageName);
        $query = "UPDATE products SET name='$name', image='$newImageName', category='$category', description='$description', price='$price' WHERE id=$id";
    } else {
        $query = "UPDATE products SET name='$name', category='$category', description='$description', price='$price' WHERE id=$id";
    }

    if (mysqli_query($conn, $query)) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            // Fetch updated product data
            $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
            $updated_product = mysqli_fetch_assoc($res);
            echo json_encode(['status' => 'success', 'message' => 'Produit mis à jour !', 'product' => $updated_product]);
            exit;
        }
        header("Location: admin.php?msg=Produit mis à jour !");
        exit;
    } else {
        $error = "Erreur: " . mysqli_error($conn);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $error]);
            exit;
        }
    }
}

// 4. Handle Delete Product
if (isset($_GET['delete_id']) && isAdminLoggedIn()) {
    $id = intval($_GET['delete_id']);
    // Optional: unlink image
    $res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image'] && file_exists('../images/'.$row['image'])) {
        unlink('../images/'.$row['image']);
    }
    
    $success = mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        // Fetch new totals
        $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
        $new_count = mysqli_fetch_assoc($res)['count'];
        echo json_encode(['status' => 'success', 'message' => 'Produit supprimé !', 'new_count' => $new_count]);
        exit;
    }
    
    header("Location: admin.php?msg=Produit supprimé !");
    exit;
}

// --- FETCH DATA FOR DASHBOARD ---
$total_products = 0;
$total_messages = 0;
$products = [];

if (isAdminLoggedIn()) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
    $total_products = mysqli_fetch_assoc($res)['count'];

    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages");
    $total_messages = mysqli_fetch_assoc($res)['count'];

    $res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        $products[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - M.A.Z Coffee House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css?v=3.1">
</head>
<body class="<?php echo !isAdminLoggedIn() ? 'admin-login-body' : ''; ?>">

    <?php if (!isAdminLoggedIn()): ?>
        <!-- LOGIN VIEW -->
        <div class="admin-login-wrapper">
            <div class="admin-login-card">
                <a href="index.php"><img src="../images/img/M.A.Z.png" width="80" alt="Logo" style="margin-bottom: 20px;"></a>
                <h2>Portail Admin</h2>
                <?php if(isset($error)): ?>
                    <p style="color: #e74c3c; margin-bottom: 20px; font-weight: 600;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="admin.php" method="POST" class="drawer-form">
                    <div class="form-group">
                        <label>Email Administrateur</label>
                        <input type="email" name="email" required placeholder="admin@maz.com">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" required placeholder="*********">
                    </div>
                    <button type="submit" name="admin_login" class="btn-admin-add" style="width: 100%;">Se connecter</button>
                </form>
                <p style="margin-top: 20px;"><a href="index.php" style="color: #888; text-decoration: none;">Retour au site</a></p>
            </div>
        </div>
    <?php else: ?>
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
                    <a href="admin.php" class="admin-nav-item active">
                        <i class="fas fa-box"></i> Produits
                    </a>
                    <a href="admin_messages.php" class="admin-nav-item">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                    <a href="admin_users.php" class="admin-nav-item">
                        <i class="fas fa-users"></i> Utilisateurs
                    </a>
                    <a href="index.php" class="admin-nav-item">
                        <i class="fas fa-external-link-alt"></i> Voir le Site
                    </a>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 15px 0;">
                    <a href="#" id="theme-toggle" class="admin-nav-item" style="cursor: pointer;">
                        <i class="fas fa-moon"></i> Changer de Thème
                    </a>
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
                        <div class="stat-card">
                            <span class="label">Messages</span>
                            <span class="value" id="stat-messages"><?php echo $total_messages; ?></span>
                        </div>
                        <div class="stat-card" style="border-left-color: #4a90e2;">
                            <span class="label">Visiteurs</span>
                            <span class="value">Active</span>
                        </div>
                    </div>

                    <div class="admin-main-card">
                        <div class="admin-header-row">
                            <h2>Gestion des Produits</h2>
                            <button class="btn-admin-add" onclick="openDrawer('add')">
                                <i class="fas fa-plus"></i> Ajouter un Produit
                            </button>
                        </div>

                        <div class="premium-table-wrapper">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Détails</th>
                                        <th>Catégorie</th>
                                        <th>Prix</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo (strpos($p['image'], 'http') === 0) ? $p['image'] : '../images/' . $p['image']; ?>" alt="" class="table-img" onerror="this.src='../images/default_avatar.jpg'">
                                        </td>
                                        <td>
                                            <div class="prod-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                            <div class="desc-toggle" style="font-size: 0.8rem; color: #999; max-width: 250px; cursor: pointer;" title="Cliquez pour afficher/masquer la description" onclick="const s=this.querySelector('.desc-short'),f=this.querySelector('.desc-full'); if(s&&f){ s.style.display=s.style.display==='none'?'inline':'none'; f.style.display=f.style.display==='none'?'inline':'none'; }">
                                                <?php if(strlen($p['description']) > 40): ?>
                                                    <span class="desc-short"><?php echo htmlspecialchars(substr($p['description'], 0, 40)); ?>...</span>
                                                    <span class="desc-full" style="display: none;"><?php echo htmlspecialchars($p['description']); ?></span>
                                                <?php else: ?>
                                                    <span><?php echo htmlspecialchars($p['description']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><span class="prod-cat"><?php echo htmlspecialchars($p['category']); ?></span></td>
                                        <td><span class="prod-price"><?php echo $p['price']; ?>DT</span></td>
                                        <td>
                                            <div class="action-btns">
                                                <button class="action-btn edit-btn" onclick='openDrawer("edit", <?php echo json_encode($p); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="admin.php?delete_id=<?php echo $p['id']; ?>" class="action-btn delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- DRAWER OVERLAY -->
        <div class="admin-form-overlay" id="formOverlay" onclick="closeDrawer()"></div>
        <div class="admin-drawer" id="adminDrawer">
            <div class="drawer-header">
                <h3 id="drawerTitle">Ajouter un Produit</h3>
                <span class="close-drawer" onclick="closeDrawer()">&times;</span>
            </div>
            
            <form action="admin.php" method="POST" enctype="multipart/form-data" class="drawer-form">
                <input type="hidden" name="id" id="form_id">
                
                <div class="form-group">
                    <label>Nom du Produit</label>
                    <input type="text" name="name" id="form_name" required placeholder="Ex: Espresso Double">
                </div>

                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="category" id="form_category" required>
                        <option value="boissons-chaudes">Boissons chaudes</option>
                        <option value="boissons-froides">Boissons froides</option>
                        <option value="patisseries">Pâtisseries</option>
                        <option value="sandwiches">Sandwiches</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Prix (DT)</label>
                    <input type="number" step="0.01" min="0.01" name="price" id="form_price" required placeholder="2.50">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="form_description" rows="4" required placeholder="Décrivez le délice..."></textarea>
                </div>

                <div class="form-group">
                    <label>Image du Produit</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    <img id="imagePreview" src="" class="drawer-preview-img" style="display: none;">
                </div>

                <div style="margin-top: 40px;">
                    <button type="submit" id="submitBtn" name="add_product" class="btn-admin-add" style="width: 100%;">Enregistrer</button>
                </div>
            </form>
        </div>

        <script>
            function openDrawer(mode, data = null) {
                const drawer = document.getElementById('adminDrawer');
                const overlay = document.getElementById('formOverlay');
                const title = document.getElementById('drawerTitle');
                const submitBtn = document.getElementById('submitBtn');
                
                // Clear form
                document.getElementById('form_id').value = '';
                document.getElementById('form_name').value = '';
                document.getElementById('form_category').value = 'boissons-chaudes';
                document.getElementById('form_price').value = '';
                document.getElementById('form_description').value = '';
                document.getElementById('imagePreview').style.display = 'none';

                if (mode === 'edit' && data) {
                    title.innerText = 'Modifier le Produit';
                    submitBtn.name = 'update_product';
                    submitBtn.innerText = 'Mettre à jour';
                    
                    document.getElementById('form_id').value = data.id;
                    document.getElementById('form_name').value = data.name;
                    document.getElementById('form_category').value = data.category;
                    document.getElementById('form_price').value = data.price;
                    document.getElementById('form_description').value = data.description;
                    
                    if (data.image) {
                        const preview = document.getElementById('imagePreview');
                        preview.src = data.image.startsWith('http') ? data.image : '../images/' + data.image;
                        preview.style.display = 'block';
                    }
                } else {
                    title.innerText = 'Ajouter un Produit';
                    submitBtn.name = 'add_product';
                    submitBtn.innerText = 'Enregistrer';
                }

                drawer.classList.add('open');
                overlay.classList.add('active');
                document.body.classList.add('admin-overlay-active');
            }

            function closeDrawer() {
                document.getElementById('adminDrawer').classList.remove('open');
                document.getElementById('formOverlay').classList.remove('active');
                document.body.classList.remove('admin-overlay-active');
            }

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('imagePreview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
        <script src="../js/shared.js"></script>
        <script src="../js/theme.js"></script>
        <script src="../js/admin.js?v=1.1"></script>

    <?php endif; ?>
</body>
</html>
