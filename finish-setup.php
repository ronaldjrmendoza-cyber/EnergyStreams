<!-- Finalizes admin account setup -->
<?php
session_start();
include 'backend/db.php';

if (empty($_SESSION['setup_admin_id'])) {
    die("Unauthorized");
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if (!$username || !$password) {
    die("All fields required");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$update = $conn->prepare("
    UPDATE Admin
    SET USERNAME = ?, PASSWORD_HASH = ?, IS_INITIALIZED = 1,
        INVITE_TOKEN_HASH = NULL,
        INVITE_TOKEN_EXPIRES = NULL
    WHERE ID = ?
");
$update->bind_param("ssi", $username, $passwordHash, $_SESSION['setup_admin_id']);
$update->execute();

unset($_SESSION['setup_admin_id']);

echo "Account created. You may now log in.";
