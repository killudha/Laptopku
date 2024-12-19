<?php
include 'C:\xampp\htdocs\Laptopku\Admin\config.php'; // Hubungkan ke database

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_produk = $_POST['id_produk'];
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

    // Cek apakah ada gambar baru yang diunggah
    if (!empty($_FILES["image_path"]["name"])) {
        // Proses upload gambar
        $target_dir = "productimages/";
        $target_file = $target_dir . basename($_FILES["image_path"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file gambar adalah gambar
        $check = getimagesize($_FILES["image_path"]["tmp_name"]);
        if ($check === false) {
        }

        // Cek ukuran file
        if ($_FILES["image_path"]["size"] > 500000) { // 500KB
        }

        // Cek format file
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        }
        else {
            // Jika semua cek lolos, coba upload file
            if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
                // Simpan data ke database menggunakan prepared statements
                $stmt = $conn->prepare("UPDATE products SET merk = ?, variety = ?, ssd_hdd = ?, processor = ?, ram = ?, vga = ?, screen_size = ?, storages = ?, price = ?, purpose = ?, feature = ?, image_path = ?, stock = ? WHERE id_product = ?");
                
                // Eksekusi statement dengan parameter
                if ($stmt->execute([$merk, $variety, $ssd_hdd, $processor, $ram, $vga, $screen_size, $storages, $price, $purpose, $feature, $target_file, $stock, $id_produk])) {
                    // Redirect ke halaman product.php setelah berhasil mengupdate produk
                    header("Location: product-admin.php");
                    exit(); // Pastikan untuk keluar setelah redirect
                } else {
                    // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
                    echo "Error updating record.";
                }
            } else {
                // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Jika tidak ada gambar baru, update data tanpa mengubah gambar
        $stmt = $conn->prepare("UPDATE products SET merk = ?, variety = ?, ssd_hdd = ?, processor = ?, ram = ?, vga = ?, screen_size = ?, storages = ?, price = ?, purpose = ?, feature = ?, stock = ? WHERE id_product = ?");
        
        // Eksekusi statement dengan parameter
        if ($stmt->execute([$merk, $variety, $ssd_hdd, $processor, $ram, $vga, $screen_size, $storages, $price, $purpose, $feature, $stock, $id_produk])) {
            // Redirect ke halaman product.php setelah berhasil mengupdate produk
            header("Location: product-admin.php");
            exit(); // Pastikan untuk keluar setelah redirect
        } else {
            // Anda bisa menambahkan penanganan kesalahan di sini jika perlu
            echo "Error updating record.";
        }
    }
} else {
    echo "Invalid request.";
}
?>