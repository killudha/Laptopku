<?php
// Memasukkan file konfigurasi untuk koneksi database
include('C:/xampp/htdocs/Laptopku/Admin/config.php');

// Mendapatkan ID produk dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data produk berdasarkan id
    $stmt = $conn->prepare("SELECT * FROM products WHERE id_product = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Mengambil hasil query
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Produk tidak ditemukan.";
        exit();
    }
} else {
    echo "ID produk tidak tersedia.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Laptopku</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h2>Edit Produk</h2>
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_produk" value="<?php echo $product['id_product']; ?>">
            <div class="form-group">
                <label for="merk">Merek</label>
                <input type="text" class="form-control" id="merk" name="merk" value="<?php echo htmlspecialchars($product['merk']); ?>" required>
            </div>
            <div class="form-group">
                <label for="variety">Tipe</label>
                <input type="text" class="form-control" id="variety" name="variety" value="<?php echo htmlspecialchars($product['variety']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ssd_hdd">SSD/HDD</label>
                <input type="text" class="form-control" id="ssd_hdd" name="ssd_hdd" value="<?php echo htmlspecialchars($product['ssd_hdd']); ?>" required>
            </div>
            <div class="form-group">
                <label for="processor">Processor</label>
                <input type="text" class="form-control" id="processor" name="processor" value="<?php echo htmlspecialchars($product['processor']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ram">RAM</label>
                <input type="text" class="form-control" id="ram" name="ram" value="<?php echo htmlspecialchars($product['ram']); ?>" required>
            </div>
            <div class="form-group">
                <label for="vga">VGA</label>
                <input type="text" class="form-control" id="vga" name="vga" value="<?php echo htmlspecialchars($product['vga']); ?>" required>
            </div>
            <div class="form-group">
                <label for="screen_size">Screen Size</label>
                <input type="text" class="form-control" id="screen_size" name="screen_size" value="<?php echo htmlspecialchars($product['screen_size']); ?>" required>
            </div>
            <div class="form-group">
                <label for="storages">Storage</label>
                <input type="text" class="form-control" id="storages" name="storages" value="<?php echo htmlspecialchars($product['storages']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="purpose">Tujuan</label>
                <input type="text" class="form-control" id="purpose" name="purpose" value="<?php echo htmlspecialchars($product['purpose']); ?>" required>
            </div>
            <div class="form-group">
                <label for="feature">Fitur</label>
                <textarea class="form-control" id="feature" name="feature" required><?php echo htmlspecialchars($product['feature']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image_path">Upload Gambar</label>
                <input type="file" class="form-control" id="image_path" name="image_path">
                <small>Biarkan kosong jika tidak ingin mengubah gambar</small>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Produk</button>
            <button class="btn btn-danger" onclick="window.history.back()">Kembali</button>
        </form>
    </div>
</body>
</html>
