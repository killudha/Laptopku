<?php
// Memasukkan file koneksi database
require_once 'C:\xampp\htdocs\Laptopku\Admin\config.php';

// Memeriksa apakah parameter id_order ada di URL
if (isset($_GET['id_order'])) {
    $idOrder = $_GET['id_order'];

    // Query untuk mengambil data pesanan
    $fetchQuery = "SELECT * FROM orders WHERE id_order = :id_order";
    $stmt = $conn->prepare($fetchQuery);
    $stmt->execute([':id_order' => $idOrder]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika form dikirim, lakukan update data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $recipientName = $_POST['recipient_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $status = $_POST['status'];

        // Query untuk mengupdate data pesanan
        $updateQuery = "UPDATE orders SET recipient_name = :recipient_name, phone = :phone, address = :address, status = :status WHERE id_order = :id_order";
        $stmt = $conn->prepare($updateQuery);

        // Eksekusi query dengan data yang diperbarui
        if ($stmt->execute([
            ':recipient_name' => $recipientName,
            ':phone' => $phone,
            ':address' => $address,
            ':status' => $status,
            ':id_order' => $idOrder
        ])) {
            // Redirect kembali ke halaman utama setelah pembaruan
            header('Location: order.php?message=updated');
            exit();
        } else {
            echo "Gagal memperbarui pesanan.";
        }
    }
} else {
    echo "ID pesanan tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Pesanan</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="recipient_name" class="form-label">Nama Penerima</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="<?php echo htmlspecialchars($order['recipient_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($order['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" required><?php echo htmlspecialchars($order['address']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pending" <?php echo ($order['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo ($order['status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Done" <?php echo ($order['status'] === 'Done') ? 'selected' : ''; ?>>Done</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="order.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
