<?php
include "includes/auth.php";
include "config/database.php";

if ($_SESSION["role_id"] != 2) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$query = "
SELECT 
    orders.order_id,
    orders.total_amount,
    orders.order_status,
    orders.created_at,
    products.product_name,
    products.image,
    order_items.quantity,
    order_items.subtotal
FROM orders
JOIN order_items ON orders.order_id = order_items.order_id
JOIN products ON order_items.product_id = products.product_id
WHERE orders.user_id = ?
ORDER BY orders.created_at DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f5fb;
            padding: 40px;
        }

        .navbar {
            background: #3525ff;
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .navbar a {
            color: white;
            margin-right: 20px;
            text-decoration: none;
            font-weight: bold;
        }

        .order-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        .order-card img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 12px;
        }

        .status {
            color: #3525ff;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Marketplace</a>
    <a href="logout.php">Logout</a>
</div>

<h1>My Orders</h1>

<?php if (mysqli_num_rows($orders) == 0) : ?>
    <p>You have not placed any orders yet.</p>
<?php endif; ?>

<?php while ($order = mysqli_fetch_assoc($orders)) : ?>

    <div class="order-card">
        <img src="uploads/<?php echo $order['image']; ?>">

        <div>
            <h2><?php echo $order["product_name"]; ?></h2>
            <p>Order ID: <?php echo $order["order_id"]; ?></p>
            <p>Quantity: <?php echo $order["quantity"]; ?></p>
            <p>Total: R<?php echo $order["total_amount"]; ?></p>
            <p>Status: <span class="status"><?php echo $order["order_status"]; ?></span></p>
            <p>Date: <?php echo $order["created_at"]; ?></p>
        </div>
    </div>

<?php endwhile; ?>

</body>
</html>