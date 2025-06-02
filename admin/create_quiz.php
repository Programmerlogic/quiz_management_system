<?php
// admin/create_quiz.php
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Insert quiz into the database
    $stmt = $pdo->prepare("INSERT INTO quizzes (title, description, created_by) VALUES (:title, :description, :created_by)");
    if ($stmt->execute(['title' => $title, 'description' => $description, 'created_by' => $_SESSION['admin_id']])) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "An error occurred while creating the quiz.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .create-quiz-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .create-quiz-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .create-quiz-container form input[type="text"],
        .create-quiz-container form textarea {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border 0.3s ease;
            font-size: 16px;
        }

        .create-quiz-container form input:focus,
        .create-quiz-container form textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .create-quiz-container form button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .create-quiz-container form button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .create-quiz-container a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }

        .create-quiz-container a:hover {
            text-decoration: underline;
        }

        #feedback {
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="create-quiz-container">
        <h2>Create New Quiz</h2>
        <form id="createQuizForm" method="POST" action="">
            <input type="text" name="title" placeholder="Quiz Title" required>
            <textarea name="description" placeholder="Quiz Description" required></textarea>
            <button type="submit">Create Quiz</button>
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
        <div id="feedback"></div>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>

    <script>
    // Run this code when the page has finished loading
    window.onload = function () {
        var form = document.getElementById('createQuizForm');
        var feedback = document.getElementById('feedback');

        form.onsubmit = function (event) {
            // Get input fields
            var title = form.elements['title'].value.trim();
            var description = form.elements['description'].value.trim();

            // Clear old feedback
            feedback.innerHTML = "";

            // Check if fields are empty
            if (title === "" || description === "") {
                // Stop form from submitting
                event.preventDefault();

                // Show error message
                feedback.innerHTML = "<p style='color: red;'>Please fill in all fields before submitting.</p>";
            }
        };
    };
</script>

</body>
</html>
