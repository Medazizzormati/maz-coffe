<?php
// konnect_webhook.php
// This script handles the GET request from Konnect after a payment attempt.

session_start();
include 'db.php';

// Log the request for debugging
$log_data = date('Y-m-d H:i:s') . " - Webhook received: " . json_encode($_GET) . "\n";
file_put_contents('payment_logs.txt', $log_data, FILE_APPEND);

if (isset($_GET['payment_ref'])) {
    $payment_ref = mysqli_real_escape_string($conn, $_GET['payment_ref']);
    
    // In a real application, you would:
    // 1. Verify the payment status with Konnect API using the payment_ref
    // 2. Update the order status in your database
    // 3. Clear the user's cart
    
    // For this demonstration, we'll just redirect the user to a success or failure page
    // Since we don't have a specific success page, we'll go back to the menu with a message
    
    header("Location: menu.php?payment_status=success&ref=" . $payment_ref);
    exit;
} else {
    header("Location: menu.php?payment_status=error");
    exit;
}
?>
