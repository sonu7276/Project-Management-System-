<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_id'])) {
    $project_id = (int)$_POST['project_id'];

    // Check if already requested or member
    $check = $conn->prepare("SELECT id FROM join_requests WHERE project_id = ? AND student_id = ?");
    $check->bind_param("ii", $project_id, $user_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "You have already sent a request to this project!";
    } else {
        $check_mem = $conn->prepare("SELECT id FROM project_members WHERE project_id = ? AND user_id = ?");
        $check_mem->bind_param("ii", $project_id, $user_id);
        $check_mem->execute();
        if ($check_mem->get_result()->num_rows > 0) {
            $error = "You are already a member of this project!";
        } else {
            $ins = $conn->prepare("INSERT INTO join_requests (project_id, student_id, status) VALUES (?, ?, 'pending')");
            $ins->bind_param("ii", $project_id, $user_id);
            if ($ins->execute()) {
                $success = "Request Sent!";
            } else {
                $error = "Failed to send request.";
            }
        }
    }
}

// Fetch available projects (not created by this user)
$projects = [];
$stmt = $conn->prepare("SELECT p.id, p.title, p.description, p.technology, p.domain, p.max_team_size, u.full_name as creator
                        FROM projects p 
                        JOIN users u ON p.created_by = u.id 
                        WHERE p.created_by != ? AND p.status = 'open' ORDER BY p.id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Projects</title>
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

        /* ===== NAVBAR ===== */
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
                <a class="nav-link" href="studentDashboard.php">Dashboard</a>
                <a class="nav-link" href="stu_profile.php">Profile</a>
                <a class="nav-link active" href="view_project.php">View Projects</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Available Projects</h2>

        <?php if($success): ?>
            <div class="alert alert-success mt-3"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row mt-4">
            <?php foreach($projects as $p): ?>
            <div class="col-md-6 mb-3">
                <div class="card p-3 shadow-sm h-100">
                    <h4 class="text-primary"><?php echo htmlspecialchars($p['title']); ?></h4>
                    <p class="mb-1"><strong>Domain:</strong> <?php echo htmlspecialchars($p['domain']); ?></p>
                    <p class="mb-1"><strong>Tech:</strong> <?php echo htmlspecialchars($p['technology']); ?></p>
                    <p class="mb-1"><strong>Created By:</strong> <?php echo htmlspecialchars($p['creator']); ?></p>
                    <p class="text-muted mt-2"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
                    <div class="mt-auto pt-3 border-top">
                        <form method="POST">
                            <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn btn-success">Request to Join</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($projects)): ?>
                <div class="col-12 text-center text-muted"><p>No open projects available right now.</p></div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
