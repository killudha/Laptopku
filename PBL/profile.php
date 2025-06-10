<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php'); // Redirect ke login jika belum login
    exit();
}

include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

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
    <title>Profile - Laptopku.</title>
    <script>
    // Fungsi untuk menampilkan pesan konfirmasi
    function showMessage() {
        const message = "<?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : ''; ?>";
        if (message) {
            document.getElementById('successModal').style.display = 'block'; // Tampilkan modal
            <?php unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan ?>
        }
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('successModal').style.display = 'none'; // Sembunyikan modal
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

    <!-- Modal untuk pesan sukses -->
    <div id="successModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-icon">
                <i class="fas fa-check-circle" style="color: green; font-size: 50px;"></i>
            </div>
            <h2>Update Berhasil!</h2>
            <p><?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : ''; ?></p>
            <button class="modal-button-green" onclick="closeModal()">OK</button>
        </div>
    </div>

    <section class="hero">
        <div class="menu">
            <div class="menu-container">
                <p onclick="window.location.href='profile.php';"><span class="pay"></span>Profile</span></p>
                <p onclick="window.location.href='pesanan.php';">Pesanan</p>
                <p onclick="window.location.href='review.php';">Ulasan</p>
                <p onclick="window.location.href='riwayat.php';">Riwayat</p>
                <hr>
                <a href="logout.php">Sign Out</a>            
            </div>     
        </div> 

        <div class="profile-container">
            <div class="title">Profile</div>
            <div class="gabunginfo">
                <div class="content-image">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:230px; height:230px; border-radius:50%; align-items: center; margin-top: 3.5rem; margin-left: 2.5rem;">
                </div>
                <div class="profile-info">
                    <p><span class="profile-label">Nama :</span><br> <?= htmlspecialchars($user['username']) ?></p>
                    <p><span class="profile-label">Email :</span><br> <?= htmlspecialchars($user['email']) ?></p>
                    <p><span class="profile-label">Telepon :</span><br><?= htmlspecialchars($user['telepon']) ?></p>
                    <p><span class="profile-label">Alamat :</span><br> <?= htmlspecialchars($user['alamat']) ?></p>
                    <div class="btn-save">
                        <button class="edit-button" onclick="window.location.href='profile2.php';">Edit Profile</button>
                    </div>
                </div>
            </div>
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
    </body>
    </html>
</body>
</html>
