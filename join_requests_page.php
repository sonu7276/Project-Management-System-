<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];

// Handle action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['action'])) {
    $req_id = (int)$_POST['request_id'];
    $action = $_POST['action'];
    $status = ($action === 'accept') ? 'accepted' : 'rejected';
    
    // Ensure the project belongs to the current user before updating
    $stmt = $conn->prepare("UPDATE join_requests jr 
                            JOIN projects p ON jr.project_id = p.id 
                            SET jr.status = ? 
                            WHERE jr.id = ? AND p.created_by = ?");
    $stmt->bind_param("sii", $status, $req_id, $user_id);
    $stmt->execute();

    // If accepted, also add to project_members table
    if ($status === 'accepted') {
        // Get the project and student id
        $get_req = $conn->prepare("SELECT project_id, student_id FROM join_requests WHERE id = ?");
        $get_req->bind_param("i", $req_id);
        $get_req->execute();
        $get_req->bind_result($pid, $sid);
        if ($get_req->fetch()) {
            $get_req->close();
            // Check if not already a member
            $check_mem = $conn->prepare("SELECT id FROM project_members WHERE project_id = ? AND user_id = ?");
            $check_mem->bind_param("ii", $pid, $sid);
            $check_mem->execute();
            if ($check_mem->get_result()->num_rows == 0) {
                $check_mem->close();
                $ins_mem = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'member')");
                $ins_mem->bind_param("ii", $pid, $sid);
                $ins_mem->execute();
            }
        }
    }
}

// Fetch requests for projects created by this user
$requests = [];
$stmt = $conn->prepare("SELECT jr.id, p.title as project, u.full_name as user, jr.status 
                        FROM join_requests jr 
                        JOIN projects p ON jr.project_id = p.id 
                        JOIN users u ON jr.student_id = u.id 
                        WHERE p.created_by = ? ORDER BY jr.id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Join Requests</title>

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
            <a class="nav-link active" href="join_requests_page.php">Requests</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Join Requests for My Projects</h2>

    <table class="table table-bordered mt-3 bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Project Name</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($requests as $req): ?>
            <tr>
                <td><?php echo htmlspecialchars($req['project']); ?></td>
                <td><?php echo htmlspecialchars($req['user']); ?></td>
                <td>
                    <?php 
                    $badge = 'bg-warning';
                    if($req['status'] === 'accepted') $badge = 'bg-success';
                    if($req['status'] === 'rejected') $badge = 'bg-danger';
                    ?>
                    <span class="badge <?php echo $badge; ?>"><?php echo ucfirst($req['status']); ?></span>
                </td>
                <td>
                    <?php if($req['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                        <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($requests)): ?>
            <tr><td colspan="4" class="text-center">No requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
