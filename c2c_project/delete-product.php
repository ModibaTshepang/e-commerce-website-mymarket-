<?php

include "includes/auth.php";
include "config/database.php";

if ($_SESSION['role_id'] != 1) {

    header("Location: login.php");
    exit();

}

if (isset($_GET['id'])) {

    $product_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM products
    WHERE product_id = ?
    AND user_id = ?";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $product_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

}

header("Location: seller-homepage.php");

exit();

?>