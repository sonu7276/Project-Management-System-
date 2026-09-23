<!DOCTYPE html>
<html>
<head>
    <title>Student Feedback</title>

   <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Top Banner */
        .top-banner {
            background: #212529;
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 30px;
            font-weight: 600;
        }

        /* Navbar */
        .custom-nav {
            background-color: #343a40;
        }

        .custom-nav a {
            color: white !important;
           
        }

        .custom-nav a:hover {
            background-color: #1e4d57;
        }

        /* Main Image */
        .main-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .title {
            text-align: center;
            margin: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #1e4d57;
        }
         .dashboard-title {
            text-align: center;
            margin: 30px 0;
            font-weight: 600;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .dashboard-card a {
            text-decoration: none;
            color: #212529;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <!-- 1. Banner -->
    <div class="top-banner">
        PROJECT MANAGEMENT SYSTEM
    </div>

    <nav class="navbar navbar-expand-lg custom-nav">
        <div class="container">
        
            <div class="navbar-nav">
    <a class="nav-link" href="home.php">Home</a>
    <a class="nav-link" href="about.php">About Us</a>
    <a class="nav-link" href="admin/adminDashboard.php">Admin</a>
    <a class="nav-link" href="student/studentDashboard.php">Student</a>
    <a class="nav-link" href="register.php">Register</a>
    <a class="nav-link" href="login.php">Login</a>
</div>
        </div>
    </nav>
<!-- Feedback Form -->
<div class="container-box">
    <h2 class="title">FEEDBACK FORM</h2>
    <hr>

    <form>
        <div class="mb-3">
            <label>Your Name:</label>
            <input type="text" id="name" class="form-control">
            <div class="error" id="e_name"></div>
        </div>

        <div class="mb-3">
            <label>Rating:</label>
            <select id="rating" class="form-control">
                <option value="">Select Rating</option>
                <option>1 - Poor</option>
                <option>2 - Average</option>
                <option>3 - Good</option>
                <option>4 - Very Good</option>
                <option>5 - Excellent</option>
            </select>
            <div class="error" id="e_rating"></div>
        </div>

        <div class="mb-3">
            <label>Feedback Message:</label>
            <textarea id="message" class="form-control"></textarea>
            <div class="error" id="e_message"></div>
        </div>

        <div class="text-center">
            <button type="button" class="btn btn-success" onclick="validateFeedback()">Submit</button>
            <a href="studentDashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>

<script>
function clearErrors() {
    document.querySelectorAll(".error").forEach(e => e.innerHTML = "");
}

function validateFeedback() {
    clearErrors();

    let name = document.getElementById("name").value;
    let type = document.getElementById("type").value;
    let rating = document.getElementById("rating").value;
    let message = document.getElementById("message").value;

    let valid = true;

    if (name.trim() === "") {
        document.getElementById("e_name").innerHTML = "Enter your name";
        valid = false;
    }

    if (type === "") {
        document.getElementById("e_type").innerHTML = "Select feedback type";
        valid = false;
    }

    if (rating === "") {
        document.getElementById("e_rating").innerHTML = "Select rating";
        valid = false;
    }

    if (message.trim() === "") {
        document.getElementById("e_message").innerHTML = "Enter feedback message";
        valid = false;
    }

    if (valid) {
        let feedback = { name, type, rating, message };
        let list = JSON.parse(localStorage.getItem("feedbackList")) || [];
        list.push(feedback);
        localStorage.setItem("feedbackList", JSON.stringify(list));

        alert("Feedback Submitted Successfully!");
        document.querySelector("form").reset();
    }
}
</script>

</body>
</html>
