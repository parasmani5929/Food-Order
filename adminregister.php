<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
        die("❌ Missing required fields. <a href='adminregister.html'>Try again</a>");
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    try {
        // ✅ Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            die("❌ Email already registered. <a href='adminregister.html'>Try again</a>");
        }

        // ✅ Insert admin into database
        $stmt = $pdo->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
        $success = $stmt->execute([$name, $email, $password]);

        if ($success) {
            echo "✅ Admin registered successfully! <a href='adminlogin.html'>Login here</a>";
        } else {
            echo "❌ Error in registration.";
        }

    } catch (PDOException $e) {
        die("❌ Database error: " . $e->getMessage());
    }
} else {
    header("Location: adminregister.html"); // Redirect if accessed without form submission
    exit;
}
?>

