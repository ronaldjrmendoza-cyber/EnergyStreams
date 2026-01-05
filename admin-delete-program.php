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
    header("Location: admin-add-programs.php");
    exit;
}

$id = (int)$_GET['id'];
$adminId = $_SESSION['admin_id'];

// only allow deletion if the logged-in admin added the program
$stmt = $conn->prepare("
    DELETE FROM Program
    WHERE ID = ?
    AND ADMIN_ID = ?
");
$stmt->bind_param("ii", $id, $adminId);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    header("Location: admin-add-programs.php?deleted=1");
} else {
    header("Location: admin-add-programs.php?error=unauthorized");
}
exit;
