<?php
include 'db_connection.php';  // Include database connection

// ✅ Fix: Check if $_SERVER['REQUEST_METHOD'] is set before accessing it
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {

    // ✅ Fix: Check if required form fields exist before accessing them
    if (!isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'], $_POST['address'])) {
        die("❌ Missing required form fields. <a href='register.html'>Try again</a>");
    }

    // Retrieve user input (trim to remove unnecessary spaces)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        // ✅ Fix: Use COUNT(*) instead of fetching all data (More efficient)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            die("❌ Email already registered. <a href='register.html'>Try again</a>");
        }

        // ✅ Fix: Use named placeholders for better readability
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (:name, :email, :password, :phone, :address)");
        $success = $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':phone' => $phone,
            ':address' => $address
        ]);

        if ($success) {
            echo "✅ Registration successful! <a href='login.html'>Login here</a>";
        } else {
            echo "❌ Error in registration. Please try again.";
        }

    } catch (PDOException $e) {
        die("❌ Database error: " . $e->getMessage());
    }
} else {
    // ✅ Fix: Show an error if accessed directly in the browser
    die("❌ Invalid request. Please submit the form properly.");
}
?>
