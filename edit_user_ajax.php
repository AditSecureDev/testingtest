<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['edit_id']);
    $name = $conn->real_escape_string($_POST['edit_name']);
    $phone = $conn->real_escape_string($_POST['edit_phone']);
    $payment = $conn->real_escape_string($_POST['edit_payment']);

    if (!empty($name) && !empty($phone) && is_numeric($payment)) {
        $sql = "UPDATE user_payments 
                SET name = '$name', phone = '$phone', payment_amount = '$payment'
                WHERE id = $id";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid form input.']);
    }
}
?>
