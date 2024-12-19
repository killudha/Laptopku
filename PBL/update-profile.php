<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

$id_user = $_SESSION['id_user']; // Pastikan id_user sudah disimpan di sesi saat login

// Mengambil data pengguna dari database
$sql = "SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah data pengguna ditemukan
if (!$user) {
    die("User  not found.");
}

// Mengambil data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['username'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $image_path = $_POST['image_path'];

    // Validasi data
    if (empty($nama) || empty($email) || empty($telepon) || empty($alamat)) {
        die("Semua field harus diisi.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email tidak valid.");
    }

    // Menyiapkan dan mengeksekusi pernyataan SQL untuk memperbarui data
    $sql = "UPDATE users SET username=?, email=?, telepon=?, alamat=?";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Pastikan folder ini ada dan dapat ditulis
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        // Validasi tipe file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.");
        }
    
        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Jika berhasil, tambahkan path gambar ke query
            $sql .= ", image_path=?";
            $params = [$nama, $email, $telepon, $alamat, $target_file, $id_user];
        } else {
            die("Maaf, terjadi kesalahan saat mengunggah gambar.");
        }
    } else {
        // Jika tidak ada gambar yang diunggah, tetap gunakan parameter yang ada
        $params = [$nama, $email, $telepon, $alamat, $id_user];
    }

    // Menyelesaikan pernyataan SQL
    $sql .= " WHERE id_user=?";
    
    // Menyiapkan dan mengeksekusi pernyataan SQL
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Set pesan sukses
    $_SESSION['success_message'] = "Profil berhasil diperbarui!";
    
    // Redirect ke halaman profil atau halaman lain
    header("Location: profile.php");
    exit();
}
?>