<?php
// user/register.php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    if ($stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_password])) {
        header('Location: login.php');
        exit;
    } else {
        $error = "An error occurred during registration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Registration</title>

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

    .register-container {
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

    input[type="text"],
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

    input[type="text"]:focus,
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

    .error, .js-error {
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

    /* Back to Home container */
    p.back-home {
        text-align: center;
        margin-top: 25px;
        animation: fadeInText 1.2s ease forwards;
        width: 100%;
        max-width: 360px;
        box-sizing: border-box;
    }

    p.back-home a {
        font-weight: 600;
        color: #007bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    p.back-home a:hover {
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

<body>
    <div class="register-container">
        <h2>User Registration</h2>
        <form id="registerForm" method="POST" action="">
            <input type="text" name="name" id="name" placeholder="Full Name" required />
            <input type="email" name="email" id="email" placeholder="Email" required />
            <input type="password" name="password" id="password" placeholder="Password" required />
            <button type="submit">Register</button>
        </form>

        <p id="jsError" class="js-error"></p>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <a href="login.php">Already have an account? Login</a>
    </div>

    <p class="back-home"><a href="../index.php">Back to Home</a></p>

<script>
    // Beginner level JS validation
    const form = document.getElementById('registerForm');
    const jsError = document.getElementById('jsError');

    form.addEventListener('submit', function(event) {
        jsError.textContent = ''; // Clear previous errors

        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;

        if (!name || !email || !password) {
            jsError.textContent = "Please fill out all fields.";
            event.preventDefault();
            return;
        }

        // Simple email regex validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            jsError.textContent = "Please enter a valid email address.";
            event.preventDefault();
            return;
        }
    });
</script>

</body>
</html>
