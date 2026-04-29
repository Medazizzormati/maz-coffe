<?php
// auth_logic.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'db.php';

/**
 * Register a new user
 */
function registerUser($username, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);
    
    try {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'user')";
        return mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $e) {
        // Log error or check for duplicate entry code 1062
        return false;
    }
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['profile_image'] = isset($row['profile_image']) ? $row['profile_image'] : 'default_avatar.jpg';
            $_SESSION['last_activity'] = time(); // Initialize activity timer
            return $row['role'];
        }
    }
    return false;
}

/**
 * Logout
 */
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>



