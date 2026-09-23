<?php
require '../../database/auth.php';
require_student();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Student Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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

        /* ===== NAVBAR ===== */
        .custom-nav {
            background-color: #1f2d3d;
        }


        .custom-nav a {
            color: #cfd8dc !important;
            font-weight: 500;
            margin-right: 10px;
        }

        .custom-nav a:hover {
            color: #fff !important;
        }

        /* TITLE */
        .dashboard-title {
            text-align: center;
            margin: 25px 0;
            font-weight: 600;
            color: #1f2d3d;
        }

        /* DASHBOARD CARD */
        .dashboard-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border: 1px solid #eee;
            transition: 0.3s;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .dashboard-card i {
            font-size: 28px;
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .dashboard-card a {
            text-decoration: none;
            color: #1f2d3d;
            font-weight: 600;
            display: block;
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
            <a class="nav-link" href="../home.php">Home</a>
            <a class="nav-link" href="../about.php">About Us</a>
            <a class="nav-link active" href="studentDashboard.php">Dashboard</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- TITLE -->
<h2 class="dashboard-title">STUDENT DASHBOARD</h2>

<!-- DASHBOARD -->
<div class="container">

    <div class="row g-4">

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-person-circle"></i>
                <a href="stu_profile.php">Update Profile</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-folder-plus"></i>
                <a href="add_project.php">Add Project</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-eye"></i>
                <a href="view_project.php">View Projects</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-hourglass-split"></i>
                <a href="join_requests_page.php">Request Status</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-chat-dots"></i>
                <a href="chatting_between_partners.php">Chat with Partner</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-briefcase"></i>
                <a href="my_project.php">My Projects</a>
            </div>
        </div>

    </div>

</div>

</body>
</html>
