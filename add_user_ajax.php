<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $payment = $conn->real_escape_string($_POST['payment']);

    if (!empty($name) && !empty($phone) && is_numeric($payment)) {
        $sql = "INSERT INTO user_payments (name, phone, payment_amount)
                VALUES ('$name', '$phone', '$payment')";
        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'User added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields correctly.']);
    }
}
?>