<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check order ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$orderQuery = mysqli_query($conn,
    "SELECT * FROM orders WHERE order_id='$order_id'");

if (mysqli_num_rows($orderQuery) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($orderQuery);

// Get ordered items
$itemQuery = mysqli_query($conn,
    "SELECT order_items.*,
            sweets.sweet_name,
            sweets.image
     FROM order_items
     INNER JOIN sweets
     ON order_items.sweet_id = sweets.sweet_id
     WHERE order_items.order_id='$order_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Order Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">

<h3 class="mb-0">
Order #<?php echo $order['order_id']; ?>
</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<h5>Customer Details</h5>

<p><strong>Name:</strong>
<?php echo htmlspecialchars($order['customer_name']); ?></p>

<p><strong>Email:</strong>
<?php echo htmlspecialchars($order['email']); ?></p>

<p><strong>Phone:</strong>
<?php echo htmlspecialchars($order['phone']); ?></p>

<p><strong>Address:</strong><br>
<?php echo nl2br(htmlspecialchars($order['address'])); ?></p>

</div>

<div class="col-md-6">

<h5>Order Information</h5>

<p><strong>Order Date:</strong>
<?php echo $order['order_date']; ?></p>

<p><strong>Total Amount:</strong>
₹<?php echo number_format($order['total_amount'],2); ?></p>

<p><strong>Payment Method:</strong>
<?php echo htmlspecialchars($order['payment_method']); ?></p>

<p><strong>Payment Status:</strong>
<?php echo htmlspecialchars($order['payment_status']); ?></p>

<p><strong>Order Status:</strong>
<?php echo htmlspecialchars($order['order_status']); ?></p>

</div>

</div>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4 class="mb-0">
Ordered Items
</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Image</th>
<th>Sweet Name</th>
<th>Price</th>
<th>Quantity</th>
<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php

while($item = mysqli_fetch_assoc($itemQuery))
{

    $subtotal = $item['price'] * $item['quantity'];

?>

<tr>

<td>

<img
src="../../uploads/sweets/<?php echo htmlspecialchars($item['image']); ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>

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

<th colspan="4" class="text-end">

Grand Total

</th>

<th>

₹<?php echo number_format($order['total_amount'],2); ?>

</th>

</tr>

</tfoot>

</table>

</div>

<hr>

<h4 class="mb-3">Update Order Status</h4>

<form action="update_status.php" method="POST">

<input
type="hidden"
name="order_id"
value="<?php echo $order['order_id']; ?>">

<div class="row">

<div class="col-md-4">

<label class="form-label">Order Status</label>

<select
name="order_status"
class="form-select"
required>

<option value="Pending"
<?php if($order['order_status']=="Pending") echo "selected"; ?>>
Pending
</option>

<option value="Processing"
<?php if($order['order_status']=="Processing") echo "selected"; ?>>
Processing
</option>

<option value="Delivered"
<?php if($order['order_status']=="Delivered") echo "selected"; ?>>
Delivered
</option>

<option value="Cancelled"
<?php if($order['order_status']=="Cancelled") echo "selected"; ?>>
Cancelled
</option>

</select>

</div>

<div class="col-md-4">

<label class="form-label">Payment Status</label>

<select
name="payment_status"
class="form-select"
required>

<option value="Pending"
<?php if($order['payment_status']=="Pending") echo "selected"; ?>>
Pending
</option>

<option value="Paid"
<?php if($order['payment_status']=="Paid") echo "selected"; ?>>
Paid
</option>

</select>

</div>

<div class="col-md-4 d-flex align-items-end">

<button
type="submit"
class="btn btn-primary w-100">

Update Status

</button>

</div>

</div>

</form>

<hr>

<div class="mt-4">

<a href="view.php"
class="btn btn-secondary">

Back to Orders

</a>

<a href="invoice.php?id=<?php echo $order['order_id']; ?>"
class="btn btn-success">

Print Invoice

</a>

</div>

</div>

</div>

</div>

</body>

</html>

