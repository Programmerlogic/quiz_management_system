<?php
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Validate question_id and quiz_id
if (!isset($_GET['question_id']) || !is_numeric($_GET['question_id']) || !isset($_GET['quiz_id']) || !is_numeric($_GET['quiz_id'])) {
    header('Location: dashboard.php');
    exit;
}

$question_id = (int)$_GET['question_id'];
$quiz_id = (int)$_GET['quiz_id'];

// Verify that the quiz belongs to the logged-in admin
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->execute([$quiz_id, $_SESSION['admin_id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: dashboard.php');
    exit;
}

// Delete the question
$stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?");
$stmt->execute([$question_id, $quiz_id]);

// Redirect back to edit_quiz.php
header("Location: edit_quiz.php?quiz_id=$quiz_id");
exit;
?>
