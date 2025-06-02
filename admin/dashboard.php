<?php
session_start();
include('../config/db.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle AJAX POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $quizId = intval($input['quiz_id'] ?? 0);

    if (!$quizId || !in_array($action, ['add', 'edit', 'delete', 'view'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        exit;
    }

    // Ensure quiz belongs to logged-in admin
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
    $stmt->execute([$quizId, $_SESSION['admin_id']]);
    $quiz = $stmt->fetch();

    if (!$quiz) {
        echo json_encode(['status' => 'error', 'message' => 'Quiz not found or unauthorized']);
        exit;
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$quizId]);
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Fetch only the quizzes created by this admin
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE created_by = ?");
$stmt->execute([$_SESSION['admin_id']]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <style>
       /* Background animated gradient */
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

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 40px;
    color: #333;

    /* Gradient background animation */
    background: linear-gradient(-45deg, #1e3c72, #2a5298, #1e3c72, #6dd5fa);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

/* Dashboard container - keep white background for readability */
.dashboard-container {
    max-width: 800px;
    margin: auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* Typography */
h2, h3 {
    text-align: center;
    color: #2c3e50;
}

/* Advanced button styles with animation */
a.button {
    display: inline-block;
    margin: 20px 0;
    padding: 12px 24px;
    background: linear-gradient(270deg, #007bff, #00d2ff, #007bff);
    background-size: 600% 600%;
    color: white;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    box-shadow: 0 0 10px rgba(0,123,255,0.6);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: buttonGradient 8s ease infinite;
    position: relative;
    overflow: hidden;
}

/* Button gradient animation */
@keyframes buttonGradient {
    0% {
        background-position: 0% 50%;
        box-shadow: 0 0 10px rgba(0,123,255,0.6);
    }
    50% {
        background-position: 100% 50%;
        box-shadow: 0 0 20px rgba(0,210,255,0.9);
    }
    100% {
        background-position: 0% 50%;
        box-shadow: 0 0 10px rgba(0,123,255,0.6);
    }
}

a.button:hover {
    transform: scale(1.08);
    box-shadow: 0 0 30px rgba(0,210,255,1);
    animation-play-state: paused; /* pause animation on hover for crisp effect */
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table thead {
    background-color: #f1f1f1;
}
table th, table td {
    padding: 12px 16px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

/* Action buttons */
.actions button {
    margin-right: 10px;
    padding: 6px 12px;
    background-color: #eee;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
    box-shadow: 0 0 5px transparent;
}
.actions button:hover {
    background-color: #ddd;
    box-shadow: 0 0 8px rgba(100, 100, 100, 0.3);
    transform: scale(1.05);
}

/* Logout link */
.logout {
    display: block;
    text-align: center;
    margin-top: 30px;
    font-size: 16px;
    color: #007bff;
    font-weight: 600;
    transition: color 0.3s ease;
}
.logout:hover {
    color: #0056b3;
}

/* Admin ID text */
.admin-id {
    text-align: center;
    margin-top: -20px;
    color: #555;
}

    </style>
</head>
<body>
<div class="dashboard-container">
    <h2>Admin Dashboard</h2>
    <p class="admin-id">Logged in as Admin ID: <?= $_SESSION['admin_id'] ?></p>
    <center><a href="create_quiz.php" class="button">Create New Quiz</a></center>

    <h3>Your Quizzes</h3>
    <?php if (count($quizzes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?= htmlspecialchars($quiz['title']) ?></td>
                    <td class="actions">
                        <button class="action-btn" data-action="add" data-id="<?= $quiz['id'] ?>">Add Questions</button>
                        <button class="action-btn" data-action="edit" data-id="<?= $quiz['id'] ?>">Edit</button>
                        <button class="action-btn" data-action="delete" data-id="<?= $quiz['id'] ?>">Delete</button>
                        <button class="action-btn" data-action="view" data-id="<?= $quiz['id'] ?>">View</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No quizzes found. Click "Create New Quiz" to get started!</p>
    <?php endif; ?>

    <a class="logout" href="logout.php">Log out</a>
</div>

<script>
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', async e => {
            const action = e.target.dataset.action;
            const quizId = e.target.dataset.id;

            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete this quiz?')) return;
            }

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ action, quiz_id: quizId })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    if (action === 'delete') {
                        alert('Quiz deleted successfully.');
                        location.reload();
                    } else if (action === 'edit') {
                        window.location.href = `edit_quiz.php?quiz_id=${quizId}`;
                    } else if (action === 'add') {
                        window.location.href = `add_question.php?quiz_id=${quizId}`;
                    } else if (action === 'view') {
                        window.location.href = `view_quiz.php?quiz_id=${quizId}`;
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                alert('An error occurred. Please try again.');
                console.error(err);
            }
        });
    });
</script>
</body>
</html>
