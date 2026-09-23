<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch user's projects
$projects = [];
$stmt = $conn->prepare("
    SELECT p.id, p.title 
    FROM projects p
    LEFT JOIN project_members pm ON p.id = pm.project_id
    WHERE p.created_by = ? OR pm.user_id = ?
    GROUP BY p.id
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $projects[] = $row;
}

$selected_project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : (count($projects) > 0 ? $projects[0]['id'] : 0);

// Fetch members of selected project
$members = [];
if ($selected_project_id > 0) {
    $m_stmt = $conn->prepare("
        SELECT u.id, u.username, 'Creator' as role FROM projects p JOIN users u ON p.created_by = u.id WHERE p.id = ? AND u.id != ?
        UNION
        SELECT u.id, u.username, pm.role FROM project_members pm JOIN users u ON pm.user_id = u.id WHERE pm.project_id = ? AND u.id != ?
    ");
    $m_stmt->bind_param("iiii", $selected_project_id, $user_id, $selected_project_id, $user_id);
    $m_stmt->execute();
    $m_res = $m_stmt->get_result();
    while ($row = $m_res->fetch_assoc()) {
        $members[] = $row;
    }
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_msg'])) {
    $msg = $_POST['message'];
    $proj_id = (int)$_POST['project_id'];
    $chat_type = $_POST['chatType'];
    $receiver_id = ($chat_type === 'private') ? (int)$_POST['receiver_id'] : NULL;
    
    // File upload (basic handling)
    $file_path = NULL;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "../../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = "uploads/" . $file_name; // Relative path for db
        }
    }

    if (!empty($msg) || $file_path) {
        $ins = $conn->prepare("INSERT INTO messages (project_id, sender_id, receiver_id, message_text, file_path) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("iiiss", $proj_id, $user_id, $receiver_id, $msg, $file_path);
        $ins->execute();
    }
    // Redirect to clear POST
    header("Location: chatting_between_partners.php?project_id=" . $proj_id);
    exit;
}

// Fetch messages for selected project
$messages = [];
if ($selected_project_id > 0) {
    // Get all group messages OR private messages where user is sender/receiver
    $msg_stmt = $conn->prepare("
        SELECT m.*, u.username as sender_name 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        WHERE m.project_id = ? 
        AND (m.receiver_id IS NULL OR m.receiver_id = ? OR m.sender_id = ?)
        ORDER BY m.sent_at ASC
    ");
    $msg_stmt->bind_param("iii", $selected_project_id, $user_id, $user_id);
    $msg_stmt->execute();
    $msg_res = $msg_stmt->get_result();
    while ($row = $msg_res->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Project Chat System</title>
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

        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
        }

        .msg {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            max-width: 75%;
        }

        .me {
            text-align: right;
            background-color: #e3f2fd;
            margin-left: auto;
        }

        .other {
            text-align: left;
            background-color: #f1f8e9;
            margin-right: auto;
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
                <a class="nav-link active" href="chatting_between_partners.php">Chat</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <h3 class="text-center dashboard-title">Project Chat System</h3>

        <?php if(count($projects) == 0): ?>
            <div class="alert alert-warning text-center">You are not part of any project yet.</div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-3">
                <div class="card p-3 shadow-sm">
                    <h5>Select Project</h5>
                    <form method="GET" action="">
                        <select name="project_id" class="form-control mb-2" onchange="this.form.submit()">
                            <?php foreach($projects as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($selected_project_id == $p['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card p-3 shadow-sm">
                    <!-- Chat Box -->
                    <div class="chat-box mb-3" id="chatBox">
                        <?php foreach($messages as $m): 
                            $is_me = ($m['sender_id'] == $user_id);
                            $msg_type = $is_me ? "me" : "other";
                            $chat_label = $m['receiver_id'] ? "(Private)" : "(Group)";
                        ?>
                            <div class="msg <?php echo $msg_type; ?>">
                                <b><?php echo $is_me ? "Me" : htmlspecialchars($m['sender_name']); ?> <?php echo $chat_label; ?></b><br>
                                <?php echo htmlspecialchars((string)$m['message_text']); ?><br>
                                <?php if($m['file_path']): ?>
                                    <a href="../../<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="btn btn-sm btn-info mt-1 mb-1">📎 Attachment</a><br>
                                <?php endif; ?>
                                <small class="text-muted"><?php echo $m['sent_at']; ?></small>
                            </div>
                        <?php endforeach; ?>
                        <?php if(empty($messages)): ?>
                            <p class="text-center text-muted">No messages yet. Start the conversation!</p>
                        <?php endif; ?>
                    </div>

                    <!-- Send Form -->
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="project_id" value="<?php echo $selected_project_id; ?>">
                        
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <select name="chatType" id="chatType" class="form-control" onchange="toggleReceiver()">
                                    <option value="group">Group Chat</option>
                                    <option value="private">Private Chat</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select name="receiver_id" id="receiver" class="form-control" style="display:none;">
                                    <?php foreach($members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['username']) . " (" . $member['role'] . ")"; ?></option>
                                    <?php endforeach; ?>
                                    <?php if(empty($members)): ?>
                                        <option value="">No other members</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Type message...">
                            <input type="file" name="file" class="form-control" style="max-width: 250px;">
                            <button type="submit" name="send_msg" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleReceiver() {
            var type = document.getElementById("chatType").value;
            document.getElementById("receiver").style.display = (type === 'private') ? 'block' : 'none';
        }
        
        // Scroll to bottom
        window.onload = function() {
            var chatBox = document.getElementById("chatBox");
            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        };
    </script>

</body>
</html>
