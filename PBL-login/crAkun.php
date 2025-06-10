<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

$errorMessage = '';

// Kunci API reCAPTCHA (sesuaikan sesuai key Anda)
$captchaSecretKey = '6LdhSpkqAAAAALp0HriufOdJgZziqOHMRD6pnlU7'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $role_id = 1; // Default role_id untuk Customer
    $captchaResponse = $_POST['g-recaptcha-response'];

    // Validasi CAPTCHA
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $captchaSecretKey . '&response=' . $captchaResponse);
    $responseData = json_decode($verifyResponse, true);

    if (!$responseData['success']) {
        // Jika CAPTCHA gagal
        $errorMessage = 'CAPTCHA tidak valid. Kode error: ' . implode(', ', $responseData['error-codes']);
    } else {
        // Validasi input
        if (empty($username) || empty($email) || empty($password) || empty($telepon) || empty($alamat)) {
            $errorMessage = "Semua field harus diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Email tidak valid.";
        } elseif (strlen($password) < 6) {
            $errorMessage = "Password harus minimal 6 karakter.";
        } elseif (!ctype_digit($telepon)) {
            $errorMessage = "Nomor telepon harus berupa angka.";
        } else {
            try {
                // Cek apakah username sudah ada
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $errorMessage = "Username sudah terdaftar.";
                } else {
                    // Hash password dan masukkan ke database
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password, telepon, alamat, role_id_user) 
                                            VALUES (:username, :email, :password, :telepon, :alamat, :role_id_user)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':telepon', $telepon);
                    $stmt->bindParam(':alamat', $alamat);
                    $stmt->bindParam(':role_id_user', $role_id);
                    $stmt->execute();

                    // Redirect setelah sukses
                    header('Location: /Laptopku/PBL-login/login.php'); // Ganti sesuai kebutuhan
                    exit();
                }
            } catch (PDOException $e) {
                // Menangani kesalahan jika terjadi
                $errorMessage = "Terjadi kesalahan pada sistem: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style-login.css">
    <link rel="stylesheet" href="footer.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Create Account - Laptopku.</title>
</head>
<body>

<section class="hero">
    <div class="container-form">
        <div class="form-section">
            <h1>Create Account</h1>
            <form method="POST" action="crAkun.php">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-phone"></i>
                    <input type="text" name="telepon" placeholder="No.Telepon" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-map-marker"></i>
                    <input type="textarea" name="alamat" placeholder="Alamat" required>
                </div>
                <div class="g-recaptcha" data-sitekey="6LdhSpkqAAAAAAcqMknz2htA7IIfCsuEYhcpXUZG"></div>
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
                <?php endif; ?>
                <button type="submit" class="create-button mt-3">Create Account</button>
                <div class="login-link">
                    <span>Sudah memiliki akun? </span>
                    <a href="login.php"><u>Login</u></a>
                </div>
            </form>
        </div>     
    </div> 
    <div class="content-section">
        <h2 class="content-title">
            <span>Bergabunglah</span> untuk<br>
            Solusi Laptop<br>
            <span>Sempurna</span>
        </h2>
        <div class="hero-image">
            <img src="img/Laptop2.png" alt="Laptop" class="laptop-img">
        </div>
        <p class="content-description">
            Buat akun Anda sekarang dan dapatkan akses ke rekomendasi laptop terbaik yang sesuai dengan kebutuhan Anda.
        </p>
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
        Copyright 2024 Laptopku
    </div>
</section>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
