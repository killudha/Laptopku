<?php
require 'C:\xampp\htdocs\Laptopku\config.php'; // Memasukkan file konfigurasi

// Ambil ID produk dari URL
$id_produk = $_GET['id'] ?? null; // Ganti 'id_produk' dengan 'id'

if ($id_produk && is_numeric($id_produk)) {
    // Query untuk mendapatkan detail produk
    $query = "SELECT * FROM products WHERE id_product = :id_product"; // Ganti 'id_produk' dengan 'id_product'
    $stmt = $conn->prepare($query); // Ganti $pdo dengan $conn
    $stmt->execute(['id_product' => $id_produk]); // Ganti 'id_produk' dengan 'id_product'

    // Ambil data produk
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika produk tidak ditemukan
    if (!$product) {
        die("Produk tidak ditemukan.");
    }
} else {
    die("ID produk tidak diberikan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Detail - Laptopku.</title>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <i class="fas fa-search"></i>
        </div>
        <div class="nav-links">
            <a href="home-1.php">Home</a>
            <a href="#">About Us</a>
            <a href="#">Contact</a>
            <a href="product.php">Shopping</a>
            <div class="login-btn">
                <a href="register.php">Login</a>
                <a href="register.php"><i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </nav>
    
    <!-- Detail Produk -->
    <a href="product.php">
    <button class="back">
        <i class="fa fa-angle-left"></i>
    </button></a>
    <div class="card-detail">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['merk']) ?>">
        </div>
        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($product['merk'] . ' ' . $product['variety']) ?></h1>
            <div class="product-price">Rp.<?= number_format($product['price'], 0, ',', '.') ?></div>
            <ul class="specs-list">
                <li><?= htmlspecialchars($product['processor']) ?></li>
                <li>RAM <?= htmlspecialchars($product['ram']) ?></li>
                <li><?= htmlspecialchars($product['vga']) ?></li>
                <li><?= htmlspecialchars($product['screen_size']) ?> inch & <?= htmlspecialchars($product['storages']) ?></li>
            </ul>
            <p class="product-description">
                <?= htmlspecialchars($product['feature']) ?>
            </p>
            <div class="quantity-control">
                <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                <span id="quantity">1</span>
                <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                <div class="btntocart">
                    <button class="cart"><i class="fa fa-shopping-cart"></i></button>
                    <button class="add-to-cart">Tambah ke keranjang</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    // Variabel untuk menyimpan jumlah produk
    let quantity = 1;

    // Fungsi untuk menambah jumlah
    function increaseQuantity() {
        quantity++;
        document.getElementById("quantity").textContent = quantity;
    }

    // Fungsi untuk mengurangi jumlah, memastikan tidak kurang dari 1
    function decreaseQuantity() {
        if (quantity > 1) {
            quantity--;
            document.getElementById("quantity").textContent = quantity;
        }
    }
</script>