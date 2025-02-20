<?php
session_start();
include 'db_connection.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to place an order. <a href='login.html'>Login here</a>");
}

// ✅ Ensure cart is not empty
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    die("🛒 Your cart is empty. <a href='index.php'>Go back to menu</a>");
}

// ✅ Calculate Total Price
$total_price = 0;
foreach ($_SESSION["cart"] as $food_id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM food_items WHERE food_id = ?");
    $stmt->execute([$food_id]);
    $food = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($food) {
        $total_price += $food["price"] * $quantity;
    }
}

// ✅ Insert Order into `orders` table
try {
    $pdo->beginTransaction();

    // ✅ Insert only one record into `orders` table
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, order_date, status) VALUES (?, ?, NOW(), 'Pending')");
    $stmt->execute([$_SESSION["user_id"], $total_price]);
    $order_id = $pdo->lastInsertId(); // ✅ Get the auto-generated `order_id`

    // ✅ Insert multiple food items into `order_items`
    foreach ($_SESSION["cart"] as $food_id => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM food_items WHERE food_id = ?");
        $stmt->execute([$food_id]);
        $food = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $food_id, $quantity, $food["price"]]);
    }

    $pdo->commit();

    // ✅ Clear cart after order placement
    unset($_SESSION["cart"]);

    echo "✅ Order placed successfully! Your food will be prepared within 10 minutes. <a href='index.php'>Go back to home</a>";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Error processing order: " . $e->getMessage());
}
?>
