<?php
$host = "127.0.0.1"; // Gunakan IP, bukan "localhost"
$dbname = "Laptopku";
$user = "postgres";
$password = "12345";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi sukses!";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>