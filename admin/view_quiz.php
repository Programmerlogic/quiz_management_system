<?php
session_start();
include('../config/db.php');

// Redirect if not logged in
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

// Fetch quiz
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->execute([$quiz_id, $_SESSION['admin_id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: dashboard.php');
    exit;
}

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Quiz</title>
    <link rel="stylesheet" href="../assets/style.css">
   <style>
        /* Animated Gradient Background */
@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    background: linear-gradient(-45deg, #43cea2, #185a9d, #43cea2, #185a9d);
    background-size: 400% 400%;
    animation: gradientBG 20s ease infinite;
    color: #2c3e50;
    min-height: 100vh;
}

.view-quiz-container {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow:
      0 10px 15px rgba(0, 0, 0, 0.05),
      0 4px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    animation: fadeIn 0.8s ease forwards;
}
.question-box {
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 2px solid #2980b9;
}

.view-quiz-container:hover {
    box-shadow:
      0 15px 25px rgba(0, 0, 0, 0.1),
      0 8px 10px rgba(0, 0, 0, 0.12);
}

/* Headings */
h2, h3 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 20px;
    letter-spacing: 0.03em;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Labels */
label {
    font-weight: 600;
    display: block;
    margin-top: 20px;
    color: #34495e;
    letter-spacing: 0.02em;
}

/* Form Inputs */
input[type="text"],
textarea,
select {
    width: 100%;
    padding: 12px 16px;
    margin-top: 8px;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 16px;
    font-family: inherit;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    outline-offset: 2px;
}

input[type="text"]:focus,
textarea:focus,
select:focus {
    border-color: #2980b9;
    box-shadow: 0 0 8px rgba(41, 128, 185, 0.3);
    outline: none;
}

/* Fieldset & Legend */
fieldset {
    margin-top: 30px;
    border-radius: 10px;
    border: 1.5px solid #ddd;
    padding: 30px 25px;
    background-color: #fafafa;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    transition: border-color 0.3s ease;
}

fieldset:focus-within {
    border-color: #2980b9;
}

legend {
    font-weight: 700;
    color: #2980b9;
    font-size: 1.1em;
    padding: 0 10px;
}

/* Submit Button */
input[type="submit"] {
    background-color: #2980b9;
    color: white;
    padding: 14px 28px;
    margin-top: 30px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 18px;
    letter-spacing: 0.05em;
    box-shadow: 0 8px 15px rgba(41, 128, 185, 0.3);
    transition:
      background-color 0.4s ease,
      box-shadow 0.4s ease,
      transform 0.2s ease;
    user-select: none;
}

input[type="submit"]:hover {
    background-color: #1c6690;
    box-shadow: 0 12px 20px rgba(28, 102, 144, 0.5);
    transform: translateY(-2px);
}

input[type="submit"]:active {
    transform: translateY(0);
    box-shadow: 0 6px 10px rgba(28, 102, 144, 0.3);
}

/* Links */
a {
    color: #e74c3c;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

a:hover {
    text-decoration: underline;
    color: #c0392b;
}

/* Error Messages */
.error {
    color: #e74c3c;
    font-weight: 700;
    margin-top: 10px;
    letter-spacing: 0.02em;
}

/* Character Counter */
.char-counter {
    font-size: 0.85em;
    color: #888;
    margin-top: 4px;
    font-style: italic;
}

/* Responsive */
@media (max-width: 600px) {
    .edit-quiz-container {
        padding: 20px 25px;
    }
    input[type="submit"] {
        width: 100%;
        font-size: 16px;
    }
}
    </style>
</head>

<body>
    <div class="view-quiz-container">
        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($quiz['description'])); ?></p>

        <h3>Questions</h3>
        <?php if (!empty($questions)) { ?>
            <?php foreach ($questions as $index => $q) { ?>
                <div class="question-box">
                    <p><strong>Q<?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($q['question_text']); ?></p>
                    <ul>
                        <li <?php if ($q['correct_option'] == 'A') echo 'class="correct"'; ?>>A. <?php echo htmlspecialchars($q['option_a']); ?></li>
                        <li <?php if ($q['correct_option'] == 'B') echo 'class="correct"'; ?>>B. <?php echo htmlspecialchars($q['option_b']); ?></li>
                        <li <?php if ($q['correct_option'] == 'C') echo 'class="correct"'; ?>>C. <?php echo htmlspecialchars($q['option_c']); ?></li>
                        <li <?php if ($q['correct_option'] == 'D') echo 'class="correct"'; ?>>D. <?php echo htmlspecialchars($q['option_d']); ?></li>
                    </ul>
                    <p><strong>Correct Answer:</strong> Option <?php echo $q['correct_option']; ?></p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No questions available in this quiz.</p>
        <?php } ?>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
