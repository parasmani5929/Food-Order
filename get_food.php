<?php
include 'db_connection.php'; // Include database connection

// Fetch all food items
$stmt = $pdo->query("SELECT * FROM food_items");
$foods = $stmt->fetchAll();

// Display food items dynamically
foreach ($foods as $food) {
    echo "<div class='food-card'>";
    echo "<img src='images/" . $food['image'] . "' alt='" . $food['name'] . "'>";
    echo "<h3>" . $food['name'] . "</h3>";
    echo "<p>" . $food['description'] . "</p>";
    echo "<p class='price'>₹" . $food['price'] . "</p>";
    echo "<button onclick='addToCart(" . $food['food_id'] . ")'>Add to Cart</button>";
    echo "</div>";
}
?>

