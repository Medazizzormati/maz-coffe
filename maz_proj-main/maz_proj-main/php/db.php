<?php
// db.php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "coffee_shop";

// Create connection with explicit port 3307 since XAMPP is configured to use it
$conn = mysqli_connect($servername, $username, $password, $dbname, 3307);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
