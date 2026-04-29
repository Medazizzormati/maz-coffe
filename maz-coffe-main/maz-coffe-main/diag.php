<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'php/db.php';

echo "Database: $dbname\n";
echo "Connection: " . ($conn ? "OK" : "FAILED") . "\n";

$tables = ['users', 'products', 'orders', 'contact_messages'];
foreach ($tables as $table) {
    $res = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($res) > 0) {
        echo "Table '$table': EXISTS\n";
        $desc = mysqli_query($conn, "DESCRIBE $table");
        while ($row = mysqli_fetch_assoc($desc)) {
            echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Table '$table': MISSING\n";
    }
}
?>
