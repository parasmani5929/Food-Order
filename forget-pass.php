<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // ✅ Check if email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // ✅ Generate a unique reset token (for testing, we'll just use user_id)
        $token = md5($user['user_id'] . time());

        // ✅ Store token in session (for easier testing)
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;

        // ✅ Show reset link (Instead of sending email)
        echo "✅ Password reset link: <a href='reset-password.php?token=$token'>Click here to reset your password</a>";
    } else {
        echo "❌ Email not found. <a href='forget-pass.php'>Try again</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 50px;
        }
        .forgot-password-container {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 0 auto;
        }
        .forgot-password-container h2 {
            margin-bottom: 20px;
        }
        .forgot-password-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .forgot-password-container button {
            width: 100%;
            padding: 10px;
            background-color: #ffd700;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .forgot-password-container button:hover {
            background-color: #ffcc00;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <p>Please enter your email address to receive password reset instructions.</p>
        <form action="forget-pass.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p>Remembered your password? <a href="login.html">Login</a></p>
    </div>
</body>
</html>
