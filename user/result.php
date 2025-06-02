<?php
// user/result.php
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;
$attempt_id = isset($_GET['attempt_id']) ? $_GET['attempt_id'] : null;

if (!$quiz_id || !$attempt_id) {
    header('Location: start_quiz.php');
    exit;
}

// Get total number of questions in the quiz
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = :quiz_id");
$stmt->execute(['quiz_id' => $quiz_id]);
$total_questions = $stmt->fetchColumn();

// Get number of correct answers by the user for this quiz attempt
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id AND quiz_id = :quiz_id AND attempt_id = :attempt_id AND is_correct = 1");
$stmt->execute(['user_id' => $_SESSION['user_id'], 'quiz_id' => $quiz_id, 'attempt_id' => $attempt_id]);
$total_correct = $stmt->fetchColumn();

// Calculate score as total_correct out of total_questions
$score = $total_correct;

$stmt = $pdo->prepare("SELECT q.question_text, ua.selected_option, 
    CASE ua.selected_option
        WHEN 'A' THEN q.option_a
        WHEN 'B' THEN q.option_b
        WHEN 'C' THEN q.option_c
        WHEN 'D' THEN q.option_d
        ELSE 'Unknown'
    END AS selected_option_text,
    q.correct_option,
    CASE q.correct_option
        WHEN 'A' THEN q.option_a
        WHEN 'B' THEN q.option_b
        WHEN 'C' THEN q.option_c
        WHEN 'D' THEN q.option_d
        ELSE 'Unknown'
    END AS correct_option_text
FROM user_answers ua
JOIN questions q ON ua.question_id = q.id
WHERE ua.user_id = :user_id AND ua.quiz_id = :quiz_id AND ua.attempt_id = :attempt_id AND ua.is_correct = 0");
$stmt->execute(['user_id' => $_SESSION['user_id'], 'quiz_id' => $quiz_id, 'attempt_id' => $attempt_id]);
$wrong_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quiz Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .result-container {
            background: white;
            max-width: 700px;
            width: 90%;
            margin-top: 50px;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #222;
            text-align: center;
        }
        p, li {
            font-size: 16px;
            color: #444;
            line-height: 1.5;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            background: #f0f4f8;
            border-left: 5px solid #e74c3c;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 4px;
        }
        strong {
            color: #333;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 25px;
            font-weight: 600;
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h1>Quiz Result</h1>
        <p><strong>Score:</strong> <?php echo $score; ?> out of <?php echo $total_questions; ?></p>

        <?php if ($wrong_answers): ?>
            <h2>Questions You Answered Incorrectly</h2>
            <ul>
                <?php foreach ($wrong_answers as $wrong): ?>
                    <li>
                        <strong>Question:</strong> <?php echo htmlspecialchars($wrong['question_text']); ?><br />
                        <strong>Your Answer:</strong> <?php echo htmlspecialchars($wrong['selected_option_text']); ?><br />
                        <strong>Correct Answer:</strong> <?php echo htmlspecialchars($wrong['correct_option_text']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>🎉 Congratulations! You answered all questions correctly.</p>
        <?php endif; ?>
    </div>
    <a href="home.php">Back to Home</a>
</body>
</html>
