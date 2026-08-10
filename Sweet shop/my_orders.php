<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders
        WHERE user_id='$user_id'
        ORDER BY order_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Orders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<h2 class="mb-4">
My Orders
</h2>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Order ID</th>
<th>Date</th>
<th>Total</th>
<th>Payment</th>
<th>Payment Status</th>
<th>Order Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0)
{

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

#<?php echo $row['order_id']; ?>

</td>

<td>

<?php echo $row['order_date']; ?>

</td>

<td>

₹<?php echo number_format($row['total_amount'],2); ?>

</td>

<td>

<?php echo $row['payment_method']; ?>

</td>

<td>

<?php

if($row['payment_status']=="Paid")
{
echo "<span class='badge bg-success'>Paid</span>";
}
else
{
echo "<span class='badge bg-warning'>Pending</span>";
}

?>

</td>

<td>

<?php

switch($row['order_status'])
{

case "Pending":

echo "<span class='badge bg-warning'>Pending</span>";

break;

case "Processing":

echo "<span class='badge bg-info'>Processing</span>";

break;

case "Delivered":

echo "<span class='badge bg-success'>Delivered</span>";

break;

default:

echo "<span class='badge bg-danger'>Cancelled</span>";

}

?>

</td>

<td>

<a
href="order_details.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-primary btn-sm">

View

</a>

<a
href="invoice.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-success btn-sm">

Invoice

</a>

</td>

</tr>

<?php

}

}
else

{

?>

<tr>

<td colspan="7" class="text-center">

No Orders Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>

</html>

