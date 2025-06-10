<?php
// Memasukkan file koneksi database
require_once 'C:\xampp\htdocs\Laptopku\Admin\config.php';

try {
    // Query untuk menghitung total pesanan berdasarkan status pengiriman
    $totalOrdersQuery = "SELECT COUNT(*) FROM orders";
    $pendingOrdersQuery = "SELECT COUNT(*) FROM status_order WHERE status_delivery = 'belum_dibayar'";
    $inProgressOrdersQuery = "SELECT COUNT(*) FROM status_order WHERE status_delivery = 'dikemas'";
    $doneOrdersQuery = "SELECT COUNT(*) FROM status_order WHERE status_delivery = 'selesai'";

    // Menjalankan query dan mendapatkan hasil
    $totalOrdersResult = $conn->query($totalOrdersQuery)->fetchColumn();
    $pendingOrdersResult = $conn->query($pendingOrdersQuery)->fetchColumn();
    $inProgressOrdersResult = $conn->query($inProgressOrdersQuery)->fetchColumn();
    $doneOrdersResult = $conn->query($doneOrdersQuery)->fetchColumn();
} catch (PDOException $e) {
    die("Kesalahan pada query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan - Laptopku</title>
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
                <a href="http://localhost/Laptopku/Admin/Dashboard/dashboard.php" >Dashboard</a>
                <a href="http://localhost/Laptopku/PBL/product-admin.php">Products</a>
                <a href="http://localhost/Laptopku/Admin/Pesanan/order.php" class="active" >Pesanan</a>
                <a href="http://localhost/Laptopku/Admin/Pengguna/user.php">Pengguna</a>
                <a href="http://localhost/Laptopku/Admin/StatusOrder/status_order.php">Status Order</a>
                <a href="http://localhost/Laptopku/Admin/KeluarMasukOrder/product_flow.php">Keluar Masuk Produk</a>
                <a href="http://localhost/Laptopku/Admin/logout.php" class="text-danger">Keluar</a>
            </div>

            <!-- Content -->
            <div class="col-md-9 col-lg-10 content">
                <h3>Pesanan</h3>
                <!-- Order Summary -->
                <div id="order-summary" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Total Orders</h5>
                                    <p id="totalOrders"><?php echo $totalOrdersResult; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Pending</h5>
                                    <p id="pendingOrders"><?php echo $pendingOrdersResult; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>In Progress</h5>
                                    <p id="inProgressOrders"><?php echo $inProgressOrdersResult; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Done</h5>
                                    <p id="doneOrders"><?php echo $doneOrdersResult; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Container -->
                <div id="orders-container">
                    <?php
                    // Query untuk mengambil data pesanan
                    $sql = "SELECT o.id_order, o.recipent_name, o.phone, o.address, c.kuantitas AS qty, o.total_price, o.resi, 
                            so.status_delivery, so.tanggal_pemesanan, p.merek AS product_name, u.nama_lengkap AS customer
                            FROM orders o
                            JOIN products p ON o.id_product = p.id_product
                            JOIN users u ON o.id_user = u.id_user
                            JOIN status_order so ON o.id_order = so.id_order
                            JOIN Carts c ON o.id_user = c.id_user AND o.id_product = c.id_product";
                    $stmt = $conn->query($sql);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Menampilkan data pesanan
                    foreach ($orders as $order) {
                        echo '<div class="order-card">';
                        echo '<p class="order-status"><strong>Status:</strong> ' . $order['status_delivery'] . '</p>';
                        echo '<p><strong>Nama Produk:</strong> ' . $order['product_name'] . '</p>';
                        echo '<p><strong>Nama:</strong> ' . $order['recipient_name'] . '</p>';
                        echo '<p><strong>No Telp:</strong> ' . $order['phone'] . '</p>';
                        echo '<p><strong>Alamat:</strong> ' . $order['address'] . '</p>';
                        echo '<p><strong>Tanggal Pemesanan:</strong> ' . $order['tanggal_pemesanan'] . '</p>';
                        echo '<p><strong>Rincian Pesanan:</strong><br>';
                        echo 'Qty: ' . $order['qty'] . '<br>';
                        echo 'Total: Rp ' . number_format($order['total_price'], 0, ',', '.') . '<br>';
                        echo 'Resi: ' . $order['resi'] . '</p>';
                        echo '<div class="order-buttons">';
                        echo '<a href="edit.php?id_order=' . $order['id_order'] . '" class="btn btn-primary">Edit</a>';
                        echo '<a href="delete.php?id_order=' . $order['id_order'] . '" class="btn btn-danger">Batalkan</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
