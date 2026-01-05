<!-- works the same as request-invite.php but for password resetting -->
<?php
include 'backend/db.php';

define('DEV_MODE', true);

$email = trim($_POST['email'] ?? '');

if (!$email) {
    die("Email required");
}

$stmt = $conn->prepare("
    SELECT ID, RESET_TOKEN_EXPIRES
    FROM Admin
    WHERE (EMAIL = ? OR USERNAME = ?)
      AND IS_INITIALIZED = 1
    LIMIT 1
");
$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();

$admin = $result->fetch_assoc();

if (!$admin) {
    echo "If this email exists, a reset link will be sent.";
    exit;
}

if (!empty($admin['RESET_TOKEN_EXPIRES']) &&
    strtotime($admin['RESET_TOKEN_EXPIRES']) > time()) {

    echo DEV_MODE
        ? "Reset link already sent. Check your email."
        : "If this email exists, a reset link will be sent.";
    exit;
}

$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);

$expires = date('Y-m-d H:i:s', time() + 3600);

$update = $conn->prepare("
    UPDATE Admin
    SET RESET_TOKEN_HASH = ?, RESET_TOKEN_EXPIRES = ?
    WHERE ID = ?
");
$update->bind_param("ssi", $tokenHash, $expires, $admin['ID']);
$update->execute();

$resetLink = "http://localhost:8000/reset-password.php?token=" . urlencode($token);

if (DEV_MODE) {
    echo $resetLink;
} else {
    echo "If this email exists, a reset link will be sent.";
}

exit;
