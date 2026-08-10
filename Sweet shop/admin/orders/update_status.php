<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Accept only POST requests
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: view.php");
    exit();
}

// Get form values
$order_id = (int)$_POST['order_id'];
$order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
$payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);

<?php
if(isset($_GET['updated']))
{
    echo '<div class="alert alert-success">
            Order status updated successfully.
          </div>';
}
?>



// Update order
$sql = "UPDATE orders
        SET order_status='$order_status',
            payment_status='$payment_status'
        WHERE order_id='$order_id'";

if (mysqli_query($conn, $sql)) {

    header("Location: details.php?id=$order_id&updated=1");
    exit();

} else {

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Update Failed</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>

    <div class='container mt-5'>

        <div class='alert alert-danger'>

            <h4>Update Failed</h4>

            <p>" . htmlspecialchars(mysqli_error($conn)) . "</p>

            <a href='details.php?id=$order_id' class='btn btn-primary'>
                Back
            </a>

        </div>

    </div>

    </body>
    </html>";
}
?>

