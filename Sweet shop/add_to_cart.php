<?php
session_start();
include("config/db.php");

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: sweets.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$sweet_id = (int)$_GET['id'];

// Check if item already exists
$check = mysqli_query($conn,
"SELECT * FROM cart
 WHERE customer_id='$customer_id'
 AND sweet_id='$sweet_id'");

if (mysqli_num_rows($check) > 0) {

    mysqli_query($conn,
    "UPDATE cart
     SET quantity = quantity + 1
     WHERE customer_id='$customer_id'
     AND sweet_id='$sweet_id'");

} else {

    mysqli_query($conn,
    "INSERT INTO cart(customer_id, sweet_id, quantity)
     VALUES('$customer_id','$sweet_id',1)");
}

header("Location: cart.php");
exit();
?>