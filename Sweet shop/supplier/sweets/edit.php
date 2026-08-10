<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$sweet_id = (int)$_GET['id'];

$query = mysqli_query($conn,"
SELECT *
FROM sweets
WHERE sweet_id='$sweet_id'
AND supplier_id='$supplier_id'
");

if(mysqli_num_rows($query)==0)
{
    header("Location:view.php");
    exit();
}

$sweet = mysqli_fetch_assoc($query);

$categories = mysqli_query($conn,"
SELECT *
FROM category
ORDER BY category_name
");

$message="";

if(isset($_POST['update']))
{
    $category_id=(int)$_POST['category_id'];
    $sweet_name=mysqli_real_escape_string($conn,$_POST['sweet_name']);
    $description=mysqli_real_escape_string($conn,$_POST['description']);
    $price=$_POST['price'];
    $weight=mysqli_real_escape_string($conn,$_POST['weight']);
    $status=$_POST['status'];

    $image=$sweet['image'];

    if($_FILES['image']['name']!="")
    {
        if($image!="" && file_exists("../../uploads/sweets/".$image))
        {
            unlink("../../uploads/sweets/".$image);
        }

        $image=time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../uploads/sweets/".$image
        );
    }

    $sql="
    UPDATE sweets SET
    category_id='$category_id',
    sweet_name='$sweet_name',
    description='$description',
    price='$price',
    image='$image',
    weight='$weight',
    status='$status'
    WHERE sweet_id='$sweet_id'
    AND supplier_id='$supplier_id'
    ";

    if(mysqli_query($conn,$sql))
    {
        header("Location:view.php?updated=1");
        exit();
    }
    else
    {
        $message="<div class='alert alert-danger'>
        Failed to update sweet.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Sweet</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Edit Sweet</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Category</label>

<select name="category_id" class="form-select" required>

<?php
while($cat=mysqli_fetch_assoc($categories))
{
?>

<option
value="<?php echo $cat['category_id']; ?>"
<?php if($cat['category_id']==$sweet['category_id']) echo "selected"; ?>>

<?php echo $cat['category_name']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="mb-3">

<label>Sweet Name</label>

<input
type="text"
name="sweet_name"
class="form-control"
value="<?php echo htmlspecialchars($sweet['sweet_name']); ?>"
required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"><?php echo htmlspecialchars($sweet['description']); ?></textarea>

</div>

<div class="row">

<div class="col-md-6">

<div class="mb-3">

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
class="form-control"
value="<?php echo $sweet['price']; ?>"
required>

</div>

</div>

<div class="col-md-6">

<div class="mb-3">

<label>Weight</label>

<input
type="text"
name="weight"
class="form-control"
value="<?php echo htmlspecialchars($sweet['weight']); ?>"
required>

</div>

</div>

</div>

<div class="mb-3">

<label>Current Image</label><br>

<img
src="../../uploads/sweets/<?php echo $sweet['image']; ?>"
width="120"
class="img-thumbnail">

</div>

<div class="mb-3">

<label>Change Image</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*">

</div>

<div class="mb-3">

<label>Status</label>

<select name="status" class="form-select">

<option value="Yes" <?php if($sweet['status']=="Yes") echo "selected"; ?>>
Available
</option>

<option value="No" <?php if($sweet['status']=="No") echo "selected"; ?>>
Unavailable
</option>

</select>

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

Update Sweet

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

Back to My Sweets

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>