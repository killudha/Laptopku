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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home - Laptopku.</title>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="#abaoutus">About Us</a>
            <a href="#contact">Contact</a>
            <a href="product-login.php">Shopping</a>
            <a href="cart.php" class="cartimg"><img src="img/chart-icon.png" alt="profile user"></a>
            <a href="profile.php"><img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="logout.php"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">
                <span>Solusi Tepat</span> untuk<br>
                Memilih Laptop yang<br>
                <span>Sempurna.</span>
            </h1>
            <p class="hero-text">
                Kami bantu kamu menemukan laptop sesuai kebutuhan dengan
                cepat dan mudah.<b class="hero-text"> Pilih, bandingkan, dan beli sekarang juga!</b>
            </p>
            <div class="cta-button">
            <a href="product-login.html">Beli Sekarang!</a>
            <a href="product-login.html"><i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="img/laptop1.png" alt="Laptop" class="laptop-img">
            <div class="circle-bg circle-blue"></div>
            <div class="circle-bg circle-green"></div>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <span class="stat-number">10+</span>
            <span class="stat-label">Years of Experience</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">4,5</span>
            <span class="stat-label">Overall Rating</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">10K+</span>
            <span class="stat-label">Happy Customers</span>
        </div>
    </section>

    <section class="container-produk">
        <div class="ourproduct">
            <h1>Our <span>Product</span></h1></div>
            <a href="product-login.php" class="view-more">View More ></a>
        
        <div class="product-grid">
            <!-- Product Card 1 -->
            <div class="product-card">
                <img src="img/vivobook.png" alt="Asus Vivobook" class="product-image">
                <h2 class="product-title">Asus Vivobook</h2>
                <p class="product-price">Rp.8.500.000</p>
                <div class="button-group">
                    <button class="cart-btn">
                        <img src="img/chart-icon.png" alt="Cart">
                    </button>
                    <button class="details-btn" onclick="window.location.href='detail-login.html';">Get Details</button>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="product-card">
                <img src="img/vivobook.png" alt="Asus Vivobook" class="product-image">
                <h2 class="product-title">Asus Vivobook</h2>
                <p class="product-price">Rp.8.500.000</p>
                <div class="button-group">
                    <button class="cart-btn">
                        <img src="img/chart-icon.png" alt="Cart">
                    </button>
                    <button class="details-btn" onclick="window.location.href='detail-login.html';">Get Details</button>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="product-card">
                <img src="img/vivobook.png" alt="Asus Vivobook" class="product-image">
                <h2 class="product-title">Asus Vivobook</h2>
                <p class="product-price">Rp.8.500.000</p>
                <div class="button-group">
                    <button class="cart-btn">
                        <img src="img/chart-icon.png" alt="Cart">
                    </button>
                    <button class="details-btn" onclick="window.location.href='detail-login.html';">Get Details</button>
                </div>
            </div>
        </div>
    </section>

    <!--About us-->
    <section id="abaoutus"  class="container-about">
        <div class="content">
          <h2>About Us</h2>
          <h1>LAPTOPKU.</h1>
          <p><span class="red-text">Solution</span> for finding the <span class="green-text">perfect laptop</span></p>
        </div>
        <div class="content2">
          <div class="content-wrapper">
            <p>Selamat datang di <span class="green-text">Laptopku!</span> toko online dengan pilihan laptop berkualitas untuk berbagai kebutuhan. Kami memudahkan Anda menemukan laptop yang tepat dengan proses pembelian cepat dan praktis. Nikmati pelayanan terbaik dan harga bersaing hanya di Laptopku!</p>
            <div class="cta-button">
                <a href="#contacts">Kontak Kami!</a>
                <a href="#contact"><i class="fa fa-arrow-right"></i></a>
            </div>
          </div>
          <div class="laptop-image">
            <img src="img/laptop2.png" alt="Laptop Image">
          </div>
        </div>
    </section>

    <section class="promo">
        <h1>Laptopku.</h1>
        <h1>Laptopku.</h1>
        <h1>Laptopku.</h1>
        <h1>Laptopku.</h1>
        <h1>Laptopku.</h1>
        <h1>Laptopku.</h1>
    </section>

    <section>
    <!--Why choose us-->
    <div class="container">
        <!-- Why Choose Us Section -->
        <h2 class="title">Why <span class="green-text-title">Choose Us</span></h2>

        <!-- Features Grid -->
        <div class="features">
            <!-- Feature 1 -->
            <div class="feature">
                <div class="icon-circle">
                    <i class="fas fa-laptop"></i>
                </div>
                <p>Pilihan <span class="blue-text">Laptop</span><br>
                yang <span class="green-text">Beragam</span></p>
            </div>

            <!-- Feature 2 -->
            <div class="feature">
                <div class="icon-circle">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p>Jaminan <span class="green-text">Garansi <br> 2 Tahun</span></p>
            </div>

            <!-- Feature 3 -->
            <div class="feature">
                <div class="icon-circle">
                    <i class="fas fa-certificate"></i>
                </div>
                <p>Barang <span class="blue-text">Original</span></p>
            </div>
        </div>
    </section>

        <section id="contact">
        <!-- Contact Form Section -->
        <div class="contact-container">
            <div class="form-section">
                <h3>Contact Us</h3>
                <form>
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email">
                    </div>
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea></textarea>
                    </div>
                    <button class="submit-btn">Send Now</button>
                </form>
            </div>
            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80"
                         alt="Laptop Image" 
                         class="w-full h-full object-cover">
        </div>
    </div>
    </section>

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