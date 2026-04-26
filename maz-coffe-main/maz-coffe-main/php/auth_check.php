<?php
// auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is admin
 */
function confirmAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: signin.php?msg=" . urlencode("Accès restreint aux administrateurs."));
        exit();
    }
}

/**
 * Check if user is logged in
 */
function confirmLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php?msg=" . urlencode("Veuillez vous connecter pour accéder à cette page."));
        exit();
    }
}
/**
 * Returns true if the user is an admin, false otherwise. (No redirect)
 */
function isAdminLoggedIn() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}
?>



