<?php
session_start();
include("config/db.php");

// Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Check cart ID
if (!isset($_GET['id'])) {
    header("Location: cart.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_id = (int)$_GET['id'];

// Delete only the logged-in customer's cart item
$sql = "DELETE FROM cart
        WHERE cart_id='$cart_id'
        AND customer_id='$customer_id'";

mysqli_query($conn, $sql);

// Redirect back to cart
header("Location: cart.php");
exit();
?>