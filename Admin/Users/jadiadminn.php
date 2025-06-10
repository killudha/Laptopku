<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

// Cek apakah ada ID pengguna yang diberikan
if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    try {
        // Update role_id menjadi 2 (Admin)
        $sql = "UPDATE users SET role_id_user = 2 WHERE id_user = :id_user";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();

        // Set pesan sukses dalam session
        $_SESSION['success_message'] = "Pengguna berhasil dijadikan admin.";
    } catch (PDOException $e) {
        // Tangani kesalahan jika ada
        $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Redirect kembali ke halaman pengguna
header('Location: /Laptopku/Admin/Pengguna/user.php');
exit();
?>