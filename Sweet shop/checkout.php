<?php
session_start();
include("config/db.php");

// Check customer login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$grandTotal = 0;

// Fetch customer details
$customerQuery = mysqli_query($conn,
"SELECT * FROM customer_detail
 WHERE customer_id='$customer_id'");

$customer = mysqli_fetch_assoc($customerQuery);

// Fetch cart items
$cartQuery = mysqli_query($conn,
"SELECT cart.cart_id,
        cart.quantity,
        sweets.sweet_id,
        sweets.sweet_name,
        sweets.price,
        sweets.image
 FROM cart
 INNER JOIN sweets
 ON cart.sweet_id = sweets.sweet_id
 WHERE cart.customer_id='$customer_id'");

if(mysqli_num_rows($cartQuery)==0){
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<h2 class="mb-4">Checkout</h2>

<div class="row">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Customer Details

</div>

<div class="card-body">

<p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>

<p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>

<p><strong>Mobile:</strong> <?php echo htmlspecialchars($customer['mobile_no']); ?></p>

<p><strong>Address:</strong><br>
<?php echo nl2br(htmlspecialchars($customer['address'])); ?>
</p>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

Order Summary

</div>

<div class="card-body">
    <table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>Sweet</th>
<th>Price</th>
<th>Qty</th>
<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php

while($item = mysqli_fetch_assoc($cartQuery))
{

    $subtotal = $item['price'] * $item['quantity'];

    $grandTotal += $subtotal;

?>

<tr>

<td>

<?php echo htmlspecialchars($item['sweet_name']); ?>

</td>

<td>

₹<?php echo number_format($item['price'],2); ?>

</td>

<td>

<?php echo $item['quantity']; ?>

</td>

<td>

₹<?php echo number_format($subtotal,2); ?>

</td>

</tr>

<?php

}

?>

</tbody>

<tfoot>

<tr>

<th colspan="3" class="text-end">

Grand Total

</th>

<th>

₹<?php echo number_format($grandTotal,2); ?>

</th>

</tr>

</tfoot>

</table>

<form action="place_order.php" method="POST">

<input
type="hidden"
name="total_amount"
value="<?php echo $grandTotal; ?>">

<div class="mb-3">

<label class="form-label">

Payment Method

</label>

<select
name="payment_method"
class="form-select"
required>

<option value="">Select Payment Method</option>

<option value="Cash on Delivery">
Cash on Delivery
</option>

<option value="UPI">
UPI
</option>

<option value="Credit Card">
Credit Card
</option>

<option value="Debit Card">
Debit Card
</option>

</select>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-success btn-lg">

Place Order

</button>

</div>

</form>

</div>

</div>

</div>

</div>
<div class="mt-4 d-flex justify-content-between">

<a href="cart.php" class="btn btn-secondary">
    ← Back to Cart
</a>

<a href="sweets.php" class="btn btn-warning">
    Continue Shopping
</a>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>