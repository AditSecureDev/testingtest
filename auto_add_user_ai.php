<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = trim($_POST['prompt']);
    $lines = explode("\n", $prompt);
    $insertedUsers = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line))
            continue;

        $parts = preg_split('/\s+/', $line);
        if (count($parts) < 3)
            continue;

        $amount = array_pop($parts);
        $phone = array_pop($parts);
        $name = implode(" ", $parts);

        if (!is_numeric($phone) || !is_numeric($amount)) {
            continue;
        }

        $name = $conn->real_escape_string($name);
        $phone = $conn->real_escape_string($phone);
        $amount = $conn->real_escape_string($amount);

        $sql = "INSERT INTO user_payments (name, phone, payment_amount)
                VALUES ('$name', '$phone', '$amount')";
        if ($conn->query($sql)) {
            $insertedUsers[] = ['name' => $name, 'phone' => $phone, 'amount' => $amount];
        }
    }

    if (count($insertedUsers) > 0) {
        echo json_encode(['status' => 'success', 'users' => $insertedUsers]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No valid users were added.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
exit;
?>