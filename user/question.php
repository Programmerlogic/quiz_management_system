<?php
// user/question.php
include('../config/db.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;
if (!$quiz_id) {
    header('Location: start_quiz.php');
    exit;
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Create attempt_id on first question
if (!isset($_SESSION['attempt_id']) || $offset == 0) {
    $stmt = $pdo->prepare("SELECT MAX(attempt_id) FROM user_results WHERE user_id = :user_id AND quiz_id = :quiz_id");
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'quiz_id' => $quiz_id]);
    $max_attempt = $stmt->fetchColumn();
    $_SESSION['attempt_id'] = $max_attempt ? $max_attempt + 1 : 1;
}
$attempt_id = $_SESSION['attempt_id'];

// Get total number of questions
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = :quiz_id");
$stmt->execute(['quiz_id' => $quiz_id]);
$total_questions = $stmt->fetchColumn();

$is_last_question = ($offset + 1 == $total_questions);

// Fetch question
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id LIMIT 1 OFFSET :offset");
$stmt->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    // Submit score
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id AND quiz_id = :quiz_id AND attempt_id = :attempt_id AND is_correct = 1");
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'quiz_id' => $quiz_id, 'attempt_id' => $attempt_id]);
    $score = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = :quiz_id");
    $stmt->execute(['quiz_id' => $quiz_id]);
    $total = $stmt->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO user_results (user_id, quiz_id, attempt_id, score, total, submitted_at) VALUES (:user_id, :quiz_id, :attempt_id, :score, :total, NOW())");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'quiz_id' => $quiz_id,
        'attempt_id' => $attempt_id,
        'score' => $score,
        'total' => $total
    ]);

    unset($_SESSION['attempt_id']);
    header("Location: result.php?quiz_id=$quiz_id&attempt_id=$attempt_id");
    exit;
}

// Save answer if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['answer'] ?? null;

    // Only update if user selected an answer
    if ($selected) {
        $stmt = $pdo->prepare("SELECT id FROM user_answers WHERE user_id = :user_id AND quiz_id = :quiz_id AND attempt_id = :attempt_id AND question_id = :question_id");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'quiz_id' => $quiz_id,
            'attempt_id' => $attempt_id,
            'question_id' => $question['id']
        ]);
        $existing = $stmt->fetchColumn();

        $is_correct = ($selected == $question['correct_option']) ? 1 : 0;

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE user_answers SET selected_option = :selected_option, is_correct = :is_correct WHERE id = :id");
            $stmt->execute([
                'selected_option' => $selected,
                'is_correct' => $is_correct,
                'id' => $existing
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user_answers (user_id, quiz_id, attempt_id, question_id, selected_option, is_correct) 
                VALUES (:user_id, :quiz_id, :attempt_id, :question_id, :selected_option, :is_correct)");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'quiz_id' => $quiz_id,
                'attempt_id' => $attempt_id,
                'question_id' => $question['id'],
                'selected_option' => $selected,
                'is_correct' => $is_correct
            ]);
        }
    }

    // Navigate
    if (isset($_POST['next'])) {
        header("Location: question.php?quiz_id=$quiz_id&offset=" . ($offset + 1));
        exit;
    } elseif (isset($_POST['prev'])) {
        header("Location: question.php?quiz_id=$quiz_id&offset=" . ($offset - 1));
        exit;
    }
}

// Pre-fill selected answer if already answered
$stmt = $pdo->prepare("SELECT selected_option FROM user_answers WHERE user_id = :user_id AND quiz_id = :quiz_id AND attempt_id = :attempt_id AND question_id = :question_id");
$stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'quiz_id' => $quiz_id,
    'attempt_id' => $attempt_id,
    'question_id' => $question['id']
]);
$selected_option = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Question</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px 10px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h3 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            padding: 10px 0;
            font-size: 16px;
        }
        input[type="radio"] {
            margin-right: 8px;
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        #prevBtn {
            background-color: #6c757d;
            color: white;
        }
        #nextBtn {
            background-color: #007bff;
            color: white;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div>Question <?php echo $offset + 1; ?> of <?php echo $total_questions; ?></div>
    <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>

    <form id="quizForm" method="POST">
        <?php foreach (['A', 'B', 'C', 'D'] as $option): ?>
            <label>
                <input type="radio" name="answer" value="<?= $option ?>" <?= $selected_option === $option ? 'checked' : '' ?>>
                <?= htmlspecialchars($question['option_' . strtolower($option)]) ?>
            </label>
        <?php endforeach; ?>

        <div class="buttons">
            <button type="submit" name="prev" id="prevBtn" <?= $offset == 0 ? 'disabled' : '' ?>>Previous</button>
            <button type="submit" name="next" id="nextBtn"><?= $is_last_question ? 'Finish' : 'Next' ?></button>
        </div>

        <p class="error" id="errorMessage"></p>
    </form>
</div>

<script>
document.getElementById("quizForm").addEventListener("submit", function(e) {
    let selected = document.querySelector('input[name="answer"]:checked');
    let error = document.getElementById("errorMessage");
    let nextBtn = document.getElementById("nextBtn");

    // If pressing next/finish without selecting
    if (!selected && e.submitter.name !== 'prev') {
        e.preventDefault();
        error.textContent = "Please select an answer.";
        return;
    }

    // If last question, confirm before submitting
    let isLast = <?= $is_last_question ? 'true' : 'false' ?>;
    if (isLast && e.submitter.name === 'next') {
        let confirmFinish = confirm("Are you sure you want to finish the quiz?");
        if (!confirmFinish) {
            e.preventDefault();
        }
    }
});
</script>

</body>
</html>
