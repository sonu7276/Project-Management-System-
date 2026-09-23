<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$port = 3307;

// Create connection
$conn = new mysqli($host, $username, $password, "", $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the SQL file
$sqlFile = 'c:\xampp\htdocs\project_system\database\mysql';
$sql = file_get_contents($sqlFile);

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "✅ Database and tables created successfully!<br>";
} else {
    echo "❌ Error executing SQL: " . $conn->error . "<br>";
}

// Wait for multi_query to finish, then switch to the correct database
while ($conn->more_results() && $conn->next_result()) {}
$conn->select_db("project_system_db");

// ── MIGRATIONS: add missing columns to existing tables ──────────────────────
$migrations = [
    // Add receiver_id to messages if missing
    "ALTER TABLE messages ADD COLUMN receiver_id INT DEFAULT NULL AFTER sender_id",
    "ALTER TABLE messages ADD CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL",
    // Add file_path to messages if missing
    "ALTER TABLE messages ADD COLUMN file_path VARCHAR(255) DEFAULT NULL AFTER message_text",
    // Hash admin password using PHP if still plain text
];

foreach ($migrations as $sql) {
    if ($conn->query($sql)) {
        echo "✅ Migration applied: <code>" . htmlspecialchars($sql) . "</code><br>";
    } else {
        // Duplicate column errors are OK (column already exists)
        if (strpos($conn->error, 'Duplicate column') !== false || strpos($conn->error, 'already exists') !== false) {
            echo "ℹ️ Already exists (skipped): <code>" . htmlspecialchars($sql) . "</code><br>";
        } else {
            echo "⚠️ Migration skipped: " . htmlspecialchars($conn->error) . "<br>";
        }
    }
}

// ── Fix admin password: hash it if stored as plain text ─────────────────────
$res = $conn->query("SELECT id, password FROM users WHERE username='admin' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    if (!password_get_info($row['password'])['algo']) {
        // Plain text password — hash it now
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $row['id']);
        if ($stmt->execute()) {
            echo "✅ Admin password hashed successfully.<br>";
        }
    } else {
        echo "ℹ️ Admin password is already hashed.<br>";
    }
}

echo "<br><strong>✅ Setup complete!</strong> <a href='../frontend/login.php'>Go to Login</a>";

$conn->close();
?>
