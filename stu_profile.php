<?php
require '../../database/auth.php';
require_student();
require '../../database/db.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error_msg = '';

// Check if profile exists
$stmt = $conn->prepare("SELECT u.username, u.full_name, u.email, sp.college, sp.roll_no, sp.study_year, sp.contact, sp.location 
                        FROM users u LEFT JOIN student_profiles sp ON u.id = sp.user_id WHERE u.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $fullname, $email, $college, $roll, $year, $contact, $location);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $college = $_POST['college'];
    $roll = $_POST['roll'];
    $year = $_POST['year'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];

    // Update profiles table (Insert or Update)
    $check_stmt = $conn->prepare("SELECT id FROM student_profiles WHERE user_id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $upd_stmt = $conn->prepare("UPDATE student_profiles SET college=?, roll_no=?, study_year=?, contact=?, location=? WHERE user_id=?");
        $upd_stmt->bind_param("sssssi", $college, $roll, $year, $contact, $location, $user_id);
        $upd_stmt->execute();
    } else {
        $ins_stmt = $conn->prepare("INSERT INTO student_profiles (user_id, college, roll_no, study_year, contact, location) VALUES (?, ?, ?, ?, ?, ?)");
        $ins_stmt->bind_param("isssss", $user_id, $college, $roll, $year, $contact, $location);
        $ins_stmt->execute();
    }
    $success = "Profile saved successfully!";
}

$view_mode = isset($_GET['view']) && $_GET['view'] == '1';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Student Profile System</title>

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

        .container-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 40px auto;
            width: 80%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .title {
            text-align: center;
            color: #43618d;
            font-weight: bold;
        }

        .center {
            text-align: center;
            margin-top: 20px;
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
                <a class="nav-link active" href="stu_profile.php">Profile</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>


    <?php if(!$view_mode): ?>
    <div class="container-box">

        <h2 class="title">STUDENT PROFILE SYSTEM</h2>
        <hr>

        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="row mb-3">
                <div class="col-md-6">
                    Username:
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled>
                </div>

                <div class="col-md-6">
                    Full Name:
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($fullname); ?>" disabled>
                </div>
            </div>

            College:
            <select name="college" class="form-control mb-2" required>
                <option value="">Select College</option>
                <option value="Zeal College Sangli" <?php if($college=='Zeal College Sangli') echo 'selected'; ?>>Zeal College Sangli</option>
                <option value="VP Institute Sangli" <?php if($college=='VP Institute Sangli') echo 'selected'; ?>>VP Institute Sangli</option>
                <option value="IMCC Pune" <?php if($college=='IMCC Pune') echo 'selected'; ?>>IMCC Pune</option>
                <option value="JSPM Coolege Pune" <?php if($college=='JSPM Coolege Pune') echo 'selected'; ?>>JSPM Coolege Pune</option>
                <option value="RIT coolege Pune" <?php if($college=='RIT coolege Pune') echo 'selected'; ?>>RIT coolege Pune</option>
            </select>

            <div class="row mt-3">
                <div class="col-md-6">
                    Roll No:
                    <input type="text" name="roll" class="form-control" value="<?php echo htmlspecialchars((string)$roll); ?>" required>
                </div>

                <div class="col-md-6">
                    Year:
                    <select name="year" class="form-control" required>
                        <option value="">Select Year</option>
                        <option value="First" <?php if($year=='First') echo 'selected'; ?>>First</option>
                        <option value="Second" <?php if($year=='Second') echo 'selected'; ?>>Second</option>
                        <option value="Third" <?php if($year=='Third') echo 'selected'; ?>>Third</option>
                        <option value="Final" <?php if($year=='Final') echo 'selected'; ?>>Final</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    Email:
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" disabled>
                </div>

                <div class="col-md-6">
                    Contact:
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars((string)$contact); ?>" required>
                </div>
            </div>

            <div class="mt-3">
                Location:
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars((string)$location); ?>" required>
            </div>

            <div class="center">
                <button type="submit" name="save_profile" class="btn btn-success">Save Profile</button>
                <a href="?view=1" class="btn btn-primary">View Profile</a>
            </div>

        </form>
    </div>
    <?php else: ?>

    <!-- PROFILE -->
    <div class="container-box">
        <h2 class="title">STUDENT PROFILE</h2>
        <hr>

        <p><b>Username:</b> <span><?php echo htmlspecialchars($username); ?></span></p>
        <p><b>Full Name:</b> <span><?php echo htmlspecialchars($fullname); ?></span></p>
        <p><b>College:</b> <span><?php echo htmlspecialchars((string)$college); ?></span></p>
        <p><b>Roll No:</b> <span><?php echo htmlspecialchars((string)$roll); ?></span></p>
        <p><b>Year:</b> <span><?php echo htmlspecialchars((string)$year); ?></span></p>
        <p><b>Email:</b> <span><?php echo htmlspecialchars($email); ?></span></p>
        <p><b>Contact:</b> <span><?php echo htmlspecialchars((string)$contact); ?></span></p>
        <p><b>Location:</b> <span><?php echo htmlspecialchars((string)$location); ?></span></p>

        <div class="center">
            <a href="stu_profile.php" class="btn btn-secondary">Back to Edit</a>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
