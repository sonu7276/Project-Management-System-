<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];
$success = '';

// Handle marking as completed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_project_id'])) {
    $pid = (int)$_POST['complete_project_id'];
    $stmt = $conn->prepare("UPDATE projects SET status = 'completed' WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $pid, $user_id);
    if ($stmt->execute()) {
        $success = "Project marked as completed!";
    }
}

// Fetch projects user is involved in
// A user is involved if they created it OR if they are in project_members
$projects = [];
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.status, p.max_team_size, p.created_by,
           (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) + 1 as current_team
    FROM projects p
    LEFT JOIN project_members pm ON p.id = pm.project_id
    WHERE p.created_by = ? OR pm.user_id = ?
    GROUP BY p.id
    ORDER BY p.id DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['role'] = ($row['created_by'] == $user_id) ? 'Leader' : 'Member';
    $projects[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Projects</title>
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

        /* ===== NAVBAR ===== */
        .custom-nav {
            background-color: #1f2d3d;
        }

        .card {
            margin-top: 20px;
            border-radius: 15px;
        }

        .completed {
            background-color: #d4edda;
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
                <a class="nav-link active" href="my_project.php">My Projects</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">My Projects</h2>

        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach($projects as $p): 
                $isCompleted = ($p['status'] === 'completed');
            ?>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm <?php echo $isCompleted ? 'completed' : ''; ?>">

                    <h5><?php echo htmlspecialchars($p['title']); ?></h5>
                    <p><b>Role:</b> <?php echo $p['role']; ?></p>
                    <p><b>Status:</b> <?php echo ucfirst($p['status']); ?></p>
                    <p><b>Team:</b> <?php echo $p['current_team'] . " / " . $p['max_team_size']; ?> Members</p>

                    <?php if(!$isCompleted && $p['role'] === 'Leader'): ?>
                        <form method="POST">
                            <input type="hidden" name="complete_project_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn btn-warning btn-sm mt-2">Mark Completed</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($projects)): ?>
                <div class="col-12 text-center text-muted"><p>You are not involved in any projects yet.</p></div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
