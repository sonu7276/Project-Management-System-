<?php
// ================================================================
// SAFE MIGRATION SCRIPT
// Adds missing columns to existing database WITHOUT dropping data
// Run once: http://localhost/project_system/database/migrate.php
// ================================================================

$host     = "127.0.0.1";
$username = "root";
$password = "";
$port     = 3307;
$db       = "project_system_db";

$conn = new mysqli($host, $username, $password, $db, $port);

if ($conn->connect_error) {
    die("<p style='color:red'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

echo "<h2>🔧 Running Database Migrations...</h2><hr>";

// Helper: run a query and report result
function run_migration($conn, $sql, $desc) {
    if ($conn->query($sql)) {
        echo "✅ <b>$desc</b> — Applied successfully.<br>";
    } else {
        $err = $conn->error;
        // "Duplicate column" or similar = already exists, that's OK
        if (strpos($err, 'Duplicate column') !== false
            || strpos($err, 'already exists')  !== false
            || strpos($err, 'Can\'t DROP')     !== false) {
            echo "ℹ️ <b>$desc</b> — Already exists, skipped.<br>";
        } else {
            echo "⚠️ <b>$desc</b> — Skipped: $err<br>";
        }
    }
}

// ── 1. Add receiver_id to messages ────────────────────────────────────────────
run_migration($conn,
    "ALTER TABLE messages ADD COLUMN receiver_id INT DEFAULT NULL AFTER sender_id",
    "messages.receiver_id column"
);

// ── 2. Add foreign key for receiver_id ────────────────────────────────────────
run_migration($conn,
    "ALTER TABLE messages ADD CONSTRAINT fk_msg_receiver
     FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL",
    "messages.receiver_id foreign key"
);

// ── 3. Add file_path to messages ──────────────────────────────────────────────
run_migration($conn,
    "ALTER TABLE messages ADD COLUMN file_path VARCHAR(255) DEFAULT NULL AFTER message_text",
    "messages.file_path column"
);

// ── 4. Hash admin password if still plain text ────────────────────────────────
$res = $conn->query("SELECT id, password FROM users WHERE username='admin' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $info = password_get_info($row['password']);
    if (!$info['algo']) {
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $row['id']);
        $stmt->execute();
        echo "✅ <b>Admin password</b> — Hashed successfully (was plain text).<br>";
    } else {
        echo "ℹ️ <b>Admin password</b> — Already hashed, skipped.<br>";
    }
} else {
    // Admin user doesn't exist yet — create it
    $hashed = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT IGNORE INTO users (full_name, username, email, password, role)
                            VALUES ('System Admin', 'admin', 'admin@system.com', ?, 'admin')");
    $stmt->bind_param("s", $hashed);
    $stmt->execute();
    echo "✅ <b>Admin user</b> — Created (username: admin, password: admin123).<br>";
}

$conn->close();

echo "<hr><h3>✅ All migrations complete!</h3>";
echo "<p><a href='../frontend/login.php' style='font-size:16px'>➡️ Go to Login Page</a></p>";
?>
