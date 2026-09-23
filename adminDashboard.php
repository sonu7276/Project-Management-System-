<?php
require '../../database/auth.php';
require_admin();
require '../../database/db.php';

// Fetch statistics
$stat_students = 0;
$stat_projects = 0;
$stat_requests = 0;

$res_stud = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='student'");
if ($row = $res_stud->fetch_assoc()) $stat_students = $row['cnt'];

$res_proj = $conn->query("SELECT COUNT(*) as cnt FROM projects");
if ($row = $res_proj->fetch_assoc()) $stat_projects = $row['cnt'];

$res_req = $conn->query("SELECT COUNT(*) as cnt FROM join_requests");
if ($row = $res_req->fetch_assoc()) $stat_requests = $row['cnt'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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

        /* ===== TITLE ===== */
        .dashboard-title {
            text-align: center;
            margin: 25px 0;
            font-weight: 600;
            color: #1f2d3d;
        }

        /* ===== CARD ===== */
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
            font-size: 30px;
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .dashboard-card a {
            text-decoration: none;
            color: #1f2d3d;
            font-weight: 600;
            display: block;
        }

        /* ===== STATS ===== */
        .stat-card {
            background: white;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stat-card h4 {
            margin: 0;
            font-weight: bold;
            color: #1f2d3d;
        }

        .stat-card p {
            margin: 0;
            color: gray;
            font-size: 14px;
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
            <a class="nav-link active" href="adminDashboard.php">Admin Dashboard</a>
            <a class="nav-link" href="reports.php">Reports</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- TITLE -->
<h2 class="dashboard-title">ADMIN DASHBOARD</h2>

<div class="container mb-5">

    <!-- STATS -->
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="stat-card">
                <h4><?php echo $stat_students; ?></h4>
                <p>Total Students</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h4><?php echo $stat_projects; ?></h4>
                <p>Total Projects</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h4><?php echo $stat_requests; ?></h4>
                <p>Join Requests</p>
            </div>
        </div>
    </div>

    <!-- ACTION CARDS -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-people-fill"></i>
                <a href="manage_student.php">Manage Students</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-folder-fill"></i>
                <a href="manage_projects.php">Manage Projects</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-check-circle-fill"></i>
                <a href="manage_requests.php">View Join Requests</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-chat-dots-fill"></i>
                <a href="chat_monitor.php">Chat Monitor</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="bi bi-bar-chart-fill"></i>
                <a href="reports.php">System Reports</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>
