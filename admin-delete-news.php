<?php
session_start();

if (
    empty($_SESSION['logged_in']) ||
    empty($_SESSION['admin_id'])
) {
    header("Location: index.php");
    exit;
}

include 'backend/db.php';

if (!isset($_GET['id'])) {
    header("Location: admin-home.php");
    exit;
}

$id = (int)$_GET['id'];
$adminId = $_SESSION['admin_id'];

// only allow deletion if the logged-in admin added the news
$stmt = $conn->prepare("
    DELETE FROM News
    WHERE ID = ?
      AND ADMIN_ID = ?
");
$stmt->bind_param("ii", $id, $adminId);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    header("Location: admin-home.php?deleted=1");
    exit;
} else {
    header("Location: admin-home.php?error=unauthorized");
    exit;
}
