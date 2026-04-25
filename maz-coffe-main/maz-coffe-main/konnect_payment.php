<?php
// konnect_payment.php
session_start();
require_once 'konnect_config.php';
include 'db.php';

header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['cart']) || empty($data['cart'])) {
    echo json_encode(['error' => 'Panier vide']);
    exit;
}

$cart = $data['cart'];
$total_amount = 0;

foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Convert to Millimes (Konnect requirement for TND: 1 TND = 1000 Millimes)
$amount_in_millimes = round($total_amount * 1000);

// Get User Info from session if available
$firstName = "Client";
$lastName = "M.A.Z";
$email = "client@example.com";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_res = mysqli_query($conn, "SELECT username, email FROM users WHERE id = $user_id");
    if ($user_res && $user_row = mysqli_fetch_assoc($user_res)) {
        $parts = explode(' ', $user_row['username'], 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : "Client";
        $email = $user_row['email'];
    }
}

// Prepare Konnect API payload - Full
$payload = [
    "receiverWalletId" => KONNECT_WALLET_ID,
    "token" => PAYMENT_CURRENCY,
    "amount" => $amount_in_millimes,
    "type" => "immediate",
    "description" => "Commande M.A.Z Coffee House",
    "acceptedPaymentMethods" => ["wallet", "bank_card", "e-DINAR"],
    "lifespan" => 60, // 1 hour for cash payments
    "checkoutForm" => true,
    "addPaymentFeesToAmount" => false,
    "firstName" => $firstName,
    "lastName" => $lastName,
    "email" => $email,
    "orderId" => "MAZ-" . time(),
    "webhook" => "http://localhost:8000/konnect_webhook.php",
    "theme" => "light"
];

// Initialize cURL
$ch = curl_init(KONNECT_API_URL . "payments/init-payment");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . KONNECT_API_KEY
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['error' => 'Erreur de connexion à Konnect: ' . $error]);
} else {
    $result = json_decode($response, true);
    if ($http_code === 200 && isset($result['payUrl'])) {
        echo json_encode(['payUrl' => $result['payUrl']]);
    } else {
        echo json_encode(['error' => 'Konnect API Error', 'details' => $result, 'code' => $http_code]);
    }
}
?>
