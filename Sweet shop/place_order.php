<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_POST['payment_method']) || !isset($_POST['total_amount'])) {
    header("Location: checkout.php");
    exit();
}

$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$total_amount = $_POST['total_amount'];

mysqli_begin_transaction($conn);

try {

    // You can change supplier_id if needed
    $supplier_id = 1;

    // Create Order
    mysqli_query($conn,
    "INSERT INTO `order`
    (customer_id,supplier_id,order_date,total_amount,order_status)
    VALUES
    ('$customer_id','$supplier_id',CURDATE(),'$total_amount','Pending')");

    $order_id = mysqli_insert_id($conn);

    // Get Cart Items
    $cart = mysqli_query($conn,
    "SELECT c.sweet_id,
            c.quantity,
            s.price
     FROM cart c
     INNER JOIN sweets s
     ON c.sweet_id=s.sweet_id
     WHERE c.customer_id='$customer_id'");

    while($row=mysqli_fetch_assoc($cart))
    {
        mysqli_query($conn,
        "INSERT INTO order_items
        (order_id,sweet_id,quantity,price)
        VALUES
        ('$order_id',
         '{$row['sweet_id']}',
         '{$row['quantity']}',
         '{$row['price']}')");
    }

    // Payment Entry
    mysqli_query($conn,
    "INSERT INTO payment
    (order_id,
     customer_id,
     payment_method,
     payment_status,
     payment_date,
     amount,
     transaction_id)

     VALUES

    ('$order_id',
     '$customer_id',
     '$payment_method',
     'Pending',
     CURDATE(),
     '$total_amount',
     '')");

    // Clear Cart
    mysqli_query($conn,
    "DELETE FROM cart
     WHERE customer_id='$customer_id'");

    mysqli_commit($conn);

    header("Location: my_orders.php?success=1");
    exit();

}
catch(Exception $e)
{
    mysqli_rollback($conn);

    echo "Order Failed : ".$e->getMessage();
}
?>


