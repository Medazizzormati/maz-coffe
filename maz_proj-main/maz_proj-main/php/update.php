<?php
// update.php
include 'db.php';

// Handle form submission for update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check if new image is uploaded
    $imageFile = $_FILES['image']['name'];
    $tmpDir = $_FILES['image']['tmp_name'];
    
    if(!empty($imageFile)) {
        $uploadDir = '../images/';
        $imageExt = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
        $validExts = array('jpeg', 'jpg', 'png', 'gif', 'webp');
        
        $newImageName = time() . '_' . rand(1000, 9999) . '.' . $imageExt;
        
        if(in_array($imageExt, $validExts)) {
            // Delete old image
            $queryOldImg = "SELECT image FROM products WHERE id=$id";
            $resOldImg = mysqli_query($conn, $queryOldImg);
            $rowOldImg = mysqli_fetch_assoc($resOldImg);
            if(!empty($rowOldImg['image']) && file_exists($uploadDir . $rowOldImg['image'])) {
                unlink($uploadDir . $rowOldImg['image']);
            }
            
            // Upload new image
            move_uploaded_file($tmpDir, $uploadDir . $newImageName);
            
            // Update with image
            $query = "UPDATE products SET name='$name', image='$newImageName', category='$category', description='$description', price='$price' WHERE id=$id";
        } else {
            header("Location: admin.php?err=" . urlencode("Seules les images JPG, JPEG, PNG, GIF, WEBP sont autorisées."));
            exit;
        }
    } else {
        // Update without changing image
        $query = "UPDATE products SET name='$name', category='$category', description='$description', price='$price' WHERE id=$id";
    }
    
    if (mysqli_query($conn, $query)) {
        header("Location: admin.php?msg=" . urlencode("Le produit a été mis à jour."));
    } else {
        header("Location: admin.php?err=" . urlencode("Erreur lors de la mise à jour: " . mysqli_error($conn)));
    }
    exit;
}

// Fetch product data to pre-fill the form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM products WHERE id=$id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        header("Location: admin.php?err=" . urlencode("Produit introuvable."));
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Produit - Admin Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D4A76A;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #f9f5f0; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        h2 { margin-bottom: 30px; color: var(--primary-color); border-bottom: 2px solid var(--secondary-color); padding-bottom: 15px; font-family: 'Roboto', sans-serif; font-size: 2rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--secondary-color); box-shadow: 0 0 5px rgba(212, 167, 106, 0.3); }
        .btn-submit { background-color: var(--primary-color); padding: 12px 25px; text-decoration: none; color: white !important; border: none; border-radius: 6px; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: background-color 0.3s, transform 0.2s; }
        .btn-submit:hover { background-color: #D2691E; transform: translateY(-2px); }
        .btn-cancel { background-color: #7f8c8d; padding: 12px 25px; text-decoration: none; color: white !important; border-radius: 6px; margin-left: 15px; font-size: 1.1rem; font-weight: 600; transition: background-color 0.3s, transform 0.2s; display: inline-block; }
        .btn-cancel:hover { background-color: #95a5a6; transform: translateY(-2px); }
        .current-img { max-width: 150px; height: 150px; object-fit: cover; margin-top: 15px; display: block; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        small { display: block; margin-top: 5px; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modifier le produit</h2>
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            
            <div class="form-group">
                <label for="name">Nom du produit</label>
                <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Catégorie</label>
                <select name="category" id="category" required>
                    <option value="boissons-chaudes" <?php if($product['category'] == 'boissons-chaudes') echo 'selected'; ?>>Boissons chaudes</option>
                    <option value="boissons-froides" <?php if($product['category'] == 'boissons-froides') echo 'selected'; ?>>Boissons froides</option>
                    <option value="patisseries" <?php if($product['category'] == 'patisseries') echo 'selected'; ?>>Pâtisseries</option>
                    <option value="sandwiches" <?php if($product['category'] == 'sandwiches') echo 'selected'; ?>>Sandwiches</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Prix (€)</label>
                <input type="number" step="0.01" name="price" id="price" required value="<?php echo htmlspecialchars($product['price']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Changer l'image (Optionnel)</label>
                <input type="file" name="image" id="image" accept="image/*">
                <?php if(!empty($product['image'])): ?>
                    <img src="../images/<?php echo $product['image']; ?>" class="current-img" alt="Current Image">
                    <small>Image actuelle</small>
                <?php endif; ?>
            </div>

            <button type="submit" name="update" class="btn-submit">Mettre à jour</button>
            <a href="admin.php" class="btn-cancel">Annuler</a>
        </form>
    </div>
</body>
</html>
