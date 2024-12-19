<?php
// koneksi database
include 'C:\xampp\htdocs\Laptopku\config.php';  // Make sure this file contains your database connection logic

// variabel untuk pesan error
$errorMessage = "";

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // menangkap data yang dikirim dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // validasi username maksimal 20 karakter
    if (strlen($username) > 20) {
        $errorMessage .= "Username tidak boleh lebih dari 20 karakter. ";
    }

    // validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage .= "Email tidak valid. ";
    }

    // validasi password minimal 6 karakter
    if (strlen($password) < 6) {
        $errorMessage .= "Password harus minimal 6 karakter. ";
    }

    // cek apakah ada error
    if (empty($errorMessage)) {
        // jika tidak ada error, hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // masukkan data ke database
        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
        
        // Check if $conn is a valid mysqli object
        if (isset($conn) && $conn instanceof mysqli) {
            if (mysqli_query($conn, $query)) {
                // alihkan halaman kembali ke login.php atau halaman sukses
                header("Location: login.php");
                exit();
            } else {
                // jika ada error saat query
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        } else {
            $errorMessage = "Database connection failed.";
        }
    }
}

// Cek apakah $conn adalah objek mysqli sebelum menutup koneksi
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}
?>