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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title === '') {
        $error = "Title cannot be empty.";
    } else {
        // Update quiz title and description
        $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ? AND created_by = ?");
        $stmt->execute([$title, $description, $quiz_id, $_SESSION['admin_id']]);

        // Update questions
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $question_id => $question_data) {
                $q_text = trim($question_data['question_text']);
                $opt_a = trim($question_data['option_a']);
                $opt_b = trim($question_data['option_b']);
                $opt_c = trim($question_data['option_c']);
                $opt_d = trim($question_data['option_d']);
                $correct_opt = $question_data['correct_option'];

                if ($q_text !== '' && in_array($correct_opt, ['A', 'B', 'C', 'D'])) {
                    $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ? AND quiz_id = ?");
                    $stmt->execute([$q_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct_opt, $question_id, $quiz_id]);
                }
            }
        }

        header('Location: dashboard.php');
        exit;
    }
} else {
    // Fetch quiz details
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Animated Gradient Background */
@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    background: linear-gradient(-45deg, #ffd6ff, #ffe6fa, #ffd6ff, #ffe6fa);

    background-size: 400% 400%;
    animation: gradientBG 20s ease infinite;
    color: #2c3e50;
    min-height: 100vh;
}

/* Container */
.edit-quiz-container {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow:
      0 10px 15px rgba(0, 0, 0, 0.05),
      0 4px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.edit-quiz-container:hover {
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
   <script>
document.addEventListener("DOMContentLoaded", function () {
    // Live character count
    function setupCharCounter(inputId) {
        const input = document.getElementById(inputId);
        const counter = document.createElement("div");
        counter.style.fontSize = "12px";
        counter.style.color = "#666";
        input.parentNode.insertBefore(counter, input.nextSibling);

        function update() {
            counter.textContent = input.value.length + " characters";
        }

        input.addEventListener("input", update);
        update();
    }

    setupCharCounter("title");
    setupCharCounter("description");

    // Confirm before submit
    const form = document.querySelector("form");
    form.addEventListener("submit", function (e) {
        if (!confirm("Are you sure you want to save the changes?")) {
            e.preventDefault();
        }
    });
});
</script>

</head>
<body>
    <div class="edit-quiz-container">
        <h2>Edit Quiz</h2>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form method="post" action="">
            <label for="title">Quiz Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($quiz['description']); ?></textarea>

            <h3>Questions</h3>
            <?php if (!empty($questions)) { ?>
                <?php foreach ($questions as $question) { ?>
                    <fieldset>
                        <legend>Question ID: <?php echo $question['id']; ?></legend>
                        <input type="hidden" name="questions[<?php echo $question['id']; ?>][id]" value="<?php echo $question['id']; ?>">

                        <label>Question Text:</label>
                        <textarea name="questions[<?php echo $question['id']; ?>][question_text]" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>

                        <label>Option A:</label>
                        <input type="text" name="questions[<?php echo $question['id']; ?>][option_a]" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>

                        <label>Option B:</label>
                        <input type="text" name="questions[<?php echo $question['id']; ?>][option_b]" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>

                        <label>Option C:</label>
                        <input type="text" name="questions[<?php echo $question['id']; ?>][option_c]" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>

                        <label>Option D:</label>
                        <input type="text" name="questions[<?php echo $question['id']; ?>][option_d]" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>

                        <label>Correct Option:</label>
                        <select name="questions[<?php echo $question['id']; ?>][correct_option]" required>
                            <option value="A" <?php if ($question['correct_option'] == 'A') echo 'selected'; ?>>Option A</option>
                            <option value="B" <?php if ($question['correct_option'] == 'B') echo 'selected'; ?>>Option B</option>
                            <option value="C" <?php if ($question['correct_option'] == 'C') echo 'selected'; ?>>Option C</option>
                            <option value="D" <?php if ($question['correct_option'] == 'D') echo 'selected'; ?>>Option D</option>
                        </select>

                        <br><br>
                        <a href="delete_question.php?question_id=<?php echo $question['id']; ?>&quiz_id=<?php echo $quiz_id; ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete Question</a>
                    </fieldset>
                <?php } ?>
            <?php } else { ?>
                <p>No questions found for this quiz.</p>
            <?php } ?>

            <input type="submit" value="Update Quiz and Questions">
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
