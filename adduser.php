<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$addUserSuccess = false;
$addUserError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $payment = $conn->real_escape_string($_POST['payment']);

    if (!empty($name) && !empty($phone) && is_numeric($payment)) {
        $sql = "INSERT INTO user_payments (name, phone, payment_amount)
                VALUES ('$name', '$phone', '$payment')";
        if ($conn->query($sql) === TRUE) {
            $addUserSuccess = true;
        } else {
            $addUserError = "Database Error: " . $conn->error;
        }
    } else {
        $addUserError = "All fields are required and payment must be numeric.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome (for icons) -->
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

        .content {
            margin-left: 220px;
            padding: 100px 20px 20px;
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
            <span class="fw-semibold"><?= htmlspecialchars($_SESSION['name']) ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
        <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>

        <!-- Add User Button -->
        <button class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Add User
        </button>+

        <!-- User Payments Table -->
        <div class="mt-4">
            <h5 class="mb-3">User Payment Records</h5>
            <div class="table-responsive">
                <table id="userTable" class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Payment Amount</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM user_payments ORDER BY created_at DESC");
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td>₹ <?= number_format($row['payment_amount'], 2) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <!-- Edit Icon -->
                                        <a href="#" class="text-primary me-2" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $row['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Delete Icon -->
                                        <a href="#" class="text-danger delete-user" data-id="<?= $row['id'] ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1"
                                    aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form class="editUserForm" id="editForm<?= $row['id'] ?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Full Name</label>
                                                        <input type="text" name="edit_name" class="form-control"
                                                            value="<?= htmlspecialchars($row['name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone Number</label>
                                                        <input type="text" name="edit_phone" class="form-control"
                                                            value="<?= htmlspecialchars($row['phone']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Payment Amount</label>
                                                        <input type="number" step="0.01" name="edit_payment"
                                                            class="form-control" value="<?= $row['payment_amount'] ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="update_user"
                                                        class="btn btn-success">Update</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>




    <!-- Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addUserForm" method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment Amount</label>
                            <input type="number" step="0.01" class="form-control" name="payment" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_user" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap + DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function () {
            $('#userTable').DataTable({
                responsive: true,
                "pageLength": 5
            });
        });
    </script>
</body>

</html>


<script>
    $(document).ready(function () {
        $('.editUserForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                type: 'POST',
                url: 'edit_user_ajax.php',
                data: form.serialize(),
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('.modal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                }
            });
        });
    });
</script>

<script>
    $('.delete-user').on('click', function (e) {
        e.preventDefault();
        const userId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the user record!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'delete_user_ajax.php',
                    data: { delete_id: userId },
                    success: function (response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message
                            });
                        }
                    }
                });
            }
        });
    });
</script>






<script>
    $(document).ready(function () {
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'add_user_ajax.php',
                data: $(this).serialize(),
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addUserModal').modal('hide');
                            location.reload(); // Reload to update the table
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                }
            });
        });
    });
</script>