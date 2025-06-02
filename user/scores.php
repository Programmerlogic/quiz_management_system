<?php
session_start();
include('../config/db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch previous quiz scores from user_results
$stmt = $pdo->prepare("SELECT ur.id, ur.score, ur.total, ur.submitted_at, q.title AS quiz_title
                       FROM user_results ur
                       JOIN quizzes q ON ur.quiz_id = q.id
                       WHERE ur.user_id = :user_id
                       ORDER BY ur.submitted_at DESC");
$stmt->execute(['user_id' => $user_id]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Scores</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
        }

        .scores-container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            text-align: center;
            margin-top: 25px;
            font-size: 16px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="scores-container">
        <h1>Your Scores</h1>
        <?php if ($scores): ?>
            <table>
                <thead>
                    <tr>
                        <th>Serial No.</th>
                        <th>Date Attempted</th>
                        <th>Quiz Title</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; ?>
                    <?php foreach ($scores as $score): ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($score['submitted_at']))); ?></td>
                            <td><?php echo htmlspecialchars($score['quiz_title']); ?></td>
                            <td><?php echo htmlspecialchars($score['score']); ?> / <?php echo htmlspecialchars($score['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not attempted any quizzes yet.</p>
        <?php endif; ?>
    </div>
    <p><a href="home.php">Back to Home</a></p>
</body>
</html>
