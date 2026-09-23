<?php
require '../../database/auth.php';
require_admin();
require '../../database/db.php';

$success = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $del_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM join_requests WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        $success = "Join request deleted successfully.";
    }
}

// Fetch all requests
$requests = [];
$res = $conn->query("
    SELECT jr.id, p.title as project_title, u_req.username as from_student, u_creator.username as to_student, jr.status, jr.request_date
    FROM join_requests jr
    JOIN projects p ON jr.project_id = p.id
    JOIN users u_req ON jr.student_id = u_req.id
    JOIN users u_creator ON p.created_by = u_creator.id
    ORDER BY jr.id DESC
");
while ($row = $res->fetch_assoc()) {
    $requests[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Join Requests</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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
            color: #ccc !important;
        }

        .custom-nav a:hover {
            color: #fff !important;
        }

        .page-title {
            text-align: center;
            margin: 30px 0;
            font-weight: 600;
        }

        .card-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .btn-sm {
            border-radius: 20px;
            padding: 5px 12px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .pending { background: #ffc107; color: black; }
        .accepted { background: #28a745; color: white; }
        .rejected { background: #dc3545; color: white; }
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
            <a class="nav-link" href="../home.php">Home</a>
            <a class="nav-link" href="../about.php">About Us</a>
            <a class="nav-link active" href="adminDashboard.php">Admin</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <a href="adminDashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    <h2 class="page-title">Manage Student Join Requests</h2>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card-box table-responsive">

        <table class="table table-hover table-bordered text-center align-middle" id="requestTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>From Student</th>
                    <th>To Student (Creator)</th>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($requests as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo htmlspecialchars($r['from_student']); ?></td>
                    <td><?php echo htmlspecialchars($r['to_student']); ?></td>
                    <td><?php echo htmlspecialchars($r['project_title']); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($r['status']); ?>">
                            <?php echo strtoupper($r['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                            <input type="hidden" name="delete_id" value="<?php echo $r['id']; ?>">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($requests)): ?>
                <tr>
                    <td colspan="6">No join requests found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
