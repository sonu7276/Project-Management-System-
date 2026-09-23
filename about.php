<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - Project Management System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
        .custom-nav {
            background-color: #1f2d3d;
        }

        .custom-nav a {
            color: #ddd !important;
        }

        .custom-nav a:hover {
            color: #fff !important;
        }

        .dashboard-title {
            text-align: center;
            margin: 30px 0;
            font-weight: 600;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .dashboard-card a {
            text-decoration: none;
            color: #212529;
            font-weight: 500;
        }

        .custom-nav a {
            color: #ddd !important;
        }

        .custom-nav a:hover {
            color: #fff !important;
        }

        /* Hero */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
            url("https://images.unsplash.com/photo-1522071820081-009f0129c71c") center/cover;
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 40px;
            font-weight: 700;
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        /* Feature Card */
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transition: 0.2s;
            border: 1px solid #eee;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        /* Team */
        .team-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #eee;
            transition: 0.2s;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .team-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0d6efd;
        }

        /* Footer */
        footer {
            background: #212529;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
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
            <a class="nav-link active" href="about.php">About Us</a>
            <a class="nav-link" href="admin/adminDashboard.php">Admin</a>
            <a class="nav-link" href="student/studentDashboard.php">Student</a>
            <a class="nav-link" href="register.php">Register</a>
            <a class="nav-link" href="login.php">Login</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1>About Our System</h1>
        <p class="lead">Simplifying teamwork, improving productivity, and managing student projects efficiently.</p>
    </div>
</section>

<!-- About -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Who We Are</h2>
        <div class="row">
            <div class="col-md-6">
                <p>
                    Our Project Management System is built for students to create, manage, and collaborate on projects easily.
                    It helps track progress, assign roles, and improve teamwork.
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    The system focuses on simplicity, collaboration, and real-time updates, making project handling easier for both students and administrators.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Key Features</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h5>Task Management</h5>
                    <p>Organize and track tasks efficiently.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h5>Team Collaboration</h5>
                    <p>Work together with real-time updates.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">⏱</div>
                    <h5>Time Tracking</h5>
                    <p>Monitor progress and deadlines easily.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Team -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Our Team members</h2>

        <div class="row g-4 text-center">

            <div class="col-md-4">
                <div class="team-card">
                    <img src="../images/Pratikshapatil.jpeg" alt="">
                    <h5 class="mt-3">Pratiksha Patil</h5>
                    
                </div>
            </div>

            <div class="col-md-4">
                <div class="team-card">
                    <img src="../images/Rutujasangame.jpg" alt="">
                    <h5 class="mt-3">Rutuja Sangame</h5>
                    
                </div>
            </div>

            <div class="col-md-4">
                <div class="team-card">
                    <img src="../images/muskan.jpeg" alt="">
                    <h5 class="mt-3">Muskan Nemani</h5>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Footer
<footer>
    <p>&copy; 2026 Project Management System | All Rights Reserved</p>
</footer> -->

</body>
</html>
