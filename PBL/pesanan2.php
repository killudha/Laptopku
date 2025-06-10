<?php
session_start();
require 'C:\xampp\htdocs\Laptopku\config.php'; // Pastikan ini sesuai dengan file koneksi Anda

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
try {
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user = :id_user";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
        exit();
    }
    $default_image = "img/pp.png";
    $image_path = !empty($user['image_path']) ? $user['image_path'] : $default_image;
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit();
}
$query = "
    SELECT o.id_order, o.total_price, o.resi, p.merk, p.variety, p.price
    FROM Orders o
    JOIN Status_Orders s ON o.id_order = s.id_order
    JOIN Products p ON o.id_product = p.id_product
    WHERE o.id_user = :id_user AND s.status_delivery = 'packaged' AND o.payment_status = 'paid'
";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Pesanan2 - Laptopku.</title>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="product-login.php">Shopping</a>
            <a href="cart.php" class="cartimg"><img src="img/chart-icon.png" alt="profile user"></a>
            <a href="profile.php"><img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="home.php"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>

    <section class="hero">
        <div class="menu">
            <div class="menu-container">
                <p onclick="window.location.href='profile.php';">Profile</p>
                <p onclick="window.location.href='pesanan.php';">Pesanan</p>
                <p onclick="window.location.href='review.php';">Ulasan</p>
                <p onclick="window.location.href='riwayat.php';">Riwayat</p>
                <a href="home.html"><i class="fa fa-sign-out"></i></a>
                <hr>
                <a href="logout.php">Sign Out</a>             
            </div>     
        </div> 
        <div class="main-content">
            <div class="title">Pesanan</div>
            <div class="track">
                <p>Order Tracker</p>
                <p><?= count($result) ?> Items</p>
            </div>

            <hr>

            <div class="status-container">
                <div class="status-item" onclick="window.location.href='pesanan.php';" style="cursor: pointer;">
                    <img src="img/bayar.png" alt="payment">
                    <p>Belum<br>Bayar</p>
                </div>
                <div class="status-item" onclick="window.location.href='pesanan2.php';" style="cursor: pointer;">
                    <img src="img/dikemas.png" alt="package">
                    <p><span class="pay">Dikemas</span></p>
                </div>
                <div class="status-item" onclick="window.location.href='pesanan3.php';" style="cursor: pointer;">
                    <img src="img/dikirim.png" alt="delivery">
                    <p>Dikirim</p>
                </div>
                <div class="status-item" onclick="window.location.href='pesanan4.php';" style="cursor: pointer;">
                    <img src="img/nilai.png" alt="rate">
                    <p>Beri<br>Penilaian</p>
                </div>
            </div>
            <div class="track">
                <p>Dikemas</p>
            </div>

            <?php foreach ($result as $row): ?>
<div class="order-card">
    <div class="detail-order">
        <p><span class="order-id">Order: #<?= htmlspecialchars($row['id_order']) ?></span></p>
        <div class="detail">
            <p><span class="price">Produk: </span><?= htmlspecialchars($row['merk'] . " " . $row['variety']) ?></p>
            <p><span class="price">Total: </span>Rp. <?= number_format($row['total_price'], 0, ',', '.') ?></p>
            <p><span class="price">Resi: </span><?= htmlspecialchars($row['resi']) ?></p>
        </div>
    </div>
    <div class="btn-payment">
        <button class="payment-button" onclick="window.location.href='detail-tracker.php?id_order=<?= $row['id_order'] ?>';">Detail</button>
    </div>
</div>
<?php endforeach; ?>


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
            Copyright © 2022024 Laptopku
        </div>
    </section>
</body>
</html>
