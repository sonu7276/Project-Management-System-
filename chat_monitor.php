<?php
require '../../database/auth.php';
require_admin();
require '../../database/db.php';

$success = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $del_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        $success = "Message deleted successfully.";
    }
}

// Fetch all messages
$messages = [];
$res = $conn->query("
    SELECT m.id, p.title as project_title, u_sender.username as sender, 
           u_receiver.username as receiver, m.message_text, m.file_path, m.sent_at
    FROM messages m
    JOIN projects p ON m.project_id = p.id
    JOIN users u_sender ON m.sender_id = u_sender.id
    LEFT JOIN users u_receiver ON m.receiver_id = u_receiver.id
    ORDER BY m.id DESC
");
if ($res === false) {
    die("<div class='container mt-4'><div class='alert alert-danger'>Database query failed: " . htmlspecialchars($conn->error) . "<br>Please run <a href='../../database/setup_db.php'>setup_db.php</a> to update your database schema.</div></div>");
}
while ($row = $res->fetch_assoc()) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat Monitor</title>
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

        .msg-box {
            max-height: 500px;
            overflow-y: auto;
        }

        .badge-user {
            background: #0d6efd;
        }

        .badge-group {
            background: #198754;
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
    <h3 class="page-title">Monitor Student Chats</h3>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card-box">

        <!-- Chat Table -->
        <div class="table-responsive msg-box">
            <table class="table table-hover table-bordered text-center align-middle" id="chatTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Message</th>
                        <th>Attachment</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($messages as $m): ?>
                    <tr>
                        <td><?php echo $m['id']; ?></td>
                        <td><?php echo htmlspecialchars($m['project_title']); ?></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($m['sender']); ?></span></td>
                        <td>
                            <?php if(empty($m['receiver'])): ?>
                                <span class="badge bg-success">Group</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($m['receiver']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-start"><?php echo htmlspecialchars((string)$m['message_text']); ?></td>
                        <td>
                            <?php if(!empty($m['file_path'])): ?>
                                <a href="../../<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="btn btn-sm btn-info">📎 File</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo $m['sent_at']; ?></small></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="delete_id" value="<?php echo $m['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($messages)): ?>
                    <tr>
                        <td colspan="8">No chat messages found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
