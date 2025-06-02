<?php
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Validate quiz_id
if (!isset($_GET['quiz_id']) || !is_numeric($_GET['quiz_id'])) {
    header('Location: dashboard.php');
    exit;
}

$quiz_id = (int)$_GET['quiz_id'];

// Delete quiz
$stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->execute([$quiz_id, $_SESSION['admin_id']]);

// Redirect to dashboard
header('Location: dashboard.php');
exit;
?>
