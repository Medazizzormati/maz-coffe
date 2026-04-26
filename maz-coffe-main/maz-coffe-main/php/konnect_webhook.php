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
    
    require_once 'konnect_config.php';

    // 1. Verify the payment status with Konnect API
    $ch = curl_init(KONNECT_API_URL . "payments/" . $_GET['payment_ref']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . KONNECT_API_KEY
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = 'error';
    if ($http_code === 200) {
        $result = json_decode($response, true);
        if (isset($result['payment']['status'])) {
            $status = $result['payment']['status']; // 'completed' or 'pending'
        }
    }

    // 2. Update the order status in your database
    $update_query = "UPDATE orders SET status = '$status' WHERE payment_ref = '$payment_ref'";
    if (!mysqli_query($conn, $update_query)) {
        file_put_contents('konnect_debug.log', date('Y-m-d H:i:s') . " - Webhook SQL Error: " . mysqli_error($conn) . "\n", FILE_APPEND);
    }
    
    // 3. Redirection based on status
    if ($status === 'completed' || $status === 'success') {
        header("Location: menu.php?payment_status=success&ref=" . $payment_ref . "&clear_cart=true");
    } else {
        header("Location: menu.php?payment_status=pending&ref=" . $payment_ref);
    }
    exit;
} else {
    header("Location: menu.php?payment_status=error");
    exit;
}
?>

