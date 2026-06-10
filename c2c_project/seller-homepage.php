<?php

include "includes/auth.php";
include "config/database.php";

if ($_SESSION["role_id"] != 1) {
    header("Location: login.php");
    exit();
}   

$message = "";

if ($_SERVER["REQUEST_METHOD"]== "POST") {

    $product_name = trim($_POST["product_name"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $user_id = $_SESSION["user_id"];
    $image = $_FILES["image"]["name"];
    $tmp_name = $_FILES["image"]["tmp_name"];
    $stock=$_POST["stock"];

    $new_image_name = time() . "_" . $image;
    move_uploaded_file($tmp_name, "uploads/" . $new_image_name);

    $category_id=$_POST["category_id"];
    $query = "INSERT INTO products (user_id, category_id, product_name, description, price, image, stock)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt=mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iissdsi",
        $user_id,
        $category_id,
        $product_name,
        $description,
        $price,
        $new_image_name,
        $stock
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Product added successfully :)";
    } else {
        $message = "Error adding product :( ";
    }
}

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if (!empty($search)) {
    $search_term = "%" . $search . "%";

    $query = "SELECT * FROM products 
              WHERE user_id = ? 
              AND (product_name LIKE ? OR description LIKE ?)";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $products_result = mysqli_stmt_get_result($stmt);

} else {
    $query = "SELECT * FROM products WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $products_result = mysqli_stmt_get_result($stmt);
}

$categories=mysqli_query($conn, "SELECT * FROM categories");

$user_id = $_SESSION['user_id'];
$count_query = "SELECT COUNT(*) AS total_products from products WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

$query = "SELECT * FROM products WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$products_result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <title>Seller Homepage</title>

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
    font-size:30px;
    font-weight:bold;
    margin-bottom:50px;
}

.logo img{
    width:180px;
    margin-top:10px;
}

.menu a{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px;
    margin-bottom:5px;
    border-radius:10px;
    transition:0.3s;
}

.menu a:hover{
    background:rgba(255,255,255,0.1);
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

.user-box{
    background:white;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
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
    color:#3525ff;
}

.stat-card p{
    color:#666;
}

.upload-section{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
    margin-bottom:40px;
}

.upload-section h2{
    margin-bottom:20px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:14px;
    border:1px solid #ccc;
    border-radius:12px;
}

textarea{
    resize:none;
    height:120px;
}

.btn{
    background:#3525ff;
    color:white;
    border:none;
    padding:14px 25px;
    border-radius:12px;
    cursor:pointer;
    font-size:16px;
}

.btn:hover{
    opacity:0.9;
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

.price{
    color:#3525ff;
    font-size:22px;
    font-weight:bold;
    margin-top:10px;
}

.delete-btn{
    display:inline-block;
    margin-top:15px;
    background:red;
    color:white;
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
}

.message{
    background:#d4edda;
    color:#155724;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
}

.search-form input {
    flex: 1;
    padding: 14px;
    border: 1px solid #ccc;
    border-radius: 10px;
}

.search-form button,
.clear-btn {
    background: #3525ff;
    color: white;
    padding: 14px 20px;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
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

}

</style>

</head>
<body>
<div class="sidebar">

    <div class="logo">
        <img src="assets/images/Logo2.png" alt="Logo">
    </div>

    <div class="menu">

        <a href="#">Dashboard</a>
        <a href="#">Add Product</a>
        <a href="#">My Products</a>
        <a href="#">Orders</a>
        <a href="#">Messages</a>
        <a href="logout.php">
            Logout
        </a>

    </div>

</div>
<div class="main-content">

<div class="header">

    <div>
        <h1>
            Welcome back,
            <?php echo $_SESSION['first_name']; ?>
        </h1>
        <p>
            Here's what's happening with your store today.
        </p>
    </div>
    <div class="user-box">

        SELLER

    </div>

</div>

<div class="stats">

    <div class="stat-card">

        <h2>
            <?php echo $data['total_products']; ?>
        </h2>

        <p>Total Products</p>

    </div>

    <div class="stat-card">

        <h2>0</h2>

        <p>Total Orders</p>

    </div>

    <div class="stat-card">

        <h2>R0</h2>

        <p>Total Sales</p>

    </div>

</div>

<div class="upload-section">

    <h2>Add New Product</h2>

    <?php if(!empty($message)) : ?>

        <div class="message">

            <?php echo $message; ?>

        </div>

    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">

            <label>Product Name</label>

            <input 
                type="text"
                name="product_name"
                required
            >

        </div>

        <div class="form-group">

            <label>Description</label>

            <textarea 
                name="description"
                required
            ></textarea>

        </div>

        <div class="form-group">
            <label>Category</label>

            <select name="category_id" required>
                <option value="">Select Category</option>

                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?php echo $category['category_id']; ?>">
                        <?php echo $category['category_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

                <div class="form-group">
            <label>Stock</label>

            <input 
                type="number"
                name="stock"
                min="1"
                required
            >
        </div>

        <div class="form-group">

            <label>Price</label>

            <input 
                type="number"
                step="0.01"
                name="price"
                required
            >

        </div>

        <div class="form-group">

            <label>Product Image</label>

            <input 
                type="file"
                name="image"
                required
            >

        </div>

        <button type="submit" class="btn">

            Upload Product

        </button>

    </form>

</div>
<form method="GET" class="search-form">
    <input 
        type="text" 
        name="search" 
        placeholder="Search your products..."
        value="<?php echo htmlspecialchars($search); ?>"
    >

    <button type="submit">Search</button>

    <a href="seller-homepage.php" class="clear-btn">Clear</a>
</form>

<h2 style="margin-bottom:20px;">Your Products</h2>

<div class="products-grid">
<?php while($product = mysqli_fetch_assoc($products_result)) : ?>

    <div class="product-card">
    <img src="uploads/<?php echo $product['image']; ?>">

    <div class="product-info">

        <h3>
            <?php echo $product['product_name']; ?>
        </h3>

        <p>
            <?php echo $product['description']; ?>
        </p>

        <div class="price">

            R<?php echo $product['price']; ?>

        </div>

        <a 
            href="delete-product.php?id=<?php echo $product['product_id']; ?>"
            class="delete-btn"
            oneclick="return confirm('Are you sure you want to delete this product?');"
        >
            Delete
        </a>

    </div>

</div>

<?php endwhile; ?>
</div>
</div>
</body>
</html>