<?php
$host = "localhost";
$dbname = "Laptopku";
$user = "postgres";
$password = "12345678"; // Ganti dengan password PostgreSQL Anda

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>