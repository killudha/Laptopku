<?php
include 'C:\xampp\htdocs\Laptopku\Admin\config.php';

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id_user = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header('Location: user.php');
} else {
    echo "Error: Data gagal dihapus.";
}
?>
