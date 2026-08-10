<?php
session_start();
include("config/db.php");

// Get all sweets with stock information
$query = mysqli_query($conn, "
    SELECT
        s.sweet_id,
        s.sweet_name,
        s.description,
        s.price,
        s.image,
        s.weight,
        COALESCE(SUM(st.quantity_in_stock), 0) AS quantity_in_stock
    FROM sweets s
    LEFT JOIN stock st
        ON s.sweet_id = st.sweet_id
    GROUP BY
        s.sweet_id,
        s.sweet_name,
        s.description,
        s.price,
        s.image,
        s.weight
    ORDER BY s.sweet_name ASC
");

if (!$query) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Our Sweets</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f8f9fa;
}

.card{
    transition:0.3s;
    border:none;
    border-radius:10px;
    overflow:hidden;
}

.card:hover{
    transform:translateY(-5px);
}

.card img{
    height:220px;
    object-fit:cover;
}

.stock-available{
    color:#198754;
    font-weight:bold;
}

.stock-unavailable{
    color:#dc3545;
    font-weight:bold;
}

</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

<a class="navbar-brand" href="index.php">
    Sweet Shop
</a>

<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="menu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="index.php">
Home
</a>
</li>

<li class="nav-item">
<a class="nav-link active" href="sweets.php">
Sweets
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="cart.php">
Cart
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="about.php">
About
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="contact.php">
Contact
</a>
</li>

</ul>

</div>

</div>

</nav>


<!-- ================= SWEETS ================= -->

<div class="container py-5">

<h2 class="text-center mb-5">
    Our Delicious Sweets
</h2>

<div class="row">

<?php

if(mysqli_num_rows($query) > 0)
{

    while($row = mysqli_fetch_assoc($query))
    {

        $stock = (int)$row['quantity_in_stock'];

?>

<div class="col-md-4 col-lg-3 mb-4">

<div class="card shadow h-100">

<img
src="uploads/sweets/<?php echo htmlspecialchars($row['image']); ?>"
class="card-img-top"
alt="<?php echo htmlspecialchars($row['sweet_name']); ?>">

<div class="card-body text-center">

<h5>
<?php echo htmlspecialchars($row['sweet_name']); ?>
</h5>

<p>
<?php echo htmlspecialchars($row['description']); ?>
</p>

<h4 class="text-success">
₹<?php echo number_format($row['price'],2); ?>
</h4>

<!-- STOCK -->

<?php if($stock > 0) { ?>

<p class="stock-available">

<i class="bi bi-check-circle"></i>

Stock:
<?php echo $stock; ?>

</p>

<a
href="add_to_cart.php?id=<?php echo $row['sweet_id']; ?>"
class="btn btn-warning w-100">

Add to Cart

</a>

<?php } else { ?>

<p class="stock-unavailable">

Out of Stock

</p>

<button
class="btn btn-secondary w-100"
disabled>

Out of Stock

</button>

<?php } ?>

</div>

</div>

</div>

<?php

    }

}
else
{

?>

<div class="col-12">

<div class="alert alert-warning text-center">

No sweets available.

</div>

</div>

<?php

}

?>

</div>

</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>