<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1)
$page = 1;

$start = ($page-1)*$limit;

/* Count */

$count = "
SELECT COUNT(*) AS total
FROM stock
INNER JOIN sweets
ON stock.sweet_id = sweets.sweet_id
";

if($search!="")
{
    $count .= "
    WHERE sweets.sweet_name
    LIKE '%$search%'
    ";
}

$countResult = mysqli_query($conn,$count);

$total = mysqli_fetch_assoc($countResult);

$totalPages = ceil($total['total']/$limit);

/* Data */

$sql = "
SELECT
stock.stock_id,
stock.quantity_in_stock,
stock.last_updated,
sweets.sweet_id,
sweets.sweet_name,
sweets.image
FROM stock
INNER JOIN sweets
ON stock.sweet_id = sweets.sweet_id
";

if($search!="")
{
    $sql .= "
    WHERE sweets.sweet_name
    LIKE '%$search%'
    ";
}

$sql .= "
ORDER BY stock.stock_id DESC
LIMIT $start,$limit
";

$result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Stock Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2>

<i class="bi bi-box-seam"></i>

Stock Management

</h2>

<a href="add.php"
class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Add Stock

</a>

</div>

<form method="GET"
class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Sweet"
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</form>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Image</th>

<th>Sweet</th>

<th>Quantity</th>

<th>Status</th>

<th>Last Updated</th>

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

<?php echo $row['stock_id']; ?>

</td>

<td>

<img
src="../../uploads/sweets/<?php echo $row['image']; ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>

<td>

<?php echo $row['sweet_name']; ?>

</td>

<td>

<?php echo $row['quantity_in_stock']; ?>

</td>

<td>

<?php

if($row['quantity_in_stock']<=10)
{
echo "<span class='badge bg-danger'>Low Stock</span>";
}
elseif($row['quantity_in_stock']<=30)
{
echo "<span class='badge bg-warning text-dark'>Medium</span>";
}
else
{
echo "<span class='badge bg-success'>In Stock</span>";
}

?>

</td>

<td>

<?php echo $row['last_updated']; ?>

</td>

<td>

<a
href="edit.php?id=<?php echo $row['stock_id'];?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

</a>

<a
href="delete.php?id=<?php echo $row['stock_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this stock record?')">

<i class="bi bi-trash"></i>

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

<td colspan="7"
class="text-center">

No Stock Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<nav>

<ul class="pagination justify-content-center">

<?php

for($i=1;$i<=$totalPages;$i++)
{

?>

<li class="page-item <?php if($page==$i) echo 'active'; ?>">

<a
class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">

<?php echo $i; ?>

</a>

</li>

<?php

}

?>

</ul>

</nav>

<a href="../dashboard.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</body>

</html>