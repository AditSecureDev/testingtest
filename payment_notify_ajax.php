<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT name, phone, payment_amount FROM user_payments WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found.'
        ]);
        exit;
    }

    $name = htmlspecialchars($user['name']);
    $phone = preg_replace('/\D/', '', $user['phone']);
    $amount = number_format((float) $user['payment_amount'], 2, '.', '');

    // UPI ID and Payee Name — replace with yours
    $upi_id = "yourname@upi";  // Replace with your actual UPI ID
    $payee_name = "Your Business"; // Replace with your business name
    

    $upi_link = "upi://pay?pa=$upi_id&pn=" . urlencode($payee_name) . "&am=$amount&cu=INR";

    // Use qrserver instead of Google
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($upi_link);


    // WhatsApp message
    $message = "Hello $name,%0A%0AYour payment of ₹$amount is pending.%0A%0APlease scan this QR to pay:%0A$qr_url";

    $wa_link = "https://wa.me/91$phone?text=$message";

    echo json_encode([
        'status' => 'success',
        'message' => 'Click to notify user via WhatsApp.',
        'wa_link' => $wa_link,
        'qr_code' => $qr_url
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
}
