<?php
require_once 'db.php';

$success = false;
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users_table (name, email, username, password)
            VALUES ('$name', '$email', '$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        $error = "Error: " . $conn->error;
    }

    $conn->close();
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
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                                        required />
                                    <label for="name">Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                                        required />
                                    <label for="email">Email</label>
                                </div>
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
                                <button type="submit" class="btn btn-primary w-100">Submit</button>

                                Login if already<a href="login.php"> Registreted</a>
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

<?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            text: 'Redirecting to login page...',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "login.php";
        });
    </script>
<?php elseif ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: <?= json_encode($error) ?>
        });
    </script>
<?php endif; ?>