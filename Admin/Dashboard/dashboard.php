<?php
// Connect to the database
include 'C:\xampp\htdocs\Laptopku\config.php';

// Query to retrieve data from the database
try {
    // Total Products
    $stmt = $conn->query("SELECT COUNT(*) AS total_products FROM products");
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

    // Active Users
    $stmt = $conn->query("SELECT COUNT(*) AS active_users FROM users WHERE role_id_user = 1");
    $active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];

    // Get order status data
    $stmt = $conn->query("SELECT 
        SUM(CASE WHEN status_delivery = 'not paid' THEN 1 ELSE 0 END) AS pending_orders,
        SUM(CASE WHEN status_delivery = 'packaged' THEN 1 ELSE 0 END) AS in_progress_orders,
        SUM(CASE WHEN status_delivery = 'completed' THEN 1 ELSE 0 END) AS completed_orders
    FROM status_orders");
    $status_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $pending_orders = $status_data['pending_orders'];
    $in_progress_orders = $status_data['in_progress_orders'];
    $completed_orders = $status_data['completed_orders'];
} catch (PDOException $e) {
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laptopku</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar a {
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            color: #000;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .active {
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h4 class="p-3">Laptopku</h4>
                <a href="http://localhost/Laptopku/Admin/Dashboard/dashboard.php" class="active">Dashboard</a>
                <a href="http://localhost/Laptopku/PBL/product-admin.php">Products</a>
                <a href="http://localhost/Laptopku/Admin\Order\order.php">Orders</a>
                <a href="http://localhost/Laptopku/Admin\Users\user.php">Users</a>
                <a href="http://localhost/Laptopku/Admin/StatusOrder/status_order.php">Order Status</a>
                <a href="http://localhost/Laptopku/Admin/InOutOrder/product_flow.php">Product In/Out</a>
                <a href="http://localhost/Laptopku/Admin/logout.php" class="text-danger">Logout</a>
            </div>

            <!-- Content -->
            <div class="col-md-9 col-lg-10 content">
                <h3>Dashboard</h3>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($total_products)) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Active Users</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($active_users)) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Orders</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($pending_orders)) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Completed Orders</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($completed_orders)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
