<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Get Food ID from URL
if (!isset($_GET['id'])) {
    die("❌ Invalid request. <a href='manage_food.php'>Go back</a>");
}

$food_id = $_GET['id'];

// ✅ Fetch existing food data
$stmt = $pdo->prepare("SELECT * FROM food_items WHERE food_id = ?");
$stmt->execute([$food_id]);
$food = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$food) {
    die("❌ Food item not found. <a href='manage_food.php'>Go back</a>");
}

// ✅ Handle Form Submission to Update Food
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_path = $food['image']; // Keep old image by default

    // ✅ Check if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            die("❌ Failed to upload new image.");
        }
    }

    // ✅ Update Food in Database
    try {
        $stmt = $pdo->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, category = ?, image = ? WHERE food_id = ?");
        $stmt->execute([$name, $description, $price, $category, $image_path, $food_id]);

        echo "✅ Food updated successfully! <a href='manage_food.php'>Go back</a>";
    } catch (PDOException $e) {
        die("❌ Error updating food: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Food Item</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Edit Food Item</h1>
<form action="edit_food.php?id=<?php echo $food_id; ?>" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" value="<?php echo $food['name']; ?>" required>
    <input type="text" name="description" value="<?php echo $food['description']; ?>" required>
    <input type="number" name="price" value="<?php echo $food['price']; ?>" step="0.01" required>
    <input type="text" name="category" value="<?php echo $food['category']; ?>" required>
    <p>Current Image:</p>
    <img src="<?php echo $food['image']; ?>" width="100"><br>
    <input type="file" name="image">
    <button type="submit">Update Food</button>
</form>

<a href="manage_food.php">Back to Manage Food</a>

</body>
</html>
