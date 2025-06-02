<?php
// start_quiz.php (Allow User to Start a Quiz)
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch available quizzes
$stmt = $pdo->prepare("SELECT * FROM quizzes");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Quiz</title>
    <style>
        /* Inline CSS (from ../assets/style.css) */
       /* Base styles */
body {
    font-family: Arial, sans-serif;
    /* Animated gradient background */
    background: linear-gradient(-45deg, #6a9cff,rgb(131, 231, 171),rgb(212, 94, 137), #003f99);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh; /* full viewport height */
}

/* Container fade in and slide up */
.quiz-selection-container {
    background: white;
    padding: 30px;
    margin-top: 60px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 90%;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeSlideUp 0.8s forwards ease-out;
}

/* Heading style */
h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

/* List styles */
ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

/* Animate each list item with stagger */
li {
    margin: 15px 0;
    opacity: 0;
    transform: translateX(-20px);
    animation: fadeSlideRight 0.6s forwards ease-out;
}

/* Stagger effect for li items */
li:nth-child(1) {
    animation-delay: 0.4s;
}
li:nth-child(2) {
    animation-delay: 0.55s;
}
li:nth-child(3) {
    animation-delay: 0.7s;
}
li:nth-child(4) {
    animation-delay: 0.85s;
}

/* Quiz buttons base */
.quiz-btn {
    display: inline-block;
    width: 100%;
    text-align: center;
    background-color:rgb(219, 52, 149);
    color: white;
    padding: 12px 0;
    text-decoration: none;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    animation: pulseColor 3s infinite ease-in-out;
}

/* Button hover with scale and shadow */
.quiz-btn:hover {
    background-color: #2980b9;
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(41, 128, 185, 0.6);
}

/* Links */
a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    text-decoration: underline;
    color: #2980b9;
}

/* Paragraph */
p {
    text-align: center;
    margin-top: 20px;
}

/* Keyframes */

/* Container fade in & slide up */
@keyframes fadeSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* List item fade & slide from left */
@keyframes fadeSlideRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
@keyframes gradientBG {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}
/* Subtle button color pulse */
@keyframes pulseColor {
    0%, 100% {
        background-color:rgb(219, 52, 119);
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.7);
    }
    50% {
        background-color: #5dade2;
        box-shadow: 0 0 18px rgba(93, 173, 226, 0.9);
    }
}

    </style>
</head>
<body>
    <div class="quiz-selection-container">
        <h1>Select a Quiz to Start</h1>

        <?php if ($quizzes): ?>
            <ul>
                <?php foreach ($quizzes as $quiz): ?>
                    <li>
                        <a href="question.php?quiz_id=<?php echo $quiz['id']; ?>" class="quiz-btn">
                            <?php echo htmlspecialchars($quiz['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No quizzes available at the moment.</p>
        <?php endif; ?>
    </div>
    <p><a href="home.php" style="font-weight: bold;">Back to Home</a></p>
</body>
</html>
