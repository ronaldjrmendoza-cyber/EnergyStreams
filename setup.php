<!-- handles admin invite token verification -->
<?php
session_start();
include 'backend/db.php';

$token = trim($_GET['token'] ?? '');

if (!$token) {
    die("Invalid link");
}

if (strlen($token) == 128 && substr($token, 0, 64) === substr($token, 64, 64)) {
    $token = substr($token, 0, 64);
}

$tokenHash = hash('sha256', $token);

$stmt = $conn->prepare("
    SELECT ID, INVITE_TOKEN_EXPIRES
    FROM Admin
    WHERE INVITE_TOKEN_HASH = ?
      AND IS_INITIALIZED = 0
    LIMIT 1
");
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
$result = $stmt->get_result();

if (!$admin = $result->fetch_assoc()) {
    die("Invalid or used link");
}

if (strtotime($admin['INVITE_TOKEN_EXPIRES']) < time()) {
    die("Link expired");
}

$_SESSION['setup_admin_id'] = $admin['ID'];
$_SESSION['show_set_credentials'] = true;

// redirect to index.php to show the credentials form
header("Location: index.php");
exit;
