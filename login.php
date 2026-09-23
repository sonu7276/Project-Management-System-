<?php
session_start();
require '../database/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // Admin might have plain password from the initial seed
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: admin/adminDashboard.php");
            exit;
        }

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin/adminDashboard.php");
            } else {
                header("Location: student/studentDashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
            body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ===== HEADER (COMMON) ===== */
        .top-banner {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Navbar */
        .custom-nav {
            background-color: #1f2d3d;
        }

        .custom-nav a {
            color: white !important;
        }

        .custom-nav a:hover {
            background-color: #1e4d57;
        }

        /* Login Box */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .login-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            background: #1e4d57;
            color: white;
        }

        .btn-login:hover {
            background: #163a42;
        }
    </style>
</head>

<body>

    <!-- Banner -->
    <div class="top-banner">
        PROJECT MANAGEMENT SYSTEM
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-nav">
        <div class="container">
            <div class="navbar-nav">
                <a class="nav-link" href="home.php">Home</a>
                <a class="nav-link" href="about.php">About Us</a>
                <a class="nav-link" href="admin/adminDashboard.php">Admin</a>
                <a class="nav-link" href="student/studentDashboard.php">Student</a>
                <a class="nav-link" href="register.php">Register</a>
                <a class="nav-link" href="login.php">Login</a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Log In</h2>

            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button type="submit" class="btn btn-login">Log In</button>
            </form>
        </div>
    </div>

</body>

</html>
