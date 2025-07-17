<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .sidebar {
        height: 100vh;
        width: 220px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #343a40;
        padding-top: 60px;
    }

    .sidebar a {
        padding: 15px;
        text-decoration: none;
        color: #ccc;
        display: block;
    }

    .sidebar a:hover {
        background-color: #495057;
        color: #fff;
    }

    .content {
        margin-left: 220px;
        padding: 20px;
    }

    .topbar {
        height: 60px;
        background-color: #ffffff;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 0 20px;
        position: fixed;
        top: 0;
        left: 220px;
        right: 0;
        z-index: 1000;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-info i {
        font-size: 22px;
        color: #333;
    }

    .username-text {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
      <h5 class="text-white text-center">User Panel</h5>
      <a href="adduser.php"><i class="fas fa-user-plus me-2"></i> User Add</a>
      <a href="payment.php"><i class="fas fa-credit-card me-2"></i> Payment</a>
      <a href="autoadd.php"><i class="fas fa-credit-card me-2"></i> Add User Automatic</a>
       <a href="chatbot.php"><i class="fas fa-credit-card me-2"></i> Chatbot</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
      <div class="user-info">
          <i class="fas fa-user-circle"></i>
          <span class="username-text"><?= htmlspecialchars($_SESSION['name']) ?></span>
      </div>
  </div>

  <!-- Main Content -->
  <div class="content pt-4 mt-5">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
      <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>
  </div>

</body>
</html>
