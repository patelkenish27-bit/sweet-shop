<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['customer_id'];
$grandTotal = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shopping Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    
    

    <div class="container mt-5">
        <?php
        if (isset($_GET['msg']) && $_GET['msg'] == "cleared") {
            echo '<div class="alert alert-success">
            Cart cleared successfully.
          </div>';
        }
        ?>

        <h2 class="mb-4">🛒 Shopping Cart</h2>

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>
                        <th>Image</th>
                        <th>Sweet</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php

                    $sql = "SELECT cart.cart_id,
               cart.quantity,
               sweets.sweet_id,
               sweets.sweet_name,
               sweets.price,
               sweets.image
        FROM cart
        INNER JOIN sweets
        ON cart.sweet_id = sweets.sweet_id
        WHERE cart.customer_id='$user_id'";

                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {

                            $subtotal = $row['price'] * $row['quantity'];
                            $grandTotal += $subtotal;

                    ?>

                            <tr>

                                <td>

                                    <img
                                        src="uploads/sweets/<?php echo $row['image']; ?>"
                                        width="80"
                                        height="80"
                                        style="object-fit:cover;">

                                </td>

                                <td>

                                    <?php echo $row['sweet_name']; ?>

                                </td>

                                <td>

                                    ₹<?php echo number_format($row['price'], 2); ?>

                                </td>

                                <td>

                                    <div class="d-flex align-items-center">

                                        <a href="update_cart.php?action=decrease&id=<?php echo $row['cart_id']; ?>"
                                            class="btn btn-warning btn-sm">

                                            -

                                        </a>

                                        <span class="mx-3 fw-bold">

                                            <?php echo $row['quantity']; ?>

                                        </span>

                                        <a href="update_cart.php?action=increase&id=<?php echo $row['cart_id']; ?>"
                                            class="btn btn-success btn-sm">

                                            +

                                        </a>

                                    </div>

                                </td>



                                <td>

                                    ₹<?php echo number_format($subtotal, 2); ?>

                                </td>

                                <td>

                                    <a
                                        href="remove_cart.php?id=<?php echo $row['cart_id']; ?>"
                                        class="btn btn-danger btn-sm">

                                        Remove

                                    </a>

                                </td>

                            </tr>

                        <?php

                        }
                    } else {
                        ?>

                        <tr>

                            <td colspan="6" class="text-center">

                                Your cart is empty.

                            </td>

                        </tr>

                    <?php

                    }

                    ?>

                </tbody>

                <tfoot>

                    <tr>

                        <th colspan="4" class="text-end">

                            Grand Total

                        </th>

                        <th>

                            ₹<?php echo number_format($grandTotal, 2); ?>

                        </th>

                        <th></th>

                    </tr>

                </tfoot>

            </table>

            <div class="d-flex justify-content-between mt-4">

                <a href="sweets.php"
                    class="btn btn-secondary">

                    Continue Shopping

                </a>

                <div>

                    <a href="clear_cart.php"
                        class="btn btn-danger"
                        onclick="return confirm('Clear all cart items?');">

                        Clear Cart

                    </a>

                    <a href="checkout.php"
                        class="btn btn-primary">

                        Proceed to Checkout

                    </a>

                </div>

            </div>



        </div>

    </div>

</body>

</html>