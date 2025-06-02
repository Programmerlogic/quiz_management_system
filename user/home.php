<?php
// index.php (User Home Page)
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Home</title>
    <style>
        /* Base body styles with animated gradient and blobs */
body {
    font-family: Arial, sans-serif;
    background-color: #f0f4f8;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    color: #333;
    animation: fadeInUp 1s ease-out;
    overflow: hidden;
}

/* Blurred animated background blobs */
.background-blobs {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: -1;
    overflow: hidden;
}

.background-blobs::before,
.background-blobs::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(120px);
    opacity: 0.6;
    animation: blobMove 25s ease-in-out infinite alternate;
}

.background-blobs::before {
    width: 400px;
    height: 400px;
    background: #007bff;
    top: 10%;
    left: 15%;
}

.background-blobs::after {
    width: 500px;
    height: 500px;
    background: #ff4081;
    bottom: 10%;
    right: 15%;
}

@keyframes blobMove {
    0% {
        transform: translate(0, 0) scale(1);
    }
    50% {
        transform: translate(60px, -40px) scale(1.1);
    }
    100% {
        transform: translate(-40px, 60px) scale(1);
    }
}

/* Container */
.home-container {
    background: white;
    padding: 40px 50px;
    border-radius: 10px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 90%;
    text-align: center;
    position: relative;
    z-index: 1;
    animation: fadeInUp 1.2s ease-out;
}

.home-container h1 {
    margin-bottom: 25px;
    color: #007bff;
}

/* Options */
.option-box {
    margin-top: 20px;
}

.option-box h2 {
    margin-bottom: 15px;
    font-weight: 600;
}

.options {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.option-btn {
    background-color: #007bff;
    color: white;
    padding: 14px 28px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    display: inline-block;
    min-width: 140px;
    text-align: center;
    animation: pulse 1.8s infinite ease-in-out;
}

.option-btn:hover {
    background-color: #0056b3;
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 15px rgba(0, 123, 255, 0.2);
}

/* Footer */
p {
    margin-top: 30px;
}

p a {
    color: #555;
    text-decoration: none;
    font-weight: 500;
}

p a:hover {
    text-decoration: underline;
}

/* Animations */
@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}


    </style>
</head>
<body>
    <div class="background-blobs"></div>

    <div class="home-container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        
        <div class="option-box">
            <h2>Choose an Option</h2>
            <div class="options">
                <a href="scores.php" class="option-btn">View Previous Scores</a>
                <a href="start_quiz.php" class="option-btn">Start a Quiz</a>
            </div>
        </div>
    </div>
    <p><a href="logout.php">Log out</a></p>
</body>
</html>
