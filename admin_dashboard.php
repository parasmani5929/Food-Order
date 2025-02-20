<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.html'>Admin Login</a>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Welcome,Surya G<?php echo $_SESSION['admin_name']; ?>!</h1>
<p>This is the admin dashboard.It is only accessible to Paras,Rasha,Surya and Saurav.</p>

<ul>
    <li><a href="manage_orders.php">Manage Orders</a></li>
    <li><a href="manage_food.php">Manage Food Items</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>

</body>
</html>
