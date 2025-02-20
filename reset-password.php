<?php
session_start();
include 'db_connection.php';

// ✅ Check if reset token is valid
if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['reset_token']) {
    die("❌ Invalid or expired reset link. <a href='forget-pass.php'>Try again</a>");
}

// ✅ Handle password reset
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    // ✅ Update password in database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$new_password, $email]);

    // ✅ Clear session data
    unset($_SESSION['reset_token'], $_SESSION['reset_email']);

    echo "✅ Password has been reset! <a href='login.html'>Login here</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Reset Your Password</h2>
    <form action="reset-password.php?token=<?php echo $_GET['token']; ?>" method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit">Update Password</button>
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>
