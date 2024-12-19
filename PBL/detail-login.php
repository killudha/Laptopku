<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php'); // Redirect ke login jika belum login
    exit();
}

require 'C:\xampp\htdocs\Laptopku\config.php'; // Memasukkan file konfigurasi

try {
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user = :id_user";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah data pengguna ditemukan
    if (!$user) {
        die("User  not found.");
        exit();
    }
    // Gambar default jika tidak ada gambar yang diunggah
    $default_image = "img/pp.png"; // Ganti dengan path gambar default Anda
    $image_path = !empty($user['image_path']) ? $user['image_path'] : $default_image;
 
    if (!$user) {
        echo "User  tidak ditemukan.";
        exit();
    }
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit();
}

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
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5); /* Gelap semi-transparan */
            backdrop-filter: blur(5px); /* Efek blur */
        }
        
        .modal-content {
            background-color: #ffffff;
            margin: 10% auto; 
            padding: 30px;
            border-radius: 10px; /* Membuat sudut melengkung */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Memberikan bayangan */
            width: 60%; 
            max-width: 500px; /* Batas maksimal lebar */
            text-align: center;
            animation: modalFadeIn 0.4s ease-out; /* Animasi fade-in */
            position: relative; /* Untuk posisi ikon tutup */
        }
        
        .close {
            color: #555;
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease-in;
        }
        
        .close:hover {
            color: #ff0000; /* Warna merah saat hover */
        }
        
        .modal-icon {
            margin-bottom: 20px;
            font-size: 50px; /* Ikon besar */
            color: #007BFF; /* Warna biru */
        }
        
        .modal h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333; /* Warna teks */
        }
        
        .modal p {
            font-size: 16px;
            color: #666; /* Warna teks sekunder */
        }
        
        
        .modal-button-green {
            background-color: #28a745; /* Warna hijau */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        
        .modal-button-green:hover {
            background-color: #218838; /* Warna hijau lebih gelap saat hover */
            transform: scale(1.05); /* Efek membesar sedikit */
        }
        
        .modal-button-green:active {
            background-color: #1e7e34; /* Warna hijau lebih gelap saat tombol ditekan */
            transform: scale(0.95); /* Efek mengecil saat ditekan */
        }
        
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
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


function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_product: productId, quantity: quantity }) // Kirim jumlah juga
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal(); // Tampilkan modal jika berhasil
        } else {
            alert('Gagal menambahkan produk ke keranjang: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function showModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'block'; // Tampilkan modal
}

function closeModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'none'; // Sembunyikan modal
}

// Menutup modal jika pengguna mengklik di luar modal
window.onclick = function(event) {
    const modal = document.getElementById('successModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
    </script>
</head>
<body onload="showMessage()">
<nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="product-login.php">Shopping</a>
            <a href="cart.php" class="cartimg"><img src="img/chart-icon.png" alt="profile user"></a>
            <a href="profile.php"><img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="logout.php"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>
    
    <!-- Detail Produk -->
    <a href="product-login.php">
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
                    <button class="cart" onclick="addToCart(<?= $product['id_product'] ?>)"><i class="fa fa-shopping-cart"></i></button>
                    <button class="add-to-cart" onclick="addToCart(<?= $product['id_product'] ?>)">Tambah ke keranjang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk pesan sukses -->
    <div id="successModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-icon">
                <i class="fas fa-check-circle" style="color: green; font-size: 50px;"></i>
            </div>
            <h2>Berhasil ditambahkan ke keranjang!</h2>
            <p><?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : ''; ?></p>
            <button class="modal-button-green" onclick="closeModal()">OK</button>
        </div>
    </div>
</body>
</html>