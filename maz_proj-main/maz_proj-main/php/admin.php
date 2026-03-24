<?php
// admin.php
include 'db.php';

// Fetch all products
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Café Local - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Reusing main CSS design + little admin-specific additions -->
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D4A76A;
        }
        .admin-section { padding: 40px 0; min-height: 80vh; }
        .admin-card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .admin-card h2 { color: var(--primary-color); margin-bottom: 25px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .admin-table th, .admin-table td { border: 1px solid #eee; padding: 15px; text-align: left; }
        .admin-table th { background-color: var(--primary-color); color: white; font-weight: 600; text-transform: uppercase; font-size: 0.9rem; }
        .admin-table tr:nth-child(even) { background-color: #fafafa; }
        .admin-table tr:hover { background-color: #f5f5f5; }
        .admin-table img { max-width: 80px; height: 80px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; color: white; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; font-size: 0.9rem; transition: transform 0.2s, opacity 0.2s; }
        .btn:hover { transform: translateY(-2px); opacity: 0.9; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #e74c3c; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--secondary-color); box-shadow: 0 0 5px rgba(212, 167, 106, 0.3); }
        .btn-submit { background-color: var(--primary-color); padding: 12px 25px; font-size: 1.1rem; margin-top: 10px; color: white !important; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; transition: background-color 0.3s, transform 0.2s; }
        .btn-submit:hover { background-color: #D2691E; transform: translateY(-2px); }
        .message { padding: 15px; margin-bottom: 25px; border-radius: 6px; font-weight: 600; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="../img/M.A.Z.png" id="photo" alt="Logo M.A.Z Coffee House"></a>
                <h1>Admin Panel</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="index.php">Aller au Site</a></li>
                    <li><a href="admin.php" class="active">Admin Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="admin-section container">
        
        <?php
        if (isset($_GET['msg'])) {
            echo '<div class="message success">' . htmlspecialchars($_GET['msg']) . '</div>';
        }
        if (isset($_GET['err'])) {
            echo '<div class="message error">' . htmlspecialchars($_GET['err']) . '</div>';
        }
        ?>

        <!-- CREATE FORM -->
        <div class="admin-card">
            <h2>Ajouter un nouveau produit</h2>
            <form action="insert.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nom du produit</label>
                    <input type="text" name="name" id="name" required placeholder="Ex: Espresso">
                </div>
                
                <div class="form-group">
                    <label for="category">Catégorie</label>
                    <select name="category" id="category" required>
                        <option value="boissons-chaudes">Boissons chaudes</option>
                        <option value="boissons-froides">Boissons froides</option>
                        <option value="patisseries">Pâtisseries</option>
                        <option value="sandwiches">Sandwiches</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Prix (€)</label>
                    <input type="number" step="0.01" name="price" id="price" required placeholder="Ex: 2.50">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" required placeholder="Un espresso classique et intense..."></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image (Upload 1 fichier)</label>
                    <input type="file" name="image" id="image" accept="image/*" required>
                </div>

                <button type="submit" name="submit" class="btn btn-submit">Ajouter le produit</button>
            </form>
        </div>

        <!-- READ TABLE -->
        <div class="admin-card">
            <h2>Tous les produits</h2>
            <?php if(mysqli_num_rows($result) > 0): ?>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix (€)</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <!-- On suppose que l'image est dans le dossier images/ -->
                                <?php if($row['image']) echo '<img src="../images/'.$row['image'].'" alt="'.htmlspecialchars($row['name']).'">'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . '...'; ?></td>
                            <td>
                                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Modifier</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');"><i class="fas fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>Aucun produit trouvé dans la base de données.</p>
            <?php endif; ?>
        </div>

    </main>

    <footer class="footer">
        <div class="container copyright">
            <p>&copy; 2025 M.A.Z Coffee House. Backend Admin Panel PHP.</p>
        </div>
    </footer>
</body>
</html>
