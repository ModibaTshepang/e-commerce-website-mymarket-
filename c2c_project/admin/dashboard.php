<?php
include "../includes/auth.php";
include "../config/database.php";

if ($_SESSION["role_id"] != 3) {
    header("Location: ../login.php");
    exit();
}
$message = "";

if (isset($_POST["add_user"])) {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role_id = $_POST["role_id"];

    $query = "INSERT INTO users (first_name, last_name, email, password, role_id)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $email, $password, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        $message = "User added successfully.";
    } else {
        $message = "Error adding user.";
    }
}

if (isset($_POST["update_user"])) {
    $user_id = $_POST["user_id"];
    $role_id = $_POST["role_id"];

    $query = "UPDATE users SET role_id = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $role_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $message = "User role updated successfully.";
    } else {
        $message = "Error updating user.";
    }
}

if (isset($_GET["delete_user"])) {
    $user_id = $_GET["delete_user"];

    if ($user_id != $_SESSION["user_id"]) {
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = "User deleted successfully.";
        } else {
            $message = "Cannot delete user because they may have products or orders.";
        }
    } else {
        $message = "You cannot delete your own admin account.";
    }
}

if (isset($_GET["delete_product"])) {
    $product_id = $_GET["delete_product"];

    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Product deleted successfully.";
    } else {
        $message = "Error deleting product.";
    }
}

if (isset($_POST["update_order"])){
    $order_id = $_POST["order_id"];
    $order_status = $_POST["order_status"];

    $query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $order_status, $order_id);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Order status updated successfully.";
    } else {
        $message = "Error updating order status.";
    }
}

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))["total"];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))["total"];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))["total"];

$users = mysqli_query($conn, "SELECT users.*, roles.role_name 
                              FROM users 
                              JOIN roles ON users.role_id = roles.role_id");

$roles = mysqli_query($conn, "SELECT * FROM roles");

$products = mysqli_query($conn, "SELECT products.*, users.first_name, users.last_name 
                                 FROM products 
                                 JOIN users ON products.user_id = users.user_id");

$orders=mysqli_query($conn,"select
                                orders.order_id,
                                orders.total_amount,
                                orders.order_status,
                                orders.created_at,
                                users.first_name,
                                users.last_name,
                                products.product_name
                            from orders
                            join users on orders.user_id=users.user_id
                            join order_items on orders.order_id=order_items.order_id
                            join products on order_items.product_id=products.product_id
                            order by orders.created_at desc");


?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/responsive.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f5fb;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background:linear-gradient(180deg,#2A4F8D);
            color: white;
            padding: 30px 20px;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .main {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        .card h2 {
            color: #3525ff;
            font-size: 35px;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #3525ff;
            color: white;
        }

        input, select {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            background: #3525ff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        .delete {
            background: red;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">

    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION["first_name"]; ?></p>

    <br>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="cards">
        <div class="card">
            <h2><?php echo $total_users; ?></h2>
            <p>Total Users</p>
        </div>

        <div class="card">
            <h2><?php echo $total_products; ?></h2>
            <p>Total Products</p>
        </div>

        <div class="card">
            <h2><?php echo $total_orders; ?></h2>
            <p>Total Orders</p>
        </div>
    </div>

    <div class="section">
        <h2>Add New User</h2>

        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role_id" required>
                <option value="">Select Role</option>
                <option value="1">Seller</option>
                <option value="2">Customer</option>
                <option value="3">Admin</option>
            </select>

            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

<div class="section">
    <h2>Manage Orders</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Update</th>
        </tr>

        <?php while ($order = mysqli_fetch_assoc($orders)) : ?>
            <tr>
                <td><?php echo $order["order_id"]; ?></td>
                <td><?php echo $order["first_name"] . " " . $order["last_name"]; ?></td>
                <td><?php echo $order["product_name"]; ?></td>
                <td>R<?php echo $order["total_amount"]; ?></td>
                <td><?php echo $order["order_status"]; ?></td>
                <td><?php echo $order["created_at"]; ?></td>

                <td>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order["order_id"]; ?>">

                        <select name="order_status">
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>

                        <button type="submit" name="update_order">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<div class="section">
     <h2>Manage Users</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Current Role</th>
            <th>Update Role</th>
              <th>Delete</th>
            </tr>

            <?php while ($user = mysqli_fetch_assoc($users)) : ?>
                <tr>
                    <td><?php echo $user["user_id"]; ?></td>
                    <td><?php echo $user["first_name"] . " " . $user["last_name"]; ?></td>
                    <td><?php echo $user["email"]; ?></td>
                    <td><?php echo $user["role_name"]; ?></td>

                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user["user_id"]; ?>">

                            <select name="role_id">
                                <option value="1">Seller</option>
                                <option value="2">Customer</option>
                                <option value="3">Admin</option>
                            </select>

                            <button type="submit" name="update_user">Update</button>
                        </form>
                    </td>

                    <td>
                        <a class="delete" href="dashboard.php?delete_user=<?php echo $user["user_id"]; ?>">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="section">
        <h2>Manage Products</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Seller</th>
                <th>Price</th>
                <th>Delete</th>
            </tr>

            <?php while ($product = mysqli_fetch_assoc($products)) : ?>
                <tr>
                    <td><?php echo $product["product_id"]; ?></td>
                    <td><?php echo $product["product_name"]; ?></td>
                    <td><?php echo $product["first_name"] . " " . $product["last_name"]; ?></td>
                    <td>R<?php echo $product["price"]; ?></td>
                    <td>
                        <a class="delete" href="dashboard.php?delete_product=<?php echo $product["product_id"]; ?>">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>