<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_project'])) {
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $tech = $_POST['tech'];
    $domain = $_POST['domain'];
    $team = (int)$_POST['team'];
    $edit_id = $_POST['edit_id'];

    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, technology=?, domain=?, max_team_size=? WHERE id=? AND created_by=?");
        $stmt->bind_param("ssssiii", $title, $desc, $tech, $domain, $team, $edit_id, $user_id);
        if ($stmt->execute()) {
            $success = "Project Updated!";
        } else {
            $error = "Error updating project!";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, technology, domain, max_team_size, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $desc, $tech, $domain, $team, $user_id);
        if ($stmt->execute()) {
            $success = "Project Added!";
        } else {
            $error = "Error adding project!";
        }
    }
}

// Fetch user's projects
$projects = [];
$stmt = $conn->prepare("SELECT id, title, description, technology, domain, max_team_size FROM projects WHERE created_by = ? ORDER BY id DESC");
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
    <title>Add Project</title>

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
            color: #ddd !important;
        }

        .custom-nav a:hover {
            color: #fff !important;
        }

        h2 {
            text-align: center;
            margin: 25px 0;
            font-weight: 600;
        }

        /* CARD STYLE FORM */
        .box {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        /* TABLE STYLE */
        .table-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        th {
            background: #343a40 !important;
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        /* BUTTONS */
        .btn {
            border-radius: 6px;
        }

        label {
            font-weight: 500;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="top-banner">
    PROJECT MANAGEMENT SYSTEM
</div>

<!-- NAV -->
<nav class="navbar navbar-expand-lg custom-nav">
    <div class="container">
        <div class="navbar-nav">
            <a class="nav-link" href="../home.php">Home</a>
            <a class="nav-link" href="../about.php">About Us</a>
            <a class="nav-link" href="studentDashboard.php">Dashboard</a>
            <a class="nav-link" href="stu_profile.php">Profile</a>
            <a class="nav-link active" href="add_project.php">Add Project</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <h2>ADD / UPDATE PROJECT</h2>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="box">

        <form method="POST" action="">

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="mb-3">
                <label><i class="bi bi-pencil-square"></i> Project Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label><i class="bi bi-card-text"></i> Description</label>
                <textarea name="desc" id="desc" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label><i class="bi bi-cpu"></i> Technology</label>
                <input type="text" name="tech" id="tech" class="form-control" required>
            </div>

            <div class="mb-3">
                <label><i class="bi bi-globe"></i> Domain</label>
                <input type="text" name="domain" id="domain" class="form-control" required>
            </div>

            <div class="mb-3">
                <label><i class="bi bi-people"></i> Max Team Members</label>
                <input type="number" name="team" id="team" class="form-control" min="1" required>
            </div>

            <button type="submit" name="save_project" class="btn btn-success">
                Save Project
            </button>

            <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
                Clear Form
            </button>

        </form>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <h4 class="mb-3">My Projects</h4>

        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Tech</th>
                    <th>Domain</th>
                    <th>Team Size</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($projects as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['technology']); ?></td>
                    <td><?php echo htmlspecialchars($p['domain']); ?></td>
                    <td><?php echo htmlspecialchars((string)$p['max_team_size']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editProject(
                            <?php echo $p['id']; ?>,
                            '<?php echo addslashes(htmlspecialchars($p['title'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($p['description'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($p['technology'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($p['domain'])); ?>',
                            <?php echo $p['max_team_size']; ?>
                        )">Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

</div>

<script>
function editProject(id, title, desc, tech, domain, team) {
    document.getElementById("edit_id").value = id;
    document.getElementById("title").value = title;
    document.getElementById("desc").value = desc;
    document.getElementById("tech").value = tech;
    document.getElementById("domain").value = domain;
    document.getElementById("team").value = team;
    window.scrollTo(0, 0);
}

function clearForm() {
    document.getElementById("edit_id").value = "";
    document.getElementById("title").value = "";
    document.getElementById("desc").value = "";
    document.getElementById("tech").value = "";
    document.getElementById("domain").value = "";
    document.getElementById("team").value = "";
}
</script>

</body>
</html>
