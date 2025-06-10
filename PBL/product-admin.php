<?php
include 'C:\xampp\htdocs\Laptopku\Admin\config.php'; // Hubungkan ke database

// Function to get the total number of products
$stmt = $conn->prepare("SELECT COUNT(*) FROM products");
$stmt->execute();
$total_products = $stmt->fetchColumn();

// Function to get the number of products with stock > 10
$stmt_instock_gt_10 = $conn->prepare("SELECT COUNT(*) FROM products WHERE stock >= 10");
$stmt_instock_gt_10->execute();
$instock_gt_10 = $stmt_instock_gt_10->fetchColumn();

// Function to get the number of products with stock < 10
$stmt_instock_lt_10 = $conn->prepare("SELECT COUNT(*) FROM products WHERE stock < 10");
$stmt_instock_lt_10->execute();
$instock_lt_10 = $stmt_instock_lt_10->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Laptopku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            background-color: #f8f9fa;
            padding: 15px; /* Sesuaikan padding */
            border-right: 1px solid #dee2e6; /* Jika ada border */
            overflow-y: auto; /* Tambahkan scroll jika konten melebihi tinggi */
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
        .product-card {
            margin-bottom: 20px;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
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
                <h3>Products</h3>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <p class="card-text"><?php echo $total_products; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">In Stock > 10</h5>
                                <p class=" card-text"><?php echo $instock_gt_10; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">In Stock < 10</h5>
                                <p class="card-text"><?php echo $instock_lt_10; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="product-admin.php" class="btn btn-danger">Reset</a>
                    </div>
                </form>

                <h3>Product List</h3>
                <a href="create.php" class="btn btn-primary mb-3">+ Add Product</a>

                <div class="row">
                    <?php
                    // Pagination Variables
                    $limit = 6; // Number of rows per page
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $start = ($page - 1) * $limit;

                    // Search Query
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $query = "SELECT * FROM products WHERE merk ILIKE :search OR variety ILIKE :search ORDER BY id_product LIMIT :limit OFFSET :start"; 
                    $stmt = $conn->prepare($query);
                    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
                    $stmt->execute();

                    // Display Products as cards
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Set image path from the database
                        $default_image = "img/laptop1.png"; 
                        $image_path = !empty($row['image_path']) ? $row['image_path'] : $default_image;

                        echo "
                        <div class='col-md-4'>
                            <div class='card product-card'>
                                <img src='" . htmlspecialchars($image_path) . "' class='card-img-top' alt='Product Image'>
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($row['merk']) . " - " . htmlspecialchars($row['variety']) . "</h5>
                                    <p class='card-text'>
                                        <strong>Processor:</strong> " . htmlspecialchars($row['processor']) . "<br>
                                        <strong>RAM:</strong> " . htmlspecialchars($row['ram']) . "<br>
                                        <strong>storages:</strong> " . htmlspecialchars($row['storages']) . "<br>
                                        <strong>Price:</strong> Rp " . number_format($row['price'], 0, ',', '.') . "<br>
                                        <strong>Stock:</strong> " . htmlspecialchars($row['stock']) . "<br>
                                        <strong>Updated at:</strong> " . htmlspecialchars($row['updated_at']) . "<br>
                                    </p>
                                    <a href='edit.php?id=" . htmlspecialchars($row['id_product']) . "' class='btn btn-warning'>Edit</a>
                                    <a href='delete.php?id=" . htmlspecialchars($row['id_product']) . "' class='btn btn-danger'>Delete</a>
                                </div>
                            </div>
                        </div>
                        ";
                    }

                    // Pagination Links
                    $stmt_total = $conn->prepare("SELECT COUNT(*) FROM products WHERE merk ILIKE :search OR variety ILIKE :search");
                    $stmt_total->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
                    $stmt_total->execute();
                    $total_rows = $stmt_total->fetchColumn();
                    $total_pages = ceil($total_rows / $limit);
                    ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-5">
                            <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>