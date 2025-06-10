<?php
// Include database connection file
$baseConfigPath = 'C:\xampp\htdocs\Laptopku\config.php';

if (!file_exists($baseConfigPath)) {
    die("Error: Configuration file not found at $baseConfigPath");
}
require_once $baseConfigPath;

try {
    // Query to count orders based on delivery status
    $totalOrdersQuery = "SELECT COUNT(*) FROM orders";
    $pendingOrdersQuery = "SELECT COUNT(*) FROM status_orders WHERE status_delivery = 'not paid'";
    $inProgressOrdersQuery = "SELECT COUNT(*) FROM status_orders WHERE status_delivery = 'packaged'";
    $doneOrdersQuery = "SELECT COUNT(*) FROM status_orders WHERE status_delivery = 'completed'";

    // Execute queries and get the results
    $totalOrdersResult = $conn->query($totalOrdersQuery)->fetchColumn();
    $pendingOrdersResult = $conn->query($pendingOrdersQuery)->fetchColumn();
    $inProgressOrdersResult = $conn->query($inProgressOrdersQuery)->fetchColumn();
    $doneOrdersResult = $conn->query($doneOrdersQuery)->fetchColumn();
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}

// Pagination logic
$limit = 10; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search query (if search is provided)
$searchQuery = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = "%" . $_GET['search'] . "%";
    $searchQuery = "WHERE o.recipent_name LIKE :searchTerm OR p.merk LIKE :searchTerm OR u.username LIKE :searchTerm";
}

// Query to fetch orders with search and pagination
$sql = "SELECT o.id_order, o.recipent_name, u.telepon AS phone, u.alamat AS address, 
               c.quantity AS qty, o.total_price, o.resi, so.status_delivery, 
               so.order_date, p.merk AS product_name, u.username AS customer
        FROM orders o
        JOIN products p ON o.id_product = p.id_product
        JOIN users u ON o.id_user = u.id_user
        JOIN status_orders so ON o.id_order = so.id_order
        LEFT JOIN cart c ON o.id_user = c.id_user AND o.id_product = c.id_product
        $searchQuery
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
if ($searchQuery) {
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of pages
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Laptopku</title>
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
        .order-card {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                <h3>Orders</h3>
                <!-- Search Form -->
                <form action="order.php" method="GET" class="mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search Orders..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary mt-2">Search</button>
                </form>

                <!-- Order Summary -->
                <div id="order-summary" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Total Orders</h5>
                                    <p id="totalOrders"><?php echo htmlspecialchars($totalOrdersResult); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Pending</h5>
                                    <p id="pendingOrders"><?php echo htmlspecialchars($pendingOrdersResult); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>In Progress</h5>
                                    <p id="inProgressOrders"><?php echo htmlspecialchars($inProgressOrdersResult); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Completed</h5>
                                    <p id="doneOrders"><?php echo htmlspecialchars($doneOrdersResult); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Container -->
                <div id="orders-container">
                    <?php
                    foreach ($orders as $order) {
                        echo '<div class="order-card">';
                        echo '<p><strong>Status:</strong> ' . htmlspecialchars($order['status_delivery']) . '</p>';
                        echo '<p><strong>Product Name:</strong> ' . htmlspecialchars($order['product_name']) . '</p>';
                        echo '<p><strong>Name:</strong> ' . htmlspecialchars($order['recipent_name']) . '</p>';
                        echo '<p><strong>Phone:</strong> ' . htmlspecialchars($order['phone']) . '</p>';
                        echo '<p><strong>Address:</strong> ' . htmlspecialchars($order['address']) . '</p>';
                        echo '<p><strong>Order Date:</strong> ' . htmlspecialchars($order['order_date']) . '</p>';
                        echo '<p><strong>Qty:</strong> ' . htmlspecialchars($order['qty']) . '<br>';
                        echo '<strong>Total:</strong> Rp ' . number_format($order['total_price'], 0, ',', '.') . '<br>';
                        echo '<strong>Resi:</strong> ' . htmlspecialchars($order['resi']) . '</p>';
                        echo '<div class="order-buttons">';

                        if ($order['status_delivery'] == 'not paid') {
                            echo '<a href="approve.php?id_order=' . urlencode($order['id_order']) . '" class="btn btn-success">Approve</a>';
                        }
                        echo '<a href="edit.php?id_order=' . urlencode($order['id_order']) . '" class="btn btn-primary">Edit</a>';
                        echo '<a href="delete.php?id_order=' . urlencode($order['id_order']) . '" class="btn btn-danger">Cancel</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=1&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
