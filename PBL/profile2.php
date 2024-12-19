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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profile - Laptopku.</title>
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
                <hr>
                <a href="home.php"><u>Sign Out</u></a>            
            </div>     
        </div> 

        <div class="profile-container">
            <div class="title">Edit Profile</div>
            <div class="gabunginfo">
                <div class="content-image">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:230px; height:230px; border-radius:50%; align-items: center; margin-top: 3.5rem; margin-left: 2.5rem;">
                </div>
                <div class="form-profile">
                    <form action="update-profile.php" method="POST" enctype="multipart/form-data">
                        <label for="username">Username :</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">

                        <label for="email">Email :</label>
                        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

                        <label for="telepon">Telepon :</label>
                        <input type="text" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user['telepon']); ?>">

                        <label for="alamat">Alamat :</label>
                        <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($user['alamat']); ?>">

                        <label for="image">Gambar Profil:</label>
                        <input type="file" name="image" accept="image/*">

                        <div class="btn-profile">
                            <div class="btn-reset">
                                <button type="button" class="reset-button" onclick="window.location.href='profile.php'">Batal</button>
                            </div>
                            <div class="btn-save">
                                <button type="submit" class="save-button">Save Profile</button>
                            </div>
                        </div> 
                    </form>
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

