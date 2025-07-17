<?php
require_once 'db.php';
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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

        <table id="paymentTable" class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Notify</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM user_payments");
                while ($row = $res->fetch_assoc()): ?>
                    <tr id="user-<?= $row['id'] ?>">
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td>₹ <?= number_format($row['payment_amount'], 2) ?></td>
                        <td>
                            <a href="payment_notify_ajax.php" class="notify" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-bell"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


    </div>

</body>
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>

<script>
    $(document).ready(function () {
        $('.notify').on('click', function (e) {
            e.preventDefault();
            const userId = $(this).data('id');

            // Show loading with smaller centered gif
            Swal.fire({
                title: 'Please wait...',
                html: '<img src="https://i.gifer.com/ZZ5H.gif" style="width: 80px; height: 80px; display: block; margin: auto;">',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                backdrop: 'rgba(0,0,0,0.3)'
            });

            // Now do the AJAX call
            $.ajax({
                type: 'POST',
                url: 'payment_notify_ajax.php',
                data: { id: userId },
                dataType: 'json',
                success: function (res) {
                    Swal.close(); // Close loading gif

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Send Payment Reminder',
                            html: `
                            <p>${res.message}</p>
                            <p><strong>Scan to Pay:</strong></p>
                            <img src="${res.qr_code}" alt="QR Code" style="width: 180px; height: 180px; display: block; margin: auto;">
                            <br><small>or click WhatsApp below</small>
                        `,
                            showCancelButton: true,
                            confirmButtonText: 'Send via WhatsApp',
                            cancelButtonText: 'Cancel',
                            width: 360,
                            padding: '1rem'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(res.wa_link, '_blank');
                            }
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'AJAX request failed.', 'error');
                }
            });
        });
    });
</script>



<script>
    $(document).ready(function () {
        $('#paymentTable').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            columnDefs: [
                { orderable: false, targets: 4 } // disable sorting on "Notify" column
            ]
        });
    });
</script>