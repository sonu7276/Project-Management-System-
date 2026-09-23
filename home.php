<!DOCTYPE html>
<html>

<head>
    <title>Project Management System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ===== HEADER (STANDARD) ===== */
        .top-banner {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* ===== NAVBAR ===== */
        .custom-nav {
            background-color: #1f2d3d;
            padding: 10px;
        }

        .custom-nav a {
            color: #cfd8dc !important;
            font-weight: 500;
            margin-right: 10px;
        }

        .custom-nav a:hover {
            color: #ffffff !important;
        }

        /* ===== TITLE ===== */
        .title {
            text-align: center;
            margin: 25px 0;
            font-weight: 600;
            color: #1f2d3d;
        }

        /* ===== HERO ===== */
        .hero {
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        .hero h2 {
            font-weight: 700;
            font-size: 30px;
        }

        .hero p {
            color: #666;
        }

        .hero img {
            border-radius: 12px;
        }

        /* ===== BUTTONS ===== */
        .btn-dark {
            background: #1f2d3d;
            border: none;
        }

        .btn-dark:hover {
            background: #000;
        }

        /* ===== FEATURES ===== */
        .section-title {
            text-align: center;
            margin: 40px 0 20px;
            font-weight: 600;
        }

        .feature-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .feature-box h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .feature-box p {
            color: #666;
        }

    </style>
</head>

<body>

<!-- HEADER -->
<div class="top-banner">
    PROJECT MANAGEMENT SYSTEM
</div>

<!-- NAVBAR -->
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

<!-- TITLE -->
<div class="title">
    PROJECT MANAGEMENT SYSTEM
</div>

<!-- HERO -->
<div class="container hero">
    <div class="row align-items-center">

        <div class="col-md-6">
            <h2>Find the Perfect Project Partner</h2>
            <p>
                A smart platform for students to collaborate, manage projects, track progress,
                and build real-world teamwork experience.
            </p>

            <a href="register.php" class="btn btn-dark mt-2">Get Started</a>
            <a href="login.php" class="btn btn-outline-dark mt-2">Login</a>
        </div>

        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f"
                 class="img-fluid">
        </div>

    </div>
</div>

<!-- FEATURES -->
<div class="container">

    <h3 class="section-title">Key Features</h3>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="feature-box">
                <h5>👥 Find Teammates</h5>
                <p>Connect with students based on skills and interests.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-box">
                <h5>📁 Project Management</h5>
                <p>Create, join, and manage academic projects easily.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-box">
                <h5>💬 Real-time Chat</h5>
                <p>Communicate instantly with your team members.</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>
