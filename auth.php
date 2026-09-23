<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit;
    }
}

function require_student() {
    require_login();
    if ($_SESSION['role'] !== 'student') {
        header("Location: ../admin/adminDashboard.php");
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../student/studentDashboard.php");
        exit;
    }
}
?>
