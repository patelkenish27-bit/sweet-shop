<?php
session_start();

include("../config/db.php");

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #8B0000;
        }

        .sidebar h3 {
            color: white;
            text-align: center;
            padding: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            font-size: 17px;
        }

        .sidebar a:hover {
            background: #a30000;
        }

        .main {
            margin-left: 250px;
        }

        .topbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .content {
            padding: 30px;
        }

        @media(max-width:768px) {

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
            }

        }
    </style>

</head>

<body>

    <!-- Sidebar -->

    <div class="sidebar">

        <h3>🍬 Sweet Shop</h3>

        <a href="dashboard.php">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <a href="sweets/view.php">
            <i class="bi bi-box"></i>
            Manage Sweets
        </a>

        <a href="category/view.php">
            <i class="bi bi-grid"></i>
            Categories
        </a>

        <a href="supplier/view.php">
            <i class="bi bi-truck"></i>
            Suppliers
        </a>

        <a href="stock/view.php">
            <i class="bi bi-archive"></i>
            Stock
        </a>

        <a href="orders/view.php">
            <i class="bi bi-bag-check"></i>
            Orders
        </a>

        <a href="payments/view.php">
            <i class="bi bi-credit-card"></i>
            Payments
        </a>

        <a href="customers/view.php">
    <i class="bi bi-people"></i>
    Customers
</a>

        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>

    </div>

    <!-- Main -->

    <div class="main">

        <div class="topbar d-flex justify-content-between align-items-center">

            <h3>
                Admin Dashboard
            </h3>

            <div>

                Welcome,

                <b>
                    <?php echo $_SESSION['admin_name']; ?>
                </b>

            </div>

        </div>

        <div class="content">

            <h2 class="mb-4">
                Dashboard Overview
            </h2>

            <?php

            $totalCustomers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_detail"));

            $totalSweets = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM sweets"));

            $totalCategories = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM category"));

            $totalOrders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `order`"));

            $totalSuppliers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM supplier_detail"));

            $totalPayments = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM payment"));

            ?>

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-primary text-white">

                        <div class="card-body">

                            <h5>Total Customers</h5>

                            <h1><?php echo $totalCustomers; ?></h1>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-success text-white">

                        <div class="card-body">

                            <h5>Total Sweets</h5>

                            <h1><?php echo $totalSweets; ?></h1>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-warning text-dark">

                        <div class="card-body">

                            <h5>Total Categories</h5>

                            <h1><?php echo $totalCategories; ?></h1>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-danger text-white">

                        <div class="card-body">

                            <h5>Total Orders</h5>

                            <h1><?php echo $totalOrders; ?></h1>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-info text-white">

                        <div class="card-body">

                            <h5>Total Suppliers</h5>

                            <h1><?php echo $totalSuppliers; ?></h1>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card border-0 shadow bg-dark text-white">

                        <div class="card-body">

                            <h5>Total Payments</h5>

                            <h1><?php echo $totalPayments; ?></h1>

                        </div>

                    </div>

                </div>

            </div>

            <!--- Recent Orders --->

            <div class="card shadow mt-4">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Recent Orders</h4>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead class="table-dark">

                                <tr>

                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                               $query = mysqli_query($conn,"
SELECT
    o.order_id,
    c.name,
    o.total_amount,
    o.order_status,
    o.order_date,
    p.payment_method,
    p.payment_status
FROM `order` o
INNER JOIN customer_detail c
    ON o.customer_id = c.customer_id
LEFT JOIN payment p
    ON o.order_id = p.order_id
ORDER BY o.order_id DESC
");

                                if (mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) {
                                ?>

                                        <tr>

                                            <td><?php echo $row['order_id']; ?></td>

                                            <td><?php echo $row['name']; ?></td>

                                            <td>₹<?php echo $row['total_amount']; ?></td>

                                            <td><?php echo $row['payment_method']; ?></td>

                                            <td>

                                                <?php

                                                if ($row['order_status'] == "Pending") {
                                                    echo "<span class='badge bg-warning'>Pending</span>";
                                                } elseif ($row['order_status'] == "Processing") {
                                                    echo "<span class='badge bg-info'>Processing</span>";
                                                } elseif ($row['order_status'] == "Delivered") {
                                                    echo "<span class='badge bg-success'>Delivered</span>";
                                                } else {
                                                    echo "<span class='badge bg-danger'>Cancelled</span>";
                                                }

                                                ?>

                                            </td>

                                            <td><?php echo $row['order_date']; ?></td>

                                            <td>

                                                <a href="orders/details.php?id=<?php echo $row['order_id']; ?>"
                                                    class="btn btn-sm btn-primary">

                                                    View

                                                </a>

                                                <a href="orders/update_status.php?id=<?php echo $row['order_id']; ?>"
                                                    class="btn btn-sm btn-success">

                                                    Update

                                                </a>

                                            </td>

                                        </tr>

                                <?php

                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No Orders Found</td></tr>";
                                }

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <!-- ================= LOW STOCK ALERT ================= -->

            <div class="row mt-4">

                <div class="col-lg-6">

                    <div class="card shadow">

                        <div class="card-header bg-danger text-white">

                            <h5>Low Stock Alert</h5>

                        </div>

                        <div class="card-body">

                            <?php

                           $stock = mysqli_query($conn, "
SELECT
    sweets.sweet_name,
    stock.quantity_in_stock AS stock_quantity
FROM sweets
INNER JOIN stock
ON sweets.sweet_id = stock.sweet_id
WHERE stock.quantity_in_stock <= 10
");

                            if (mysqli_num_rows($stock) > 0) {

                            ?>

                                <table class="table table-bordered">

                                    <tr>

                                        <th>Sweet</th>

                                        <th>Stock</th>

                                    </tr>

                                    <?php

                                    while ($row = mysqli_fetch_assoc($stock)) {

                                    ?>

                                        <tr>

                                            <td><?php echo $row['sweet_name']; ?></td>

                                            <td>

                                                <span class="badge bg-danger">

                                                    <?php $row['stock_quantity'] ?>

                                                </span>

                                            </td>

                                        </tr>

                                    <?php

                                    }

                                    ?>

                                </table>

                            <?php

                            } else {

                                echo "<div class='alert alert-success'>
All products are sufficiently stocked.
</div>";
                            }

                            ?>

                        </div>

                    </div>

                </div>

                <!-- ================= SALES SUMMARY ================= -->

                <div class="col-lg-6">

                    <div class="card shadow">

                        <div class="card-header bg-success text-white">

                            <h5>Sales Summary</h5>

                        </div>

                        <div class="card-body">

                            <?php

                            $sales = mysqli_query($conn, "
SELECT SUM(total_amount) AS total
FROM `order`
WHERE order_status='delivered'
");

                            $data = mysqli_fetch_assoc($sales);

                            $totalSales = $data['total'];

                            ?>

                            <h2 class="text-success">

                                ₹<?php

                                    if ($totalSales == "")
                                        echo "0";

                                    else
                                        echo number_format($totalSales, 2);

                                    ?>

                            </h2>

                            <p>Total Delivered Sales</p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ================= QUICK ACTION ================= -->

            <div class="card shadow mt-4">

                <div class="card-header bg-primary text-white">

                    <h5>Quick Actions</h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3 mb-3">

                            <a href="sweets/add.php"
                                class="btn btn-success w-100">

                                Add Sweet

                            </a>

                        </div>

                        <div class="col-md-3 mb-3">

                            <a href="category/add.php"
                                class="btn btn-warning w-100">

                                Add Category

                            </a>

                        </div>

                        <div class="col-md-3 mb-3">

                            <a href="supplier/add.php"
                                class="btn btn-info w-100">

                                Add Supplier

                            </a>

                        </div>

                        <div class="col-md-3 mb-3">

                            <a href="orders/view.php"
                                class="btn btn-danger w-100">

                                Manage Orders

                            </a>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ================= FOOTER ================= -->

            <footer class="text-center mt-5 mb-3">

                <hr>

                <p>

                    © 2026 Online Sweet Shopping & Storage System

                    <br>

                    Admin Dashboard

                </p>

            </footer>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>