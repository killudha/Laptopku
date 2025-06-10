<?php
// Connect to the database
include 'C:\xampp\htdocs\Laptopku\config.php';

try {
    // Fetch incoming and outgoing product data
    $stmt_in = $conn->query("SELECT COUNT(*) AS total_in FROM In_Product");
    $total_in = $stmt_in->fetch(PDO::FETCH_ASSOC)['total_in'];

    $stmt_out = $conn->query("SELECT COUNT(*) AS total_out FROM Out_Product");
    $total_out = $stmt_out->fetch(PDO::FETCH_ASSOC)['total_out'];

    // Fetch product data for dropdown and table (updated 'type' to 'variety')
    $stmt_products = $conn->query("SELECT id_product, merk, variety FROM Products"); // Corrected 'type' to 'variety'
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

    // Combine incoming and outgoing data for the table
    $stmt_flow = $conn->query("
        SELECT 'In' AS type, id_in AS id, id_product, in_date AS date, quantity 
        FROM In_Product
        UNION ALL
        SELECT 'Out' AS type, id_out AS id, id_product, out_date AS date, quantity 
        FROM Out_Product
        ORDER BY date DESC
    ");
    $product_flow = $stmt_flow->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product In and Out - Laptopku</title>
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
                <h3>Product In and Out</h3>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products In</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($total_in)) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products Out</h5>
                                <p class="card-text"><?= htmlspecialchars(number_format($total_out)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Form -->
                <form method="POST" action="product_flow_process.php" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="In">In</option>
                                <option value="Out">Out</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="id_product" class="form-label">Product</label>
                            <select name="id_product" id="id_product" class="form-select">
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id_product'] ?>">
                                        <?= htmlspecialchars($product['merk'] . ' - ' . $product['variety']) ?> <!-- 'variety' instead of 'type' -->
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>

                <!-- Data Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product_flow as $flow): ?>
                            <tr>
                                <td><?= htmlspecialchars($flow['type']) ?></td>
                                <td>
                                    <?php
                                    $product_name = array_filter($products, function ($p) use ($flow) {
                                        return $p['id_product'] == $flow['id_product'];
                                    });
                                    $product_name = reset($product_name);
                                    echo htmlspecialchars($product_name['merk'] . ' - ' . $product_name['variety']); // 'variety' instead of 'type'
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($flow['date']) ?></td>
                                <td><?= htmlspecialchars(number_format($flow['quantity'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
