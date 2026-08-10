<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all orders
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1)
{
    $page = 1;
}

$start = ($page - 1) * $limit;

$search = isset($_GET['search'])
    ? mysqli_real_escape_string($conn, trim($_GET['search']))
    : "";

$status = isset($_GET['status'])
    ? mysqli_real_escape_string($conn, trim($_GET['status']))
    : "";

$where = " WHERE 1=1 ";

if($search != "")
{
    $where .= " AND (
        order_id LIKE '%$search%'
        OR customer_name LIKE '%$search%'
        OR email LIKE '%$search%'
    )";
}

if($status != "")
{
    $where .= " AND order_status='$status'";
}

// Total records
$countSql = "SELECT COUNT(*) AS total
             FROM `order`
             $where";

$countResult = mysqli_query($conn, $countSql);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Main query
$sql = "
SELECT
    `order`.order_id,
    customer_detail.name AS customer_name,
    `order`.order_date,
    `order`.total_amount,
    `order`.order_status,
    payment.payment_method,
    payment.payment_status,
    customer_detail.email AS email
FROM `order`
INNER JOIN customer_detail
    ON `order`.customer_id = customer_detail.customer_id
LEFT JOIN payment
    ON `order`.order_id = payment.order_id
ORDER BY `order`.order_id DESC
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Orders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container py-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Customer Orders</h2>

<a href="../dashboard.php" class="btn btn-secondary">
Back to Dashboard
</a>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h4 class="mb-0">All Orders</h4>
</div>

<div class="card-body">

<div class="table-responsive">

<form method="GET" class="row g-3 mb-4">

<div class="col-md-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Search by Order ID, Customer or Email"
value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

</div>

<div class="col-md-3">

<select name="status" class="form-select">

<option value="">All Status</option>

<option value="Pending"
<?php if(isset($_GET['status']) && $_GET['status']=="Pending") echo "selected"; ?>>
Pending
</option>

<option value="Processing"
<?php if(isset($_GET['status']) && $_GET['status']=="Processing") echo "selected"; ?>>
Processing
</option>

<option value="Delivered"
<?php if(isset($_GET['status']) && $_GET['status']=="Delivered") echo "selected"; ?>>
Delivered
</option>

<option value="Cancelled"
<?php if(isset($_GET['status']) && $_GET['status']=="Cancelled") echo "selected"; ?>>
Cancelled
</option>

</select>

</div>

<div class="col-md-2">

<button type="submit" class="btn btn-primary w-100">
Search
</button>

</div>

<div class="col-md-2">

<a href="view.php" class="btn btn-secondary w-100">
Reset
</a>

</div>

</form>



<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>Order ID</th>
<th>Customer</th>
<th>Email</th>
<th>Total</th>
<th>Payment</th>
<th>Payment Status</th>
<th>Order Status</th>
<th>Date</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {
?>

<tr>

<td>
#<?php echo $row['order_id']; ?>
</td>

<td>
<?php echo htmlspecialchars($row['customer_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['email']); ?>
</td>

<td>
₹<?php echo number_format($row['total_amount'], 2); ?>
</td>

<td>
<?php echo htmlspecialchars($row['payment_method']); ?>
</td>

<td>

<?php
if($row['payment_status'] == "Paid")
{
    echo "<span class='badge bg-success'>Paid</span>";
}
else
{
    echo "<span class='badge bg-warning text-dark'>Pending</span>";
}
?>

</td>

<td>

<?php
switch($row['order_status'])
{
    case "Pending":
        echo "<span class='badge bg-warning text-dark'>Pending</span>";
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
<?php echo $row['order_date']; ?>
</td>

<td>

<a href="details.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-primary btn-sm">
View
</a>

<a href="invoice.php?id=<?php echo $row['order_id']; ?>"
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
<td colspan="9" class="text-center">
No orders found.
</td>
</tr>

<?php
}
?>

</tbody>

</table>

<nav class="mt-4">

<ul class="pagination justify-content-center">

<?php for($i = 1; $i <= $totalPages; $i++) { ?>

<li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">

<a class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">

<?php echo $i; ?>

</a>

</li>

<?php } ?>

</ul>

</nav>



</div>

</div>

</div>

</div>

</body>
</html>

