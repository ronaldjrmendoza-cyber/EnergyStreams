<!-- handles admin access requests -->
<?php
include 'backend/db.php';

define('DEV_MODE', true);

$email = trim($_POST['email'] ?? '');

if (!$email) {
    die("Email required");
}

$stmt = $conn->prepare("
    SELECT ID, INVITE_TOKEN_EXPIRES
    FROM Admin
    WHERE EMAIL = ? AND IS_INITIALIZED = 0
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

// does not reveal whether the email exists for security
if (!$row) {
    echo "If this email exists, an invite will be sent.";
    exit;
}

// prevents spamming if token is still valid
if (!empty($row['INVITE_TOKEN_EXPIRES']) &&
    strtotime($row['INVITE_TOKEN_EXPIRES']) > time()) {

    echo DEV_MODE
        ? "Invite already sent. Check your email."
        : "If this email exists, an invite will be sent.";
    exit;
}

$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);

// sets 1 hour expiration
$expires = date('Y-m-d H:i:s', time() + 3600);

$update = $conn->prepare("
    UPDATE Admin
    SET INVITE_TOKEN_HASH = ?, INVITE_TOKEN_EXPIRES = ?
    WHERE ID = ?
");
$update->bind_param("ssi", $tokenHash, $expires, $row['ID']);
$update->execute();

$inviteLink = "http://localhost:8000/setup.php?token=" . urlencode($token);

// DEV vs PROD behavior
if (DEV_MODE) {
    echo $inviteLink;
} else {
    echo "If this email exists, an invite will be sent.";
}

// If deployed with a doamin, the invite link will be sent to email instead
// sendInviteEmail($email, $inviteLink);

exit;
