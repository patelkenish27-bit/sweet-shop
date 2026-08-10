<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = "";

if(isset($_POST['save']))
{
    $sweet_id = (int)$_POST['sweet_id'];
    $quantity = (int)$_POST['quantity'];

    // Check if stock already exists
    $check = mysqli_query($conn,
    "SELECT * FROM stock
     WHERE sweet_id='$sweet_id'");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Stock record already exists for this sweet.
        </div>";
    }
    else
    {
        $sql = "INSERT INTO stock
        (sweet_id,quantity_in_stock,last_updated)
        VALUES
        ('$sweet_id','$quantity',NOW())";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?success=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to add stock.
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Stock</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Add Stock</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Sweet</label>

<select name="sweet_id" class="form-select" required>

<option value="">Select Sweet</option>

<?php

$sweets = mysqli_query($conn,
"SELECT sweet_id,sweet_name
 FROM sweets
 ORDER BY sweet_name ASC");

while($sweet = mysqli_fetch_assoc($sweets))
{
?>

<option value="<?php echo $sweet['sweet_id']; ?>">

<?php echo $sweet['sweet_name']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Stock Quantity

</label>

<input
type="number"
name="quantity"
class="form-control"
min="0"
required>

</div>

<div class="d-grid">

<button
type="submit"
name="save"
class="btn btn-success">

<i class="bi bi-save"></i>

Save Stock

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Stock

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>