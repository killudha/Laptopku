<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

// Kunci API reCAPTCHA (sesuaikan sesuai key Anda)
$captchaSecretKey = '6LdhSpkqAAAAALp0HriufOdJgZziqOHMRD6pnlU7'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captchaResponse = $_POST['g-recaptcha-response'];
    $error = '';

    // Validasi CAPTCHA
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $captchaSecretKey . '&response=' . $captchaResponse);
    $responseData = json_decode($verifyResponse, true);    

    if (!$responseData['success']) {
        // Jika CAPTCHA gagal
        $errorMessage = 'CAPTCHA tidak valid. Kode error: ' . implode(', ', $responseData['error-codes']);
    } else {
        try {
            // Query untuk mencari user berdasarkan username
            $sql = "SELECT id_user, username, email, password, role_id_user FROM users WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validasi user
            if ($user && password_verify($password, $user['password'])) {
                // Simpan data user ke dalam sesi
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role_id_user'] = $user['role_id_user'];

                // Redirect berdasarkan role
                if ((int)$user['role_id_user'] === 2) {
                    header('Location: /Laptopku/Admin/Dashboard/dashboard.php'); // Halaman admin
                } else {
                    header('Location: /Laptopku/PBL/home-login.php'); // Halaman customer
                }
                exit();
            } else {
                $error = "Username atau password salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Login - Laptopku.</title>
</head>
<body>

<section class="hero">
    <div class="container-form">
        <div class="form-section">
            <h1>Login to Laptopku.</h1>
            <form method="POST" action="">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="g-recaptcha" data-sitekey="6LdhSpkqAAAAAAcqMknz2htA7IIfCsuEYhcpXUZG"></div>
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
                <?php endif; ?>
                <button type="submit" class="create-button mt-3">Login</button>
                <div class="login-link">
                    <span>Belum memiliki akun? </span>
                    <a href="crAkun.php"><u>Create Account</u></a>
                </div>
            </form>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="content-section">
        <h2 class="content-title">
            <span>Welcome</span> Back 
            <span>!!</span>
        </h2>
        <div class="hero-image">
            <img src="img/Laptop2.png" alt="Laptop" class="laptop-img">
        </div>
        <p class="content-description">
            Selamat datang kembali! Masuk untuk melanjutkan pencarian laptop sempurna Anda.
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
        Copyright © 2024 Laptopku
    </div>
</section>

<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>
