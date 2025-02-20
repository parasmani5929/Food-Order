<?php
session_start();
include 'db_connection.php';

// ✅ Fetch food items from database
$stmt = $pdo->query("SELECT * FROM food_items");
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Check if a search query exists
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT * FROM food_items WHERE name LIKE ?");
    $stmt->execute(["%$searchQuery%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM food_items");
}
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["food_id"], $_POST["quantity"])) {
    $food_id = $_POST["food_id"];
    $quantity = $_POST["quantity"];

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    if (isset($_SESSION["cart"][$food_id])) {
        $_SESSION["cart"][$food_id] += $quantity;
    } else {
        $_SESSION["cart"][$food_id] = $quantity;
    }

    // Refresh page to update cart instantly
    header("Location: index.php");
    exit;
}

// ✅ Handle Remove from Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_food_id"])) {
    $remove_food_id = $_POST["remove_food_id"];
    unset($_SESSION["cart"][$remove_food_id]);

    // Refresh page after removing item
    header("Location: index.php");
    exit;
}

// ✅ Get Cart Items
$cart_items = [];
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $food_id => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM food_items WHERE food_id = ?");
        $stmt->execute([$food_id]);
        $food = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($food) {
            $food["quantity"] = $quantity;
            $cart_items[] = $food;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Food Ordering</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f8f8f8; text-align: center; }
        header { display: flex; justify-content: space-between; align-items: center; background-color: #ffd700; padding: 15px; }
        nav ul { list-style: none; display: flex; }
        nav ul li { margin: 0 15px; }
        .search-bar input { padding: 5px; }
        .container { display: flex; justify-content: space-between; padding: 20px; }
        /* Container for food items */

        .food-card {
        background-color: white;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; /* Ensures all food cards have the same height */
    }

    .food-card img {
        width: 100%;
        height: 200px; /* Fixed height */
        object-fit: cover; /* Crops the image instead of stretching */
        border-radius: 5px;
    }

    .food-items {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 1000px;
        margin: 0 auto;
        align-items: start; /* Ensures all grid rows align properly */
    }
        .cart ul { list-style: none; padding: 0; }
        .cart ul li { display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; }
        .cart img { width: 50px; border-radius: 5px; }
        .cart button { background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .btn { padding: 10px 15px; background-color: green; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<header>
    <div class="logo">
    <img src="logo.jpg" alt="TuckIn Logo" width="100" height="100">
    </div>
    <nav>
        <ul>
          
            <li><a href="about.html" target="_blank">About us</a></li>
            <li><a href="contact.html">Contact us</a></li>
        </ul>
    </nav>

    <div class="search-bar">
    <form action="index.php" method="GET">
        <input type="text" name="search" placeholder="Search Food" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit">Search</button>
    </form>
    </div>

    <div class="auth-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>👤 Welcome, <?php echo $_SESSION['name']; ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="forget-pass.php">Forget password</a>
            <a href="register.html">Register</a>
            <a href="login.html">Login</a>
        <?php endif; ?>
    </div>
</header>

<section class="banner">
    <h1>Tuck In <span>On Time</span></h1>
</section>

<div class="container">
    <!-- ✅ Food Items Section -->
    <div class="food-items">
    <?php if (empty($food_items)): ?>
        <p>❌ No results found for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>" </p>
    <?php else: ?>
        
        <?php foreach ($food_items as $food): ?>
            <div class="food-card">
                <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                <h3><?php echo $food['name']; ?></h3>
                <p><?php echo $food['description']; ?></p>
                <p class="price">₹<?php echo $food['price']; ?></p>
                <form action="index.php" method="POST">
                    <input type="hidden" name="food_id" value="<?php echo $food['food_id']; ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit" class="btn">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ✅ Cart Section -->
    <div class="cart">
        <h2>🛒 Your Cart</h2>
        <?php if (!empty($cart_items)): ?>
            <ul>
                <?php foreach ($cart_items as $item): ?>
                    <li>
                        <img src="<?php echo $item['image']; ?>" width="50">
                        <?php echo $item['name']; ?> - ₹<?php echo $item['price']; ?> x <?php echo $item['quantity']; ?>
                        <form action="index.php" method="POST" style="display:inline;">
                            <input type="hidden" name="remove_food_id" value="<?php echo $item['food_id']; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total: ₹
                <?php echo array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart_items)); ?>
            </strong></p>
            <a href="checkout.php"><button>Proceed to Checkout</button></a>
        <?php else: ?>
            <p>🛒 Your cart is empty.</p>
        <?php endif; ?>
    </div>
</div>

<div>
    <a href="adminlogin.html">Admin login</a>
</div>

</body>
</html>
