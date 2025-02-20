<?php
session_start();
include 'db_connection.php';  // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables for logged-in user
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        header("Location: index.html"); // Redirect to homepage
        exit;
    } else {
        echo "Invalid email or password. <a href='login.html'>Try again</a>";
    }
}
?>
