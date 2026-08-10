<?php
session_start();
include("config/db.php");

// Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Check parameters
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: cart.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_id = (int)$_GET['id'];
$action = $_GET['action'];

// Verify cart item belongs to logged-in customer
$check = mysqli_query($conn,
"SELECT * FROM cart
 WHERE cart_id='$cart_id'
 AND customer_id='$customer_id'");

if (mysqli_num_rows($check) == 0) {
    header("Location: cart.php");
    exit();
}

$item = mysqli_fetch_assoc($check);

if ($action == "increase") {

    mysqli_query($conn,
    "UPDATE cart
     SET quantity = quantity + 1
     WHERE cart_id='$cart_id'");

} elseif ($action == "decrease") {

    if ($item['quantity'] > 1) {

        mysqli_query($conn,
        "UPDATE cart
         SET quantity = quantity - 1
         WHERE cart_id='$cart_id'");

    } else {

        mysqli_query($conn,
        "DELETE FROM cart
         WHERE cart_id='$cart_id'");

    }
}

header("Location: cart.php");
exit();
?>