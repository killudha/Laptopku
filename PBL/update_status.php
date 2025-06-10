<?php
include 'C:\xampp\htdocs\Laptopku\config.php'; // Hubungkan ke database

// Ambil data dari request body
$data = json_decode(file_get_contents('php://input'), true);

// Periksa apakah id_status telah diterima dari request
if (isset($data['id_status'])) {
    $id_status = $data['id_status'];

    try {
        // Query untuk memperbarui status delivery menjadi 'completed'
        $query = "UPDATE Status_Orders SET status_delivery = 'completed' WHERE id_status = :id_status";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_status', $id_status, PDO::PARAM_INT);

        // Eksekusi query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status pesanan berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status pesanan.']);
        }
    } catch (PDOException $e) {
        // Tangkap dan tangani kesalahan
        echo json_encode(['success' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
    }
} else {
    // Tanggapi jika id_status tidak ditemukan dalam permintaan
    echo json_encode(['success' => false, 'message' => 'ID status tidak ditemukan.']);
}
?>
