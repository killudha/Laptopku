<?php
session_start();
require 'C:\xampp\htdocs\Laptopku\config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_cart = $data['id_cart'];

    try {
        $sql = "DELETE FROM cart WHERE id_cart = :id_cart";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>