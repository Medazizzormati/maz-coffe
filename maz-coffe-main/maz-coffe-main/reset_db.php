<?php
// reset_db.php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "coffee_shop";

$conn = mysqli_connect($servername, $username, $password, "", 3306);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Dropping database $dbname...\n";
if (mysqli_query($conn, "DROP DATABASE IF EXISTS $dbname")) {
    echo "Database dropped successfully.\n";
} else {
    echo "Error dropping database: " . mysqli_error($conn) . "\n";
}

echo "Creating database $dbname...\n";
if (mysqli_query($conn, "CREATE DATABASE $dbname")) {
    echo "Database created successfully.\n";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
