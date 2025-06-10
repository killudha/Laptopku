<?php
session_start();
require 'C:\xampp\htdocs\Laptopku\config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment-submit'])) {
    try {
        // Ambil data pengguna
        $sql = "SELECT username FROM users WHERE id_user = :id_user";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User not found.");
        }

        // Ambil data pembayaran dari formulir
        $name = htmlspecialchars($_POST['name']);
        $card_number = htmlspecialchars($_POST['card_number']);
        $month = htmlspecialchars($_POST['month']);
        $year = htmlspecialchars($_POST['year']);
        $cvv = htmlspecialchars($_POST['cvv']);
        $id_cart = $_POST['id_cart'];
        $id_product = $_POST['id_product'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $subtotal = $_POST['subtotal'];

        $shippingFee = 15000;
        $totalOrder = array_sum($subtotal) + $shippingFee;

        // Validasi sederhana untuk nomor kartu
        if (!preg_match('/^[0-9]{16}$/', $card_number)) {
            throw new Exception("Invalid card number.");
        }
        if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
            throw new Exception("Invalid CVV.");
        }

        // Proses transaksi
        $conn->beginTransaction();

        foreach ($id_product as $key => $productId) {
            $stmt = $conn->prepare("
                INSERT INTO Orders (id_user, id_product, recipent_name, product_price, total_price, shipping_type, payment_status) 
                VALUES (:id_user, :id_product, :recipent_name, :product_price, :total_price, :shipping_type, 'paid')
            ");
            $stmt->execute([
                ':id_user' => $id_user,
                ':id_product' => $productId,
                ':recipent_name' => $user['username'],
                ':product_price' => $price[$key],
                ':total_price' => $subtotal[$key] + $shippingFee,
                ':shipping_type' => 'JNE/J&T'
            ]);

            // Ambil ID order yang baru dimasukkan
            $id_order = $conn->lastInsertId();

            // Insert ke tabel Status_Orders
            $stmt = $conn->prepare("
                INSERT INTO Status_Orders (id_order, status_delivery, order_date) 
                VALUES (:id_order, 'packaged', CURRENT_DATE)
            ");
            $stmt->execute([':id_order' => $id_order]);
        }

        // Hapus item dari tabel Cart untuk pengguna
        $stmt = $conn->prepare("DELETE FROM Cart WHERE id_user = :id_user");
        $stmt->execute([':id_user' => $id_user]);

        $conn->commit();

        // Redirect dengan status sukses
        header('Location: payment.php?status=success');
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Payment Error: " . $e->getMessage());
        header('Location: payment.php?status=failed');
        exit();
    }
} else {
    // Jika akses langsung tanpa formulir
    header('Location: payment.php');
    exit();
}
?>
