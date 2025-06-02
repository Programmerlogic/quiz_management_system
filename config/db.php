<?php
// config/db.php
$host = 'localhost';     // Database host
$dbname = 'quiz_system'; // Database name
$username = 'root';      // Database username
$password = '';          // Database password (change accordingly)

try {
    // Create a PDO instance and set error mode to exception for debugging
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}
?>
