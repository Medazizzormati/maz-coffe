<?php
// delete.php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // First, find the image to delete the file from the images/ folder
    $query = "SELECT image FROM products WHERE id=$id";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $imagePath = '../images/' . $row['image'];
        
        // Delete from database
        $deleteQuery = "DELETE FROM products WHERE id=$id";
        if (mysqli_query($conn, $deleteQuery)) {
            // Remove the file from disk if it exists
            if (!empty($row['image']) && file_exists($imagePath)) {
                unlink($imagePath);
            }
            header("Location: admin.php?msg=" . urlencode("Le produit a été supprimé."));
        } else {
            header("Location: admin.php?err=" . urlencode("Erreur lors de la suppression: " . mysqli_error($conn)));
        }
    } else {
        header("Location: admin.php?err=" . urlencode("Produit introuvable."));
    }
} else {
    header("Location: admin.php");
}
?>
