<?php
// Connect to the database
include 'C:\xampp\htdocs\Laptopku\config.php';

// Define how many results per page
$resultsPerPage = 10;

// Get the current page number, default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

// Initialize search term
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Initialize sorting order
$sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'DESC' : 'ASC';

try {
    // Modify query to include search, pagination, and sorting by order_date
    $stmt = $conn->prepare("SELECT so.id_status, so.status_delivery, so.order_date, o.id_order, u.username, p.merk, p.stock
                            FROM Status_Orders so
                            JOIN Orders o ON so.id_order = o.id_order
                            JOIN Users u ON o.id_user = u.id_user
                            JOIN Products p ON o.id_product = p.id_product
                            WHERE u.username LIKE :search OR p.merk LIKE :search OR so.status_delivery LIKE :search
                            ORDER BY so.order_date $sortOrder
                            LIMIT :limit OFFSET :offset");

    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $resultsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total rows for pagination
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM Status_Orders so
                                JOIN Orders o ON so.id_order = o.id_order
                                JOIN Users u ON o.id_user = u.id_user
                                JOIN Products p ON o.id_product = p.id_product
                                WHERE u.username LIKE :search OR p.merk LIKE :search OR so.status_delivery LIKE :search");
    
    $countStmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $countStmt->execute();
    $totalOrders = $countStmt->fetchColumn();
    $totalPages = ceil($totalOrders / $resultsPerPage);

    // Update order status
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $id_status = $_POST['id_status'];
        $new_status = $_POST['new_status'];

        if ($new_status == 'packaged') {
            // Get product stock before update
            $productStmt = $conn->prepare("SELECT o.id_product, p.stock FROM Orders o 
                                          JOIN Products p ON o.id_product = p.id_product
                                          WHERE o.id_order = (SELECT id_order FROM Status_Orders WHERE id_status = :id_status)");
            $productStmt->bindParam(':id_status', $id_status, PDO::PARAM_INT);
            $productStmt->execute();
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);

            // Update product stock (reduce stock by 1)
            if ($product) {
                $newStock = $product['stock'] - 1;
                $updateProductStmt = $conn->prepare("UPDATE Products SET stock = :new_stock WHERE id_product = :id_product");
                $updateProductStmt->execute([
                    ':new_stock' => $newStock,
                    ':id_product' => $product['id_product']
                ]);

                // Insert into Out_Product table
                $insertOutProductStmt = $conn->prepare("INSERT INTO Out_Product (id_product, quantity, out_date) VALUES (:id_product, 1, NOW())");
                $insertOutProductStmt->execute([
                    ':id_product' => $product['id_product']
                ]);
            }
        }

        // Update the status in Status_Orders table
        $updateStmt = $conn->prepare("UPDATE Status_Orders SET status_delivery = :new_status WHERE id_status = :id_status");
        $updateStmt->execute([
            ':new_status' => $new_status,
            ':id_status' => $id_status
        ]);

        echo "<script>alert('Status order berhasil diperbarui!');</script>";
        header("Location: status_order.php"); // Redirect to avoid form resubmission
        exit();
    }
} catch (PDOException $e) {
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - Laptopku</title>
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
        .pagination {
            margin-top: 20px;
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
                <a href="http://localhost/Laptopku/Admin/Order/order.php">Orders</a>
                <a href="http://localhost/Laptopku/Admin/Users/user.php">Users</a>
                <a href="http://localhost/Laptopku/Admin/StatusOrder/status_order.php">Order Status</a>
                <a href="http://localhost/Laptopku/Admin/InOutOrder/product_flow.php">Product In/Out</a>
                <a href="http://localhost/Laptopku/Admin/logout.php" class="text-danger">Logout</a>
            </div>

            <!-- Content -->
            <div class="col-md-9 col-lg-10 content">
                <h3>Order Status</h3>

                <!-- Search Form -->
                <form method="GET" action="" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search orders..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>

                <!-- Sort Order -->
                <form method="GET" action="" class="mb-3">
                    <div class="input-group">
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="asc" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Sort by Newest</option>
                            <option value="desc" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Sort by Oldest</option>
                        </select>
                    </div>
                </form>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Username</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id_status']) ?></td>
                                    <td><?= htmlspecialchars($order['id_order']) ?></td>
                                    <td><?= htmlspecialchars($order['username']) ?></td>
                                    <td><?= htmlspecialchars($order['merk']) ?></td>
                                    <td><?= htmlspecialchars($order['status_delivery']) ?></td>
                                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="id_status" value="<?= $order['id_status'] ?>">
                                            <select name="new_status" class="form-select" required>
                                                <option value="not paid" <?= $order['status_delivery'] == 'not paid' ? 'selected' : '' ?>>Not Paid</option>
                                                <option value="packaged" <?= $order['status_delivery'] == 'packaged' ? 'selected' : '' ?>>Packaged</option>
                                                <option value="shipped" <?= $order['status_delivery'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                <option value="completed" <?= $order['status_delivery'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm mt-2">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=1&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=<?= $sortOrder ?>">First</a>
                        </li>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=<?= $sortOrder ?>">Previous</a>
                        </li>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=<?= $sortOrder ?>">Next</a>
                        </li>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $totalPages ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=<?= $sortOrder ?>">Last</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
