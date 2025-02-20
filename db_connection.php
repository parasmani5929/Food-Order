<?php
$host = "localhost";  // Change if MySQL runs on a different port
$username = "#";   // Your MySQL username
$password = "#";       // Your MySQL password (leave empty if no password)
$dbname = "food_ordering"; // Your database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ ";
} catch (PDOException $e) {
    die("❌.. Database connection failed: " . $e->getMessage());
}
?>
