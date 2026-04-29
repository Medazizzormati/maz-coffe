<?php
// auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout logic (1 hour = 3600 seconds)
$timeout_duration = 3600;

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
        // Last activity was more than 1 hour ago
        session_unset();
        session_destroy();
        header("Location: auth.php?mode=login&msg=" . urlencode("Session expirée après 1 heure d'inactivité."));
        exit();
    }
    // Update last activity time
    $_SESSION['last_activity'] = time();
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



