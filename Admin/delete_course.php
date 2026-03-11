<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: all_coursesAdmin.php");
exit;
?>
