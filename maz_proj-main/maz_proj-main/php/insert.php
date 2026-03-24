<?php
// insert.php
include 'db.php';

if (isset($_POST['submit'])) {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $imageFile = $_FILES['image']['name'];
    $tmpDir = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];
    
    if($imageFile) {
        $uploadDir = '../images/'; // directory for uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageExt = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION)); // extension
        $validExts = array('jpeg', 'jpg', 'png', 'gif', 'webp'); // valid extensions
        
        $newImageName = time() . '_' . rand(1000, 9999) . '.' . $imageExt;
        
        // Allow valid image file formats
        if(in_array($imageExt, $validExts)) {
            move_uploaded_file($tmpDir, $uploadDir . $newImageName);
        } else {
            header("Location: admin.php?err=" . urlencode("Seules les images JPG, JPEG, PNG, GIF, WEBP sont autorisées."));
            exit;
        }
    } else {
        $newImageName = ''; // Fallback if somehow no image passed despite Required tag
    }

    // Insert Product into database
    $query = "INSERT INTO products (name, image, category, description, price) VALUES ('$name', '$newImageName', '$category', '$description', '$price')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: admin.php?msg=" . urlencode("Le produit a été ajouté avec succès."));
    } else {
        header("Location: admin.php?err=" . urlencode("Erreur lors de l'ajout: " . mysqli_error($conn)));
    }
    
} else {
    // Si on accède directement sans POST
    header("Location: admin.php");
}
?>
