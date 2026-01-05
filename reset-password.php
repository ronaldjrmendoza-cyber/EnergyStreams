<!-- works the same as setup.php but instead handles password reset token verification -->
<?php
session_start();
include 'backend/db.php';

$token = trim($_GET['token'] ?? '');

if (!$token) {
    die("Invalid link");
}

// handles accidentally duplicated token 
if (strlen($token) == 128 && substr($token, 0, 64) === substr($token, 64, 64)) {
    $token = substr($token, 0, 64);
}

$tokenHash = hash('sha256', $token);

$stmt = $conn->prepare("
    SELECT ID, RESET_TOKEN_EXPIRES
    FROM Admin
    WHERE RESET_TOKEN_HASH = ?
      AND IS_INITIALIZED = 1
    LIMIT 1
");
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
$result = $stmt->get_result();

if (!$admin = $result->fetch_assoc()) {
    die("Invalid or used link");
}

if (strtotime($admin['RESET_TOKEN_EXPIRES']) < time()) {
    die("Link expired");
}

$_SESSION['reset_admin_id'] = $admin['ID'];
$_SESSION['show_reset_password'] = true;

// redirects to index.php after resetting
header("Location: index.php");
exit;
