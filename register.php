<?php
session_start();
require '../database/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists!";
        } else {
            // Insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, 'student')");
            $insert_stmt->bind_param("ssss", $fullname, $username, $email, $hashed_password);
            
            if ($insert_stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register Page</title>

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

        /* Form Box */
        .form-box {
             width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .link {
            text-align: center;
            margin-top: 10px;
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

    <!-- Register Form -->
    <div class="container">
        <div class="form-box">
            <h2>Register Form</h2>

            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn btn-dark w-100">Register</button>
            </form>

            <div class="link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>

</body>

</html>
