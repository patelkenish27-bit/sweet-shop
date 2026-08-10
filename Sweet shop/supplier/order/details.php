<?php
session_start();
include("../../config/db.php");

if(!isset($_SESSION['supplier_id']))
{
    header("Location: ../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

$sql = "
SELECT
    o.order_id,
    c.name,
    o.order_date,
    o.total_amount,
    o.order_status,
    p.payment_method,
    p.payment_status
FROM `order` o
INNER JOIN customer_detail c
ON o.customer_id = c.customer_id
LEFT JOIN payment p
ON o.order_id = p.order_id
WHERE o.supplier_id='$supplier_id'
ORDER BY o.order_id DESC
";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Supplier Orders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>My Orders</h2>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Order ID</th>
<th>Customer</th>
<th>Date</th>
<th>Total</th>
<th>Payment Method</th>
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

<td><?php echo $row['order_id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['order_date']; ?></td>

<td>₹<?php echo number_format($row['total_amount'],2); ?></td>

<td>
<?php
echo !empty($row['payment_method']) ? $row['payment_method'] : "Not Paid";
?>
</td>

<td>
<?php
echo !empty($row['payment_status']) ? $row['payment_status'] : "-";
?>
</td>

<td>

<?php

$status=strtolower($row['order_status']);

if($status=="pending")
{
echo "<span class='badge bg-warning'>Pending</span>";
}
elseif($status=="processing")
{
echo "<span class='badge bg-info'>Processing</span>";
}
elseif($status=="delivered")
{
echo "<span class='badge bg-success'>Delivered</span>";
}
else
{
echo "<span class='badge bg-danger'>Cancelled</span>";
}

?>

</td>

<td>

<a href="details.php?id=<?php echo $row['order_id']; ?>" class="btn btn-primary btn-sm">
View
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

<td colspan="8" class="text-center">
No Orders Found
</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</body>

</html>