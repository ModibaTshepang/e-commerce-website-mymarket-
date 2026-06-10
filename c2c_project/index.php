<?php
include "includes/auth.php";
include "config/database.php";

if ($_SESSION["role_id"] != 2) {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION["user_id"];

if (isset($_GET["buy"])) {
    $product_id = $_GET["buy"];

    $product_query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $product_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);

        if ($product["stock"] <= 0) {
            $message = "This product is out of stock.";
        } else {
            $quantity = 1;
            $total_amount = $product["price"];
            $subtotal = $product["price"];

            $order_query = "INSERT INTO orders (user_id, total_amount, order_status)
                            VALUES (?, ?, 'Pending')";
            $stmt = mysqli_prepare($conn, $order_query);
            mysqli_stmt_bind_param($stmt, "id", $user_id, $total_amount);

            if (mysqli_stmt_execute($stmt)) {
                $order_id = mysqli_insert_id($conn);

                $item_query = "INSERT INTO order_items (order_id, product_id, quantity, subtotal)
                               VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $item_query);
                mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product_id, $quantity, $subtotal);

                if (mysqli_stmt_execute($stmt)) {
                    $stock_query = "UPDATE products SET stock = stock - 1 WHERE product_id = ?";
                    $stmt = mysqli_prepare($conn, $stock_query);
                    mysqli_stmt_bind_param($stmt, "i", $product_id);
                    mysqli_stmt_execute($stmt);

                    $message = "Order placed successfully!";
                } else {
                    $message = "Failed to add order item.";
                }
            } else {
                $message = "Failed to place order.";
            }
        }
    }
}

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))["total"];

$total_orders_query = "SELECT COUNT(*) AS total FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $total_orders_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_orders_result = mysqli_stmt_get_result($stmt);
$total_orders = mysqli_fetch_assoc($total_orders_result)["total"];

$total_spent_query = "SELECT SUM(total_amount) AS total FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $total_spent_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_spent_result = mysqli_stmt_get_result($stmt);
$total_spent_data = mysqli_fetch_assoc($total_spent_result);
$total_spent = $total_spent_data["total"] ?? 0;

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if (!empty($search)) {
    $search_term = "%" . $search . "%";

    $query = "
        SELECT 
            products.*, 
            users.first_name, 
            users.last_name,
            categories.category_name
        FROM products
        JOIN users ON products.user_id = users.user_id
        LEFT JOIN categories ON products.category_id = categories.category_id
        WHERE products.product_name LIKE ?
        OR products.description LIKE ?
        OR categories.category_name LIKE ?
        ORDER BY products.created_at DESC
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);

} else {
    $products = mysqli_query($conn, "
        SELECT 
            products.*, 
            users.first_name, 
            users.last_name,
            categories.category_name
        FROM products
        JOIN users ON products.user_id = users.user_id
        LEFT JOIN categories ON products.category_id = categories.category_id
        ORDER BY products.created_at DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/responsive.css">

<title>Customer Homepage</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f4f5fb;
    display:flex;
}

.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#2A4F8D,#cc4e00);
    color:white;
    padding:30px 20px;
    position:fixed;
}

.logo{
    margin-bottom:45px;
}

.logo img{
    width:180px;
}

.menu a{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px;
    margin-bottom:10px;
    border-radius:10px;
    transition:0.3s;
}

.menu a:hover{
    background:rgba(255,255,255,0.15);
}

.main-content{
    margin-left:260px;
    width:100%;
    padding:40px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:40px;
}

.header h1{
    font-size:40px;
    color:#222;
}

.header p{
    color:#666;
}

.user-box{
    background:white;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
    font-weight:bold;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:40px;
}

.stat-card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
}

.stat-card h2{
    font-size:40px;
    color:#2A4F8D;
}

.stat-card p{
    color:#666;
}

.search-section{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
    margin-bottom:40px;
}

.search-form{
    display:flex;
    gap:10px;
}

.search-form input{
    flex:1;
    padding:14px;
    border:1px solid #ccc;
    border-radius:12px;
}

.btn,
.clear-btn{
    background:#2A4F8D;
    color:white;
    border:none;
    padding:14px 25px;
    border-radius:12px;
    cursor:pointer;
    font-size:16px;
    text-decoration:none;
}

.clear-btn{
    background:#cc4e00;
}

.products-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.product-card{
    background:white;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
}

.product-card img{
    width:100%;
    height:220px;
    object-fit:cover;
}

.product-info{
    padding:20px;
}

.product-info h3{
    margin-bottom:10px;
}

.product-info p{
    color:#666;
    margin-bottom:10px;
    line-height:1.4;
}

.details{
    color:#444;
    font-size:14px;
    line-height:1.6;
    margin-bottom:10px;
}

.price{
    color:#2A4F8D;
    font-size:22px;
    font-weight:bold;
    margin:10px 0 15px;
}

.buy-btn{
    display:inline-block;
    background:#2A4F8D;
    color:white;
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
}

.out-of-stock{
    display:inline-block;
    background:#999;
    color:white;
    padding:10px 14px;
    border-radius:8px;
    font-weight:bold;
}

.message{
    background:#d4edda;
    color:#155724;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.empty{
    background:white;
    padding:25px;
    border-radius:15px;
    color:#666;
}

@media(max-width:900px){
    body{
        flex-direction:column;
    }

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main-content{
        margin-left:0;
    }

    .search-form{
        flex-direction:column;
    }

    .header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }
}
</style>
</head>

<body>

<div class="sidebar">

    <div class="logo">
        <img src="assets/images/Logo2.png" alt="Logo">
    </div>

    <div class="menu">
        <a href="index.php">Marketplace</a>
        <a href="my-orders.php">My Orders</a>
        <a href="#">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main-content">

    <div class="header">
        <div>
            <h1>
                Welcome back,
                <?php echo $_SESSION["first_name"]; ?>
            </h1>
            <p>Browse products from local sellers and support township businesses.</p>
        </div>

        <div class="user-box">
            CUSTOMER
        </div>
    </div>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="stats">

        <div class="stat-card">
            <h2><?php echo $total_products; ?></h2>
            <p>Available Products</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $total_orders; ?></h2>
            <p>My Orders</p>
        </div>

        <div class="stat-card">
            <h2>R<?php echo number_format($total_spent, 2); ?></h2>
            <p>Total Spent</p>
        </div>

    </div>

    <div class="search-section">
        <h2 style="margin-bottom:20px;">Search Marketplace</h2>

        <form method="GET" class="search-form">
            <input 
                type="text"
                name="search"
                placeholder="Search products, descriptions or categories..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit" class="btn">Search</button>
            <a href="index.php" class="clear-btn">Clear</a>
        </form>
    </div>

    <h2 style="margin-bottom:20px;">Marketplace Products</h2>

    <div class="products-grid">

        <?php if (mysqli_num_rows($products) == 0) : ?>
            <div class="empty">No products found.</div>
        <?php endif; ?>

        <?php while ($product = mysqli_fetch_assoc($products)) : ?>

            <div class="product-card">

                <img src="uploads/<?php echo $product['image']; ?>" alt="Product Image">

                <div class="product-info">

                    <h3><?php echo $product["product_name"]; ?></h3>

                    <p><?php echo $product["description"]; ?></p>

                    <div class="details">
                        Seller: <?php echo $product["first_name"] . " " . $product["last_name"]; ?><br>
                        Category: <?php echo $product["category_name"] ?? "Uncategorized"; ?><br>
                        Stock: <?php echo $product["stock"]; ?>
                    </div>

                    <div class="price">
                        R<?php echo $product["price"]; ?>
                    </div>

                    <?php if ($product["stock"] > 0) : ?>

                        <a 
                            href="index.php?buy=<?php echo $product['product_id']; ?>"
                            class="buy-btn"
                            oneclick="return confirm('Are you sure you want to buy this product?');"
                        >
                            Buy Now
                        </a>

                    <?php else : ?>

                        <span class="out-of-stock">Out of Stock</span>

                    <?php endif; ?>

                </div>
            </div>

        <?php endwhile; ?>

    </div>

</div>

</body>
</html>