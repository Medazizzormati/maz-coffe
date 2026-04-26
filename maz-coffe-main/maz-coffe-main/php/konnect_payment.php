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
    $total_amount += (float)$item['price'] * (int)$item['quantity'];
}

// Round to 2 decimal places to match frontend toFixed(2)
$total_amount = round($total_amount, 2);

// Convert to Millimes (Konnect requirement for TND: 1 TND = 1000 Millimes)
$amount_in_millimes = (int)round($total_amount * 1000);

// Get User Info from session if available
$firstName = "Client";
$lastName = "M.A.Z";
$email = "wafacash@gmail.com"; // Default requested email
$user_id = null;

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

// Prepare Konnect API payload with all methods
$accepted_methods = ["wallet", "bank_card", "e-DINAR", "flouci", "wafacash"];
$order_ref = "MAZ-" . time();
$payload = [
    "receiverWalletId" => KONNECT_WALLET_ID,
    "token" => PAYMENT_CURRENCY,
    "amount" => $amount_in_millimes,
    "type" => "immediate",
    "description" => "Commande M.A.Z Coffee House",
    "acceptedPaymentMethods" => $accepted_methods,
    "lifespan" => 60,
    "checkoutForm" => true,
    "addPaymentFeesToAmount" => false,
    "firstName" => $firstName,
    "lastName" => $lastName,
    "email" => $email,
    "orderId" => $order_ref,
    "webhook" => "http://localhost:8000/php/konnect_webhook.php",
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
curl_close($ch);

$result = json_decode($response, true);

if ($http_code === 200 && isset($result['payUrl'])) {
    $payment_ref = $result['paymentRef'];
    $pay_url = $result['payUrl'];
    
    // Force the cash methods to show at the bottom of the gateway as requested
    if (strpos($pay_url, '?') !== false) {
        $pay_url .= "&selectedPaymentMethod=cash";
    } else {
        $pay_url .= "?selectedPaymentMethod=cash";
    }
    
    // Save order to database
    $user_id_val = $user_id ? $user_id : "NULL";
    $insert_query = "INSERT INTO orders (user_id, payment_ref, amount, payment_method, user_email) 
                     VALUES ($user_id_val, '$payment_ref', $total_amount, 'konnect', '$email')";
    if (!mysqli_query($conn, $insert_query)) {
        file_put_contents('konnect_debug.log', date('Y-m-d H:i:s') . " - SQL Error: " . mysqli_error($conn) . "\n", FILE_APPEND);
    }

    // Automatically send the payment code to email as requested
    $subject = "Votre code de commande M.A.Z Coffee House";
    $message = "Bonjour $firstName,\n\nVotre commande a été enregistrée. Si vous payez en agence (Wafa Cash, etc.), voici votre code de paiement : $payment_ref\n\nMontant : $total_amount DT\n\nMerci de votre confiance !";
    $headers = "From: no-reply@mazcoffee.com";
    
    // Log the email simulation
    file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - To: $email - Code: $payment_ref\n", FILE_APPEND);
    @mail($email, $subject, $message, $headers);

    echo json_encode(['payUrl' => $pay_url, 'paymentRef' => $payment_ref]);
} else {
    // Log for debugging
    file_put_contents('konnect_debug.log', date('Y-m-d H:i:s') . " - Payload: " . json_encode($payload) . "\n", FILE_APPEND);
    file_put_contents('konnect_debug.log', date('Y-m-d H:i:s') . " - Response: " . $response . " - Code: " . $http_code . "\n", FILE_APPEND);

    echo json_encode(['error' => 'Konnect API Error', 'details' => $result, 'code' => $http_code]);
}
?>

