<?php
session_start();
require_once 'db.php';

$loginError = "";
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // do not escape this yet

    $sql = "SELECT * FROM users_table WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name']; // store name for welcome
            $loginSuccess = true;
        } else {
            $loginError = "Invalid password.";
        }
    } else {
        $loginError = "Username not found.";
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>

        <center class="mt-5">
            <h2>Login </h2>
        </center>

        <div class="container mt-5">
            <div class="row justify-content-center align-items-center g-2">
                <div class="col-6">
                    <div class="card">
                        <div class="mb-3 p-3">
                            <form action="" method="post">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="Username" required />
                                    <label for="username">Username</label>
                                </div>

                                <div class="form-floating mb-3 mt-2">
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="Password" required />
                                    <label for="password">Password</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>

</html>


<?php if ($loginSuccess): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome <?= $_SESSION['name'] ?>! Redirecting to your dashboard...',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "dashboard.php";
        });
    </script>
<?php elseif (!empty($loginError)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: <?= json_encode($loginError) ?>
        });
    </script>
<?php endif; ?>