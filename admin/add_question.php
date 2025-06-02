<?php
session_start();
include('../config/db.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get quiz ID from URL
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Check if quiz belongs to admin
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->execute([$quiz_id, $_SESSION['admin_id']]);
$quiz = $stmt->fetch();

if (!$quiz) {
    echo "<p>Invalid quiz or unauthorized access.</p>";
    exit;
}

// Handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $required = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option'];
    foreach ($required as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO questions 
        (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $quiz_id,
        $data['question_text'],
        $data['option_a'],
        $data['option_b'],
        $data['option_c'],
        $data['option_d'],
        $data['correct_option']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Question added successfully!']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Add Question to Quiz</title>
    <style>
       /* Background animated gradient */
@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

body {
    font-family: Arial, sans-serif;
   background: linear-gradient(-45deg, #ff6a00, #ee0979, #ff6a00, #ee0979);



    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    padding: 40px;
}


body {
    font-family: Arial, sans-serif;
    background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #ff9a9e, #fad0c4);


    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    padding: 40px;
}

/* Container styling */
.add-question-container {
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.add-question-container:hover {
    box-shadow: 0 6px 30px rgba(0,0,0,0.15);
}

/* Form elements */
textarea, input, select, button {
    width: 100%;
    margin-top: 10px;
    padding: 10px;
    font-size: 16px;
    border: 1.5px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Input focus animation */
textarea:focus, input:focus, select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
}

/* Button styling with animation */
button {
    background-color:rgb(231, 95, 95);
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 15px;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.4s ease, transform 0.2s ease, box-shadow 0.3s ease;
}

/* Button hover with scale and glow */
button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
    box-shadow: 0 0 12px rgba(0, 86, 179, 0.8);
}

/* Error and success message colors */
.error-message {
    color: #e74c3c;
    margin-top: 10px;
    font-weight: 600;
}

.success-message {
    color: #27ae60;
    margin-top: 10px;
    font-weight: 600;
}

/* Link styling */
a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #007bff;
    font-weight: 600;
    transition: color 0.3s ease;
}

a:hover {
    text-decoration: underline;
    color: #0056b3;
}

    </style>
</head>
<body>
    <div class="add-question-container">
        <h2>Add Question to Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>
        <form id="questionForm">
            <textarea name="question_text" placeholder="Enter question text" required></textarea>
            <input type="text" name="option_a" placeholder="Option A" required>
            <input type="text" name="option_b" placeholder="Option B" required>
            <input type="text" name="option_c" placeholder="Option C" required>
            <input type="text" name="option_d" placeholder="Option D" required>
            <select name="correct_option" required>
                <option value="">Select Correct Option</option>
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
            </select>
            <button type="submit">Add Question</button>
        </form>

        <div id="feedback"></div>
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>

   <script>
    // Get the form and feedback elements
    var form = document.getElementById('questionForm');
    var feedback = document.getElementById('feedback');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Stop the form from submitting normally

        // Get values from the form inputs
        var question = form.elements['question_text'].value.trim();
        var optionA = form.elements['option_a'].value.trim();
        var optionB = form.elements['option_b'].value.trim();
        var optionC = form.elements['option_c'].value.trim();
        var optionD = form.elements['option_d'].value.trim();
        var correctOption = form.elements['correct_option'].value;

        // Check if any field is empty
        if (!question || !optionA || !optionB || !optionC || !optionD || !correctOption) {
            feedback.innerHTML = '<p class="error-message">All fields are required.</p>';
            return;
        }

        // Prepare data to send
        var data = {
            question_text: question,
            option_a: optionA,
            option_b: optionB,
            option_c: optionC,
            option_d: optionD,
            correct_option: correctOption
        };

        // Send the data to the server using fetch
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if (result.status === 'success') {
                feedback.innerHTML = '<p class="success-message">' + result.message + '</p>';
                form.reset(); // Clear the form
            } else {
                feedback.innerHTML = '<p class="error-message">' + result.message + '</p>';
            }
        })
        .catch(function(error) {
            feedback.innerHTML = '<p class="error-message">Something went wrong. Please try again.</p>';
        });
    });
</script>

</body>
</html>
