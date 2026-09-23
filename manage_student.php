<?php
require '../../database/auth.php';
require_admin();
require '../../database/db.php';

$success = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $del_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        $success = "Student deleted successfully.";
    }
}

// Fetch all students
$students = [];
$res = $conn->query("
    SELECT u.id, u.full_name, u.username, u.email, sp.college, sp.study_year
    FROM users u
    LEFT JOIN student_profiles sp ON u.id = sp.user_id
    WHERE u.role = 'student'
    ORDER BY u.id DESC
");
while ($row = $res->fetch_assoc()) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
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

        th {
            background: #2c3e50 !important;
            color: white !important;
        }

        .btn-sm {
            border-radius: 20px;
            padding: 5px 12px;
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
            <a class="nav-link" href="../home.php">Home</a>
            <a class="nav-link" href="../about.php">About Us</a>
            <a class="nav-link active" href="adminDashboard.php">Admin</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <a href="adminDashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    <h2 class="page-title">Manage Students</h2>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card-box table-responsive">

        <table class="table table-hover table-bordered text-center align-middle" id="studentTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>College</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['username']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars((string)$s['college']); ?></td>
                    <td><?php echo htmlspecialchars((string)$s['study_year']); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $s['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($students)): ?>
                <tr><td colspan="7">No students found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
