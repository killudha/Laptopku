<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

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
$id_user = $_SESSION['id_user']; // Ambil id_user dari session

// Query untuk mengambil ulasan berdasarkan id_user
$query = "
    SELECT r.id_review, r.rating, r.review_text, r.review_date, u.username 
    FROM Review r
    JOIN Users u ON r.id_user = u.id_user
    WHERE r.id_user = :id_user
    ORDER BY r.review_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ambil semua ulasan
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
    <title>Review - Laptopku.</title>
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
                <a href="logout.php">Sign Out</a>           
            </div>     
        </div> 
        <div class="main-content">
            <div class="title">Ulasan</div>
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="order-card">
                        <div class="btn-view">
                            <button class="view-button">Lihat</button>
                        </div>
                        <div class="review-info">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="user" style="width:150px; height:150px; border-radius:50%">
                            <div class="detail-review">
                                <p><span class="rate"><?= htmlspecialchars($review['rating']) ?></span></p>
                                <p><strong><?= htmlspecialchars($review['username']) ?></strong></p>
                                <p><?= htmlspecialchars($review['review_text']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews available for you.</p>
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
</body>
</html>
