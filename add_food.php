<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Handle form submission to add food item
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['category'], $_FILES['image'])) {
        die("❌ Missing required fields. <a href='manage_food.php'>Try again</a>");
    }

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    // ✅ Validate and upload image
    $target_dir = "uploads/"; // Ensure this folder is writable
    $target_file = $target_dir . basename($image["name"]);
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $image_path = $target_file;
    } else {
        die("❌ Failed to upload image.");
    }

    // ✅ Insert food item into database
    try {
        $stmt = $pdo->prepare("INSERT INTO food_items (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $category, $image_path]);

        echo "✅ Food item added successfully! <a href='manage_food.php'>Go back to manage food</a>";
    } catch (PDOException $e) {
        die("❌ Error inserting food item: " . $e->getMessage());
    }
} else {
    die("❌ Invalid request. <a href='manage_food.php'>Go back</a>");
}
?>
