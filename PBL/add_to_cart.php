<?php
session_start();
require 'C:\xampp\htdocs\Laptopku\config.php'; // Memasukkan file konfigurasi

// Ambil data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);
$id_product = $data['id_product'];
$quantity = $data['quantity'];
$id_user = $_SESSION['id_user'];

try {
    // Cek apakah produk sudah ada di keranjang
    $query = "SELECT id_cart FROM cart WHERE id_user = :id_user AND id_product = :id_product";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_user', $id_user);
    $stmt->bindParam(':id_product', $id_product);
    $stmt->execute();
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // Jika produk sudah ada, update jumlahnya
        $query = "UPDATE cart SET quantity = quantity + :quantity WHERE id_cart = :id_cart";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id_cart', $cartItem['id_cart']);
        $stmt->execute();
    } else {
        // Jika produk belum ada, tambahkan ke keranjang
        $query = "INSERT INTO cart (id_user, id_product, quantity) VALUES (:id_user, :id_product, :quantity)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>