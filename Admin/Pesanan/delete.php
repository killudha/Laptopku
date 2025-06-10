<?php
// Memasukkan file koneksi database
require_once 'C:\xampp\htdocs\Laptopku\Admin\config.php';

// Memeriksa apakah parameter id_order ada di URL
if (isset($_GET['id_order'])) {
    $idOrder = $_GET['id_order'];

    // Query untuk menghapus pesanan
    $deleteQuery = "DELETE FROM orders WHERE id_order = :id_order";
    $stmt = $conn->prepare($deleteQuery);

    // Eksekusi query
    if ($stmt->execute([':id_order' => $idOrder])) {
        // Redirect kembali ke halaman utama setelah penghapusan
        header('Location: order.php?message=deleted');
        exit();
    } else {
        echo "Gagal menghapus pesanan.";
    }
} else {
    echo "ID pesanan tidak ditemukan.";
}
?>
