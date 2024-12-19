<?php
include 'C:\xampp\htdocs\Laptopku\Admin\config.php'; // Hubungkan ke database

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $merk = $_POST['merk'];
    $variety = $_POST['variety'];
    $ssd_hdd = $_POST['ssd_hdd'];
    $processor = $_POST['processor'];
    $ram = $_POST['ram'];
    $vga = $_POST['vga'];
    $screen_size = $_POST['screen_size'];
    $storages = $_POST['storages'];
    $price = $_POST['price'];
    $purpose = $_POST['purpose'];
    $feature = $_POST['feature'];
    $stock = $_POST['stock'];

    // Proses upload gambar
    $target_dir = "productimages/";
    $target_file = $target_dir . basename($_FILES["image_path"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar
    $check = getimagesize($_FILES["image_path"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["image_path"]["size"] > 5000000) { // 500KB
        $uploadOk = 0;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $uploadOk = 0;
    }

    // Cek jika $uploadOk di-set ke 0 oleh kesalahan
    if ($uploadOk == 0) {
        // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
    } else {
        // Jika semua cek lolos, coba upload file
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            // Simpan data ke database menggunakan prepared statements
            $stmt = $conn->prepare("INSERT INTO products (merk, variety, ssd_hdd, processor, ram, vga, screen_size, storages, price, purpose, feature, image_path, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Eksekusi statement dengan parameter
            if ($stmt->execute([$merk, $variety, $ssd_hdd, $processor, $ram, $vga, $screen_size, $storages, $price, $purpose, $feature, $target_file, $stock])) {
                // Redirect ke halaman product.php setelah berhasil menambahkan produk
                header("Location: product-admin.php");
                exit(); // Pastikan untuk keluar setelah redirect
            } else {
                // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
            }
        } else {
            // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
        }
    }
}

// Tidak perlu menutup koneksi secara eksplisit, PDO akan menutupnya secara otomatis saat skrip selesai
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Laptopku</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h2>Tambah Produk</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="merk">Merek</label>
                <input type="text" class="form-control" id="merk" name="merk" required>
            </div>
            <div class="form-group">
                <label for="variety">Tipe</label>
                <input type="text" class="form-control" id="variety" name="variety" required>
            </div>
            <div class="form-group">
                <label for="ssd_hdd">SSD/HDD</label>
                <input type="text" class="form-control" id="ssd_hdd" name="ssd_hdd" required>
            </div>
            <div class="form-group">
                <label for="processor">Processor</label>
                <input type="text" class="form-control" id="processor" name="processor" required>
            </div>
            <div class="form-group">
                <label for="ram">RAM</label>
                <input type="text" class="form-control" id="ram" name="ram" required>
            </div>
            <div class="form-group">
                <label for="vga">VGA</label>
                <input type="text" class="form-control" id="vga" name="vga" required>
            </div>
            <div class="form-group">
                <label for="screen_size">Screen Size</label>
                <input type="text" class="form-control" id="screen_size" name="screen_size" required>
            </div>
            <div class="form-group">
                <label for="storages">Storage</label>
                <input type="text" class="form-control" id="storages" name="storages" required>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="purpose">Tujuan</label>
                <input type="text" class="form-control" id="purpose" name="purpose" required>
            </div>
            <div class="form-group">
                <label for="feature">Fitur</label>
                <textarea class="form-control" id="feature" name="feature" required></textarea>
            </div>
            <div class="form-group">
                <label for="image_path">Gambar Produk</label>
                <input type="file" class="form-control" id="image_path" name="image_path" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Produk</button>
            <button class="btn btn-danger" onclick="window.history.back()">Kembali</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>