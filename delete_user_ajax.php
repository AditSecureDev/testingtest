<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $conn->real_escape_string($_POST['delete_id']);

    $sql = "DELETE FROM user_payments WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]);
    }
}
?>
