<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied.");
}

// ✅ Get Food ID from URL
if (!isset($_GET['id'])) {
    die("❌ Invalid request.");
}

$food_id = $_GET['id'];

// ✅ Delete Food Item
try {
    $stmt = $pdo->prepare("DELETE FROM food_items WHERE food_id = ?");
    $stmt->execute([$food_id]);

    echo "✅ Food item deleted successfully! <a href='manage_food.php'>Go back</a>";
} catch (PDOException $e) {
    die("❌ Error deleting food: " . $e->getMessage());
}
?>
