<?php
$conn = pg_connect("host=localhost dbname=Laptopku user=postgres password=12345678");

$id_produk = $_POST['id_produk'];
$merek = $_POST['merek'];
$tipe = $_POST['tipe'];
$ssd_hdd = $_POST['ssd_hdd'];
$processor = $_POST['processor'];
$ram = $_POST['ram'];
$vga = $_POST['vga'];
$screen_size = $_POST['screen_size'];
$storage = $_POST['storage'];
$harga = $_POST['harga'];
$tujuan = $_POST['tujuan'];
$fitur = $_POST['fitur'];
$stock = $_POST['stock'];

$query = "UPDATE products SET merek='$merek', tipe='$tipe', ssd_hdd='$ssd_hdd', processor='$processor', ram='$ram', vga='$vga', screen_size='$screen_size', storage='$storage', harga=$harga, tujuan='$tujuan', fitur='$fitur', stock=$stock WHERE id_produk=$id_produk";

$result = pg_query($conn, $query);

if ($result) {
    header("Location: product.php?message=Produk berhasil diperbarui");
} else {
    echo "Error: " . pg_last_error($conn);
}

pg_close($conn);
?>