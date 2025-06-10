<?php
session_start();
require 'C:\xampp\htdocs\Laptopku\config.php'; // Memasukkan file konfigurasi

header('Content-Type: application/json'); // Penting untuk respons JSON

// 1. Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang.']);
    exit();
}
$id_user = $_SESSION['id_user'];

// 2. Ambil dan validasi data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id_product']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid. Data produk atau jumlah tidak ditemukan.']);
    exit();
}

$id_product = filter_var($data['id_product'], FILTER_VALIDATE_INT);
$quantity_to_add = filter_var($data['quantity'], FILTER_VALIDATE_INT); // Jumlah yang ingin ditambahkan dari request

if ($id_product === false || $id_product <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID Produk tidak valid.']);
    exit();
}

if ($quantity_to_add === false || $quantity_to_add <= 0) {
    echo json_encode(['success' => false, 'message' => 'Jumlah produk yang dimasukkan tidak valid (harus lebih dari 0).']);
    exit();
}

try {
    // 3. Mulai transaksi database
    $conn->beginTransaction();

    // 4. Cek stok produk dan dapatkan detail produk
    // Gunakan FOR UPDATE untuk locking jika Anda mengurangi stok di sini,
    // atau jika ada kemungkinan besar beberapa request bersamaan untuk produk yang sama.
    $stmt_product_stock = $conn->prepare("SELECT stock, merk, variety FROM products WHERE id_product = :id_product FOR UPDATE");
    $stmt_product_stock->bindParam(':id_product', $id_product, PDO::PARAM_INT);
    $stmt_product_stock->execute();
    $product_details = $stmt_product_stock->fetch(PDO::FETCH_ASSOC);

    if (!$product_details) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan di database.']);
        exit();
    }

    $current_stock = (int)$product_details['stock'];
    $product_name = htmlspecialchars($product_details['merk'] . ' ' . $product_details['variety']);

    // 5. Validasi stok
    if ($current_stock <= 0) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Maaf, stok untuk produk ' . $product_name . ' saat ini sudah habis.']);
        exit();
    }

    // 6. Cek apakah produk sudah ada di keranjang
    $query_check_cart = "SELECT id_cart, quantity FROM cart WHERE id_user = :id_user AND id_product = :id_product";
    $stmt_check_cart = $conn->prepare($query_check_cart);
    $stmt_check_cart->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt_check_cart->bindParam(':id_product', $id_product, PDO::PARAM_INT);
    $stmt_check_cart->execute();
    $cartItem = $stmt_check_cart->fetch(PDO::FETCH_ASSOC);

    $final_message = '';

    if ($cartItem) {
        // Jika produk sudah ada, update jumlahnya
        $new_total_quantity = $cartItem['quantity'] + $quantity_to_add;

        // Validasi stok terhadap total kuantitas baru di keranjang
        if ($current_stock < $new_total_quantity) {
            $conn->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Tidak bisa menambahkan. Stok produk ' . $product_name . ' tidak mencukupi (tersisa: ' . $current_stock . '). Anda mencoba memiliki total ' . $new_total_quantity . ' di keranjang.'
            ]);
            exit();
        }

        $query_update_cart = "UPDATE cart SET quantity = :new_quantity WHERE id_cart = :id_cart";
        $stmt_update_cart = $conn->prepare($query_update_cart);
        $stmt_update_cart->bindParam(':new_quantity', $new_total_quantity, PDO::PARAM_INT);
        $stmt_update_cart->bindParam(':id_cart', $cartItem['id_cart'], PDO::PARAM_INT);
        $stmt_update_cart->execute();
        $final_message = $product_name . ' ('.$quantity_to_add.') berhasil ditambahkan. Jumlah di keranjang kini ' . $new_total_quantity . '.';

    } else {
        // Jika produk belum ada, tambahkan ke keranjang
        // Validasi stok untuk penambahan baru
        if ($current_stock < $quantity_to_add) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Stok produk ' . $product_name . ' tidak mencukupi (tersisa: ' . $current_stock . '). Anda mencoba menambahkan ' . $quantity_to_add . '.']);
            exit();
        }

        $query_insert_cart = "INSERT INTO cart (id_user, id_product, quantity, added_at) VALUES (:id_user, :id_product, :quantity_to_add, NOW())";
        $stmt_insert_cart = $conn->prepare($query_insert_cart);
        $stmt_insert_cart->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt_insert_cart->bindParam(':id_product', $id_product, PDO::PARAM_INT);
        $stmt_insert_cart->bindParam(':quantity_to_add', $quantity_to_add, PDO::PARAM_INT);
        $stmt_insert_cart->execute();
        $final_message = $product_name . ' ('.$quantity_to_add.') berhasil ditambahkan ke keranjang.';
    }

    // 7. Pengurangan Stok Produk (OPSIONAL - PERTIMBANGKAN DENGAN HATI-HATI)
    // Umumnya, stok dikurangi SAAT CHECKOUT BERHASIL.
    // Jika Anda mengurangi stok di sini, pastikan ada mekanisme untuk mengembalikan stok jika
    // keranjang tidak jadi di-checkout atau item dihapus.
    /*
    $new_product_stock = $current_stock - $quantity_to_add;
    $stmt_update_product_stock = $conn->prepare("UPDATE products SET stock = :stock WHERE id_product = :id_product");
    $stmt_update_product_stock->bindParam(':stock', $new_product_stock, PDO::PARAM_INT);
    $stmt_update_product_stock->bindParam(':id_product', $id_product, PDO::PARAM_INT);
    $stmt_update_product_stock->execute();
    */

    // 8. Commit transaksi
    $conn->commit();

    echo json_encode(['success' => true, 'message' => $final_message]);

} catch (PDOException $e) {
    // 9. Rollback transaksi jika terjadi error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    // Log error untuk developer, jangan tampilkan $e->getMessage() langsung ke user di production
    error_log("Error add_to_cart.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.']);
}
?>