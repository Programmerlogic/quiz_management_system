<?php
// admin/register.php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'Email is already registered.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)");

    if ($stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_password])) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred during registration.']);
    }
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../assets/style.css" />
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
</head>
<body>
    <div class="register-container">
        <h2>Admin Registration</h2>
        <form id="registerForm" novalidate>
            <input type="text" name="name" id="name" placeholder="Full Name" required />
            <input type="email" name="email" id="email" placeholder="Email" required />
            <input type="password" name="password" id="password" placeholder="Password" required />
            <button type="submit">Register</button>
        </form>
        <p id="message"></p>
        <a href="login.php">Already have an account? Login</a>
    </div>
    <p><a href="../index.php">Back to Home</a></p>

    <script>
    const form = document.getElementById('registerForm');
    const message = document.getElementById('message');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();  // Prevent default form submit

        // Clear previous messages
        message.textContent = '';
        message.className = '';

        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;

        if (!name || !email || !password) {
            message.textContent = 'All fields are required.';
            message.className = 'error';
            return;
        }

        // Email regex validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            message.textContent = 'Please enter a valid email address.';
            message.className = 'error';
            return;
        }

        try {
            const response = await fetch('', {  // empty string '' means same page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                message.textContent = 'Registration successful! Redirecting to login...';
                message.className = 'success';

                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                message.textContent = data.error || 'Registration failed.';
                message.className = 'error';
            }
        } catch (err) {
            message.textContent = 'An error occurred. Please try again.';
            message.className = 'error';
        }
    });
</script>

</body>
</html>
