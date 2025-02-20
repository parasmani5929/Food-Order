<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['email'], $_POST['password'])) {
        die("❌ Missing email or password. <a href='adminlogin.php'>Try again</a>");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ Check admin credentials
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        // ✅ Store admin details in session
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['adminname'];

        header("Location: admin_dashboard.php"); // Redirect to admin dashboard
        exit;
    } else {
        echo "❌ Invalid email or password. <a href='adminlogin.php'>Try again</a>";
    }
} else {
    header("Location: adminlogin.html"); // Redirect if accessed without form submission
    exit;
}
?>
