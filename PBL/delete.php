<?php
// Memasukkan file konfigurasi untuk koneksi database
include('C:/xampp/htdocs/Laptopku/Admin/config.php');

// Mendapatkan ID produk dari URL
$id_product = $_GET['id'];

// Menyiapkan query untuk menghapus produk berdasarkan ID
$query = "DELETE FROM products WHERE id_product = :id_product";

// Menyiapkan statement untuk query
$stmt = $conn->prepare($query);

// Mengikat parameter
$stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);

// Menjalankan query
if ($stmt->execute()) {
    // Jika berhasil, redirect ke halaman produk dengan pesan sukses
    header("Location: product-admin.php?message=Produk berhasil dihapus");
    exit(); // Pastikan tidak ada kode yang dijalankan setelah redirect
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error: " . $stmt->errorInfo()[2];
}

// Menutup koneksi (PDO biasanya tidak perlu ditutup secara eksplisit)
?>
