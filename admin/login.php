<?php
// admin/login.php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(strtolower($_POST['email']));
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #eef2f7;
        padding: 40px 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        animation: bgFade 1.5s ease forwards;
    }

    .login-container {
        max-width: 360px;
        background: white;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateY(30px);
        animation: fadeSlideUp 0.8s ease forwards;
        width: 100%;
        box-sizing: border-box;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
        animation: fadeInText 1s ease forwards;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin: 8px 0 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 15px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        animation: fadeInText 1s ease forwards;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        animation: pulseColor 3s infinite ease-in-out;
    }

    button:hover {
        background-color: #0056b3;
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 86, 179, 0.6);
    }

    .error {
        color: red;
        margin-top: 12px;
        font-size: 14px;
        text-align: center;
        animation: fadeInText 1.2s ease forwards;
    }

    a {
        display: block;
        margin-top: 15px;
        text-align: center;
        text-decoration: none;
        color: #007bff;
        font-weight: 600;
        transition: color 0.3s ease;
        animation: fadeInText 1.2s ease forwards;
    }

    a:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    /* Keyframes */
    @keyframes bgFade {
        from { background-color: #eef2f7; }
        to { background-color: #d9e2ec; }
    }

    @keyframes fadeSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInText {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulseColor {
        0%, 100% {
            background-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.7);
        }
        50% {
            background-color: #3399ff;
            box-shadow: 0 0 18px rgba(51, 153, 255, 0.9);
        }
    }
</style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form id="loginForm" method="POST" action="">
            <input type="email" id="email" name="email" placeholder="Email" required />
            <input type="password" id="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <a href="register.php">Don't have an account? Register</a>
        <a href="../index.php">Back to Home</a>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            const email = emailInput.value.trim().toLowerCase();
            const password = passwordInput.value.trim();

            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                e.preventDefault(); // Stop form submission
                return;
            }

            // Modify input fields before submitting
            emailInput.value = email;
            passwordInput.value = password;
        });
    </script>
</body>
</html>
