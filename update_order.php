<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied.");
}

// ✅ Check if order_id is received
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["order_id"])) {
    $order_id = $_POST["order_id"];

    // ✅ Update order status to "Completed"
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Completed' WHERE order_id = ?");
    $stmt->execute([$order_id]);

    header("Location: manage_orders.php"); // Redirect back to orders page
    exit;
} else {
    die("❌ Invalid request.");
}
?>
