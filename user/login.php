<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session and redirect to the quiz selection page
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: home.php');
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
   <style>
    /* Body background with subtle gradient and floating quiz symbols */
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #eef2f7, #dce6f0);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        overflow: hidden;
        position: relative;
    }

    /* Animated floating quiz symbols */
    .symbol {
        position: absolute;
        font-size: 36px;
        font-weight: 900;
        color: rgba(0, 0, 0, 0.08);
        animation: floatSymbol 12s infinite ease-in-out;
        user-select: none;
        pointer-events: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }

    @keyframes floatSymbol {
        0% {
            transform: translateY(0) scale(1);
            opacity: 0.08;
        }
        50% {
            transform: translateY(-25px) scale(1.15) rotate(10deg);
            opacity: 0.15;
        }
        100% {
            transform: translateY(0) scale(1);
            opacity: 0.08;
        }
    }

    /* Login box with slide and fade animation */
    .login-container {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(6px);
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        animation: fadeSlideIn 1s ease-out;
        z-index: 1;
    }

    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-container h2 {
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }

    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        transition: border 0.3s;
    }

    input:focus {
        border-color: #007bff;
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: #0056b3;
        transform: scale(1.03);
    }

    .error {
        color: red;
        margin-top: 10px;
        text-align: center;
    }

    a {
        display: block;
        margin-top: 15px;
        text-align: center;
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>
    <div class="login-container">
        <!-- Floating background quiz symbols -->
<span class="symbol" style="top: 10%; left: 15%;">?</span>
<span class="symbol" style="top: 30%; left: 70%;">✔</span>
<span class="symbol" style="top: 60%; left: 25%;">✖</span>
<span class="symbol" style="top: 80%; left: 80%;">💡</span>
<span class="symbol" style="top: 50%; left: 50%;">★</span>

        <h2>User Login</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Server-side error -->
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <a href="register.php">Don't have an account? Register</a>
        <a href="../index.php">← Back to Home</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.querySelector('form');
            var emailInput = form.elements['email'];
            var passwordInput = form.elements['password'];

            form.addEventListener('submit', function (event) {
                var oldError = document.querySelector('.client-error');
                if (oldError) oldError.remove();

                var email = emailInput.value.trim();
                var password = passwordInput.value.trim();
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                var errorMessage = document.createElement('p');
                errorMessage.className = 'error client-error';

                if (!email || !password) {
                    errorMessage.textContent = 'Both fields are required.';
                    form.parentNode.appendChild(errorMessage);
                    event.preventDefault();
                } else if (!emailPattern.test(email)) {
                    errorMessage.textContent = 'Please enter a valid email address.';
                    form.parentNode.appendChild(errorMessage);
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
