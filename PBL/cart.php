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

// Ambil data keranjang
$query = "SELECT c.id_cart, p.merk, p.variety, p.price, c.quantity, p.image_path 
          FROM cart c 
          JOIN products p ON c.id_product = p.id_product 
          WHERE c.id_user = :id_user";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_user', $id_user);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script type="text/javascript" src="app.js" defer></script>
    <title>Home - Laptopku.</title>
</head>
<body>
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

    <div class="container">
    <h2 class="title">Your<span class="blue-text-title"> Cart</span></h2>
    <div class="container-cart">
        <div class="cart">
            <div class="cart-items">
                <div class="total-items">
                    <h1>Shopping Cart</h1>
                    <h1><?= count($cartItems) ?> Items</h1>
                </div>
                <hr>
                <!-- Header Section -->
                <div class="cart-header">
                    <div class="header-item"></div>
                    <div class="header-price">Item & Price</div>
                    <div class="header-qty">Qty</div>
                    <div class="header-subtotal">Sub Total</div>
                </div>
                <!-- product -->
                <?php foreach ($cartItems as $item): ?>
                <div class="item">
                    <div class="detail">
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['merk']) ?>">
                        <div class="item-details">
                            <p><?= htmlspecialchars($item['merk']) ?> - <?= htmlspecialchars($item['variety']) ?></p>
                            <p class="price">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                        </div>
                        <div class="quantity-control">
                            <button class="quantity-btn decrease" onclick="updateQuantity('<?= $item['id_cart'] ?>', -1)">-</button>
                            <span class="quantity"><?= $item['quantity'] ?></span>
                            <button class="quantity-btn increase" onclick="updateQuantity('<?= $item['id_cart'] ?>', 1)">+</button>
                        </div>
                        
                        <div class="subtotal">
                            <p>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                    <!-- Pisahkan tombol tong sampah -->
                    <form method="POST" action="remove_from_cart.php" class="delete-container" onsubmit="return false;">
                        <input type="hidden" name="product_id" value="<?= $item['id_cart'] ?>">
                        <button class="trashbtn" type="button" onclick="removeFromCart('<?= $item['id_cart'] ?>')"><i class="fa fa-trash"></i></button>
                    </form>
                </div>
                <?php endforeach; ?>
                </div>
                
          <!-- Sidebar -->
          <div class="sidebar">
            <div class="total-items">
                <h1>Order Summary</h1>
            </div>
            <hr>
            <div class="address">
              <h3>Alamat Pengiriman</h3>
              <input type="text" value="<?= htmlspecialchars($user['username']) ?>">
              <input type="text" value="<?= htmlspecialchars($user['telepon']) ?>">
              <input type="text" value="<?= htmlspecialchars($user['alamat']) ?>">
              <textarea placeholder="Detail Lainnya (Cth: Blok / Unit No, Patokan)"></textarea>
            </div>
            <div class="map">
                <div class="mapswrapper"><iframe width="370" height="200" loading="lazy" allowfullscreen src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=Depok&zoom=15&maptype=roadmap"></iframe><a href="https://www.fluxpromptgenerator.net">Prompt Generator</a><style>.mapswrapper{background:#fff;position:relative}.mapswrapper iframe{border:0;position:relative;z-index:2}.mapswrapper a{color:rgba(0,0,0,0);position:absolute;left:0;top:0;z-index:0}</style></div>
            </div>
            <div class="delivery">
              <h3>Layanan Pengiriman</h3>
              <select>
                <option>JNE Reguler</option>
                <option>J&T Express</option>
              </select>
            <div class="total-detail">
                <p>Delivery: </p>
                <span>Rp 15.000</span>
            </div>
            <div class="total-detail">
                <p>Subtotal: </p>
                <span>Rp 35.500.000</span>
            </div>
            <div class="total-detail">
                <p>Order Total: </p>
                <span>Rp 33.515.000</span>
            </div>
              <button class="make-order" onclick="window.location.href='payment.php';">Make Order</button>
            </div>
          </div>
        </div>
    </div>
</div>

<!--Footer-->
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
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Pilih elemen yang membungkus semua produk dalam keranjang
    const cartContainer = document.querySelector('.cart-items');

    // Pastikan elemen ditemukan
    if (cartContainer) {
        cartContainer.addEventListener('click', function (event) {
            // Pastikan tombol yang ditekan adalah "increase" atau "decrease"
            if (event.target.classList.contains('increase') || event.target.classList.contains('decrease')) {
                // Temukan elemen kuantitas terkait
                const quantityElement = event.target.parentElement.querySelector('.quantity');
                
                if (quantityElement) {
                    let quantity = parseInt(quantityElement.textContent);

                    if (event.target.classList.contains('increase')) {
                        quantity++;
                    } else if (event.target.classList.contains('decrease') && quantity > 1) {
                        quantity--;
                    }

                    // Perbarui jumlah kuantitas di elemen yang sesuai
                    quantityElement.textContent = quantity;

                    // Update subtotal
                    const itemPriceElement = event.target.closest('.item').querySelector('.price');
                    const subtotalElement = event.target.closest('.item').querySelector('.subtotal p');

                    if (itemPriceElement && subtotalElement) {
                        // Ambil harga barang, abaikan format teks
                        const price = parseInt(itemPriceElement.textContent.replace(/[^\d]/g, ''));
                        const subtotal = price * quantity;
                        subtotalElement.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
                    }
                }
            }
        });
    }
});

function updateQuantity(id_cart, action) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_cart: id_cart, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload the page to see updated cart
        } else {
            alert('Gagal memperbarui jumlah.');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function removeFromCart(id_cart) {
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_cart: id_cart })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload the page to see updated cart
        } else {
            alert('Gagal menghapus produk dari keranjang.');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function showModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'block';
    console.log("Modal shown"); // Tambahkan log untuk debugging
}

function closeModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'none';
    console.log("Modal closed"); // Tambahkan log untuk debugging
}
</script>