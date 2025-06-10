<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit(); // Pastikan script berhenti setelah redirect
}

try {
    $id_user = $_SESSION['id_user'];
    $sql_user = "SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user = :id_user";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt_user->execute();
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("User not found for id_user: " . $id_user);
        echo "User tidak ditemukan. Silakan coba lagi nanti.";
        exit();
    }

    $default_image = "img/pp.png";
    $user_image_path = !empty($user['image_path']) ? htmlspecialchars($user['image_path']) : $default_image;

} catch (PDOException $e) {
    error_log("Database error fetching user: " . $e->getMessage());
    echo "Terjadi kesalahan koneksi database. Silakan coba lagi nanti.";
    exit();
}

// Query untuk mengambil produk berdasarkan status_delivery 'completed'
$query_orders = "
    SELECT o.id_order, o.id_product, o.product_price, o.total_price, p.merk, p.variety, p.image_path
    FROM Orders o
    JOIN Status_Orders s ON o.id_order = s.id_order
    JOIN Products p ON o.id_product = p.id_product
    WHERE o.id_user = :id_user AND s.status_delivery = 'completed'
    ORDER BY s.delivery_date DESC
";

try {
    $stmt_orders = $conn->prepare($query_orders);
    $stmt_orders->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt_orders->execute();
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage());
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css"> <!-- Pastikan styling tombol asli ada di sini -->
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Riwayat - Laptopku.</title>
    <style>
        /* CSS untuk mengatur ukuran gambar produk di riwayat */
        .order-card .order-info img.product-image-history {
            width: 100px; /* Atur lebar gambar, sesuaikan jika perlu */
            height: 100px; /* Atur tinggi gambar, sesuaikan jika perlu */
            object-fit: cover; /* Memastikan gambar terisi tanpa merusak aspek rasio */
            margin-right: 15px; /* Jarak antara gambar dan detail teks */
            border-radius: 5px; /* Sudut lengkung untuk gambar */
            flex-shrink: 0;
        }

        /* Memastikan .order-info menggunakan flex untuk alignment yang baik */
        .order-card .order-info {
            display: flex;
            align-items: center; /* Menyelaraskan gambar dan teks secara vertikal */
        }

        /* Styling dasar untuk .order-card .button jika diperlukan untuk layout, tapi bukan styling spesifik tombol */
        .order-card .button {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end; /* Posisikan grup tombol ke kanan */
        }
        /* Pastikan tombol-tombol di dalam .btn-review dan .btn-order memiliki jarak jika berdampingan */
        .order-card .button .btn-review {
            margin-right: 10px; /* Jarak antara "Lihat Ulasan" dan "Beli Lagi" */
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="product-login.php">Shopping</a>
            <a href="cart.php" class="cartimg"><img src="img/chart-icon.png" alt="Keranjang Belanja"></a>
            <a href="profile.php"><img src="<?php echo $user_image_path; ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="home.php" title="Logout"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>

    <section class="hero">
        <div class="menu">
            <div class="menu-container">
                <p onclick="window.location.href='profile.php';">Profile</p>
                <p onclick="window.location.href='pesanan.php';">Pesanan</p>
                <p onclick="window.location.href='review.php';">Ulasan</p>
                <p onclick="window.location.href='riwayat.php';" class="active">Riwayat</p>
                <hr>
                <a href="logout.php">Sign Out</a>
            </div>
        </div>
        <div class="main-content">
            <div class="title">Riwayat Pesanan</div>

            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-info">
                            <img src="<?= htmlspecialchars($order['image_path']) ?>" alt="Product Image: <?= htmlspecialchars($order['merk']) ?>" class="product-image-history">
                            <div class="detail">
                                <p><strong><?= htmlspecialchars($order['merk']) ?> - <?= htmlspecialchars($order['variety']) ?></strong></p>
                                <p><span class="status">Harga Produk: </span>Rp. <?= number_format($order['product_price'], 0, ',', '.') ?></p>
                                <p><span class="status">Total Pesanan: </span>Rp. <?= number_format($order['total_price'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="button">
                            <div class="btn-review">
                                <!-- Pastikan class .review-button di-style di user.css atau file CSS utama Anda -->
                                <button class="review-button" onclick="window.location.href='review.php?order_id=<?= htmlspecialchars($order['id_order']) ?>&product_id=<?= htmlspecialchars($order['id_product']) ?>'">Lihat Ulasan</button>
                            </div>
                            <div class="btn-order">
                                <!-- Pastikan class .order-button di-style di user.css atau file CSS utama Anda -->
                                <button class="order-button" onclick="addToCart(<?= htmlspecialchars($order['id_product']) ?>)">Beli Lagi</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada riwayat pesanan yang selesai.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="footer">
        <div class="brand">LAPTOPKU.</div>
        <div class="tagline">Toko Online Laptop</div>
        <div class="social-icons">
            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="#"><i class="fa-regular fa-envelope"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-discord"></i></a>
        </div>
        <hr class="garis">
        <div class="copyright">
            Copyright © 2024 Laptopku
        </div>
    </section>

    <script>
        function addToCart(productId) {
            alert('Produk dengan ID ' + productId + ' akan ditambahkan ke keranjang. (Fungsi Beli Lagi belum diimplementasikan sepenuhnya)');
            // Implementasi sebenarnya:
            // window.location.href = 'add_to_cart.php?product_id=' + productId;
        }
    </script>
</body>
</html>