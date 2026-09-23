<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "project_system_db";
$port = 3307;

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
