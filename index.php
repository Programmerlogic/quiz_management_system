<?php
// index.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Home - Quiz System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
            background: radial-gradient(circle at 20% 30%, rgba(0, 123, 255, 0.4) 0%, transparent 60%),
                        radial-gradient(circle at 80% 70%, rgba(255, 64, 129, 0.4) 0%, transparent 60%),
                        radial-gradient(circle at 50% 50%, rgba(0, 201, 167, 0.3) 0%, transparent 60%);
            animation: backgroundMove 20s ease-in-out infinite alternate;
            background-size: 200% 200%;
            background-position: center;
        }

        @keyframes backgroundMove {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }

        .quiz-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
            pointer-events: none;
            background: radial-gradient(circle, rgba(0, 123, 255, 0.1) 0%, transparent 70%);
        }

        .symbol {
            position: absolute;
            font-size: 40px;
            font-weight: bold;
            color: rgba(0, 123, 255, 0.2);
            animation: floatSymbol 10s infinite ease-in-out;
            user-select: none;
        }

        .symbol:nth-child(1) { top: 10%; left: 20%; animation-delay: 0s; }
        .symbol:nth-child(2) { top: 30%; left: 70%; animation-delay: 2s; }
        .symbol:nth-child(3) { top: 60%; left: 40%; animation-delay: 4s; }
        .symbol:nth-child(4) { top: 80%; left: 10%; animation-delay: 6s; }
        .symbol:nth-child(5) { top: 50%; left: 85%; animation-delay: 8s; }
        .symbol:nth-child(6) { top: 15%; left: 55%; animation-delay: 1s; }
        .symbol:nth-child(7) { top: 70%; left: 25%; animation-delay: 3s; }
        .symbol:nth-child(8) { top: 40%; left: 90%; animation-delay: 5s; }
        .symbol:nth-child(9) { top: 25%; left: 10%; animation-delay: 7s; }
        .symbol:nth-child(10) { top: 65%; left: 60%; animation-delay: 9s; }

        @keyframes floatSymbol {
            0% { transform: translateY(0) scale(1); opacity: 0.2; }
            50% { transform: translateY(-30px) scale(1.2); opacity: 0.4; }
            100% { transform: translateY(0) scale(1); opacity: 0.2; }
        }

        .home-container {
            background: white;
            padding: 40px 50px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        h1 {
            margin-bottom: 30px;
            color: #333;
        }

        p {
            font-size: 16px;
            margin: 15px 0;
            color: #555;
        }

        a {
            color: #0056b3;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #003d80;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="animated-background"></div>
    <div class="quiz-background">
        <span class="symbol">?</span>
        <span class="symbol">✔</span>
        <span class="symbol">✖</span>
        <span class="symbol">★</span>
        <span class="symbol">💡</span>
        <span class="symbol">?</span>
        <span class="symbol">✔</span>
        <span class="symbol">✖</span>
        <span class="symbol">★</span>
        <span class="symbol">💡</span>
    </div>

    <div class="home-container">
        <h1>Welcome to the Quiz System</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            <a href="user/logout.php">Logout</a>
        <?php elseif (isset($_SESSION['admin_id'])): ?>
            <p>Welcome, Admin!</p>
            <a href="admin/logout.php">Logout</a>
        <?php else: ?>
            <p><a href="user/login.php">User Login</a> | <a href="user/register.php">User Register</a></p>
            <p><a href="admin/login.php">Admin Login</a> | <a href="admin/register.php">Admin Register</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
