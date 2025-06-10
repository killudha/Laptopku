<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

require 'C:\xampp\htdocs\Laptopku\config.php';
$id_user = $_SESSION['id_user'];

try {
    // Ambil data pengguna
    $stmt = $conn->prepare("SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user = :id_user");
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if (!$user) {
        die("User not found.");
        exit();
    }
    $default_image = "img/pp.png";
    $image_path = !empty($user['image_path']) ? $user['image_path'] : $default_image;

    $shippingFee = 15000;
    $cartItems = [];
    $totalOrder = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cart'])) {
        foreach ($_POST['id_cart'] as $key => $id_cart) {
            $id_cart = filter_var($id_cart, FILTER_SANITIZE_NUMBER_INT);

            $stmt = $conn->prepare("
                SELECT id_product, quantity
                FROM Cart
                WHERE id_cart = :id_cart AND id_user = :id_user
            ");
            $stmt->execute([
                ':id_cart' => $id_cart,
                ':id_user' => $id_user
            ]);

            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cartItem) {
                $id_product = $cartItem['id_product'];
                $quantity = $cartItem['quantity'];

                $stmt = $conn->prepare("
                    SELECT merk, variety, price
                    FROM Products
                    WHERE id_product = :id_product
                ");
                $stmt->execute([':id_product' => $id_product]);

                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $subtotal = $product['price'] * $quantity;

                    $cartItems[] = [
                        'id_product' => $id_product,
                        'merk' => $product['merk'],
                        'variety' => $product['variety'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'id_cart' => $id_cart
                    ];
                    $totalOrder += $subtotal;
                }
            }
        }
    }

    $totalOrderWithShipping = $totalOrder + $shippingFee;

    if (isset($_POST['payment-submit'])) {
        $conn->beginTransaction();
    
        try {
            // Periksa stok terlebih dahulu
            foreach ($cartItems as $item) {
                $stmt = $conn->prepare("
                    SELECT stock FROM Products WHERE id_product = :id_product
                ");
                $stmt->execute([':id_product' => $item['id_product']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception('Stock Habis untuk produk: ' . $item['merk']);
                }
            }
    
            // Jika stok cukup, lakukan transaksi
            foreach ($cartItems as $item) {
                // Kurangi stok produk
                $stmt = $conn->prepare("
                    UPDATE Products SET stock = stock - :quantity WHERE id_product = :id_product
                ");
                $stmt->execute([
                    ':quantity' => $item['quantity'],
                    ':id_product' => $item['id_product']
                ]);
    
                // Masukkan ke tabel Orders
                $stmt = $conn->prepare("
                    INSERT INTO Orders (id_user, id_product, recipent_name, product_price, total_price, shipping_type, payment_status) 
                    VALUES (:id_user, :id_product, :recipent_name, :product_price, :total_price, :shipping_type, 'paid')
                ");
                $stmt->execute([
                    ':id_user' => $id_user,
                    ':id_product' => $item['id_product'],
                    ':recipent_name' => $user['username'],
                    ':product_price' => $item['price'],
                    ':total_price' => $item['subtotal'] + $shippingFee,
                    ':shipping_type' => 'JNE/J&T'
                ]);
    
                $id_order = $conn->lastInsertId();
    
                // Masukkan ke tabel Status_Orders
                $stmt = $conn->prepare("
                    INSERT INTO Status_Orders (id_order, status_delivery, order_date) 
                    VALUES (:id_order, 'packaged', CURRENT_DATE)
                ");
                $stmt->execute([':id_order' => $id_order]);
    
                // Masukkan ke tabel Out_Product
                $stmt = $conn->prepare("
                    INSERT INTO Out_Product (id_product, quantity, out_date)
                    VALUES (:id_product, :quantity, CURRENT_DATE)
                ");
                $stmt->execute([
                    ':id_product' => $item['id_product'],
                    ':quantity' => $item['quantity']
                ]);
            }
    
            // Hapus item dari Cart
            $stmt = $conn->prepare("DELETE FROM Cart WHERE id_user = :id_user");
            $stmt->execute([':id_user' => $id_user]);
    
            $conn->commit();
            echo "<script>showModal('successModal');</script>";
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            echo "<script>alert('" . $e->getMessage() . "'); showModal('failedModal');</script>";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
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
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Payment - Page</title>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="profile.php"><img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="home.php"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>
    <!-- Payment Method -->
<div class="container">
    <h2 class="title"><span class="green-text-title">Payment</span> Method</h2>
    <a href="cart.php">
    <button class="back">
        <i class="fa fa-angle-left"></i>
    </button></a>
    <div class="card-pay">
        <div class="content-payment">
            <div class="select-payment">
                <form action="#">
                    <input type="radio" name="payment" id="mandiri">
                    <input type="radio" name="payment" id="bca">
                    <input type="radio" name="payment" id="bri">
                    <div class="category">
                        <label for="mandiri" class="mandiriMethod">
                            <div class="imgName">
                                <div class="imgContainer">
                                    <img src="img/mandiri.png" alt="Bank Mandiri">
                                </div>
                            </div>
                            <span class="check"><i class="fa-solid fa-circle-check" style="color: #45a33a;"></i></span>
                        </label>
                        <label for="bca" class="bcaMethod">
                            <div class="imgName">
                                <div class="imgContainer">
                                    <img src="img/bca.png" alt="Bank Mandiri">
                                </div>
                            </div>
                            <span class="check"><i class="fa-solid fa-circle-check" style="color: #45a33a;"></i></span>
                        </label>
                        <label for="bri" class="briMethod">
                            <div class="imgName">
                                <div class="imgContainer">
                                    <img src="img/bri.png" alt="Bank Mandiri">
                                </div>
                            </div>
                            <span class="check"><i class="fa-solid fa-circle-check" style="color: #45a33a;"></i></span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="form-payment">
                            <h2 class="payment-title">Total Akhir: <span class="total-amount">Rp <?= number_format($totalOrderWithShipping, 0, ',', '.') ?></span></h2>
                <!-- payment button -->
                <form action="proses_payment.php" method="POST" onsubmit="return validatePaymentForm()">
                <div class="payment-input">
                                <label for="name">Nama Kartu :</label>
                                <input type="text" id="name" name="name" required>
                                <label for="card-number">Nomor Kartu :</label>
                                <input type="text" id="card-number" name="card_number" required>
                                <div class="expiration-cvv">
                                    <div class="expiry">
                                        <label for="month">Bulan :</label>
                                        <input type="text" id="month" name="month" required>
                                    </div>
                                    <div class="expiry">
                                        <label for="year">Tahun :</label>
                                        <input type="text" id="year" name="year" required>
                                    </div>
                                    <div class="cvv">
                                        <label for="cvv">CVV :</label>
                                        <input type="text" id="cvv" name="cvv" required>
                                    </div>
                                </div>
                            </div>
                            <?php foreach ($cartItems as $item): ?>
    <input type="hidden" name="id_cart[]" value="<?= htmlspecialchars($item['id_cart']) ?>">
    <input type="hidden" name="id_product[]" value="<?= htmlspecialchars($item['id_product']) ?>">
    <input type="hidden" name="price[]" value="<?= htmlspecialchars($item['price']) ?>">
    <input type="hidden" name="quantity[]" value="<?= htmlspecialchars($item['quantity']) ?>">
    <input type="hidden" name="subtotal[]" value="<?= htmlspecialchars($item['subtotal']) ?>">
<?php endforeach; ?>
                            <div class="btn-pay">
                                <button type="button" class="back-button" onclick="window.location.href='home-login.php';">Bayar Nanti</button>
                                <button type="submit" name="payment-submit" class="pay-button">Bayar Sekarang</button>
                            </div>
                        </div>
                    </form>

            </div>
        </div>
    </div>
</div>
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Pembayaran Berhasil!</h2>
        <p>Pesanan Anda sedang dikemas.</p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>
<div id="failedModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Pembayaran Gagal!</h2>
        <p>Terjadi kesalahan saat memproses pembayaran Anda.</p>
        <button onclick="closeModal()">OK</button>
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
            Copyright &copy; 2024 Laptopku
        </div>
    </section>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Panggil fungsi ini saat halaman dimuat untuk menghitung subtotal awal
        updateShippingCost();
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
                        // Panggil fungsi untuk memperbarui subtotal dan total keseluruhan
                        updateShippingCost();
                    }
                }
            });
        }
    });
    function updateShippingCost() {
    const shippingFee = 15000; // Biaya pengiriman tetap
    const shippingCostElement = document.getElementById('shipping-cost').querySelector('span');
    const subtotalElement = document.getElementById('subtotal').querySelector('span');
    const orderTotalElement = document.getElementById('order-total').querySelector('span');
    // Hitung subtotal produk
    let subtotal = 0;
    const cartItems = document.querySelectorAll('.item');
    cartItems.forEach(item => {
        const quantity = parseInt(item.querySelector('.quantity').textContent);
        const price = parseInt(item.querySelector('.price').textContent.replace(/[^\d]/g, ''));
        subtotal += price * quantity;
    });
    // Update subtotal dan total keseluruhan
    subtotalElement.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
    const total = subtotal + shippingFee;
    orderTotalElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    // Update nilai hidden input untuk form submission
    document.getElementById('hidden-subtotal').value = subtotal;
    document.getElementById('hidden-total').value = total;
}
function updateQuantity(id_cart, action) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_cart: id_cart, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload halaman setelah perubahan
        } else {
            alert('Gagal memperbarui jumlah.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function removeFromCart(id_cart) {
    fetch('proses_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_cart: id_cart })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload halaman setelah produk dihapus
        } else {
            alert('Gagal menghapus produk.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    checkPaymentStatus();
});

function validatePaymentForm() {
    const cardNumber = document.getElementById('card-number').value;
    const cvv = document.getElementById('cvv').value;
    const month = document.getElementById('month').value;
    const year = document.getElementById('year').value;

    const cardNumberRegex = /^[0-9]{16}$/;
    const cvvRegex = /^[0-9]{3,4}$/;
    const monthRegex = /^(0[1-9]|1[0-2])$/;
    const yearRegex = /^[0-9]{4}$/;

    if (!cardNumberRegex.test(cardNumber)) {
        alert("Nomor kartu tidak valid!");
        return false;
    }
    if (!cvvRegex.test(cvv)) {
        alert("CVV tidak valid!");
        return false;
    }
    if (!monthRegex.test(month)) {
        alert("Bulan tidak valid!");
        return false;
    }
    if (!yearRegex.test(year) || year < new Date().getFullYear()) {
        alert("Tahun tidak valid!");
        return false;
    }
    return true;
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
}

window.addEventListener('click', function (event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

function checkPaymentStatus() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status === 'success') {
        showModal('successModal');
    } else if (status === 'failed') {
        showModal('failedModal');
    }
}

</script>