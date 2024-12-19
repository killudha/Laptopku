<?php
session_start();
session_unset(); // Hapus semua data sesi
session_destroy(); // Hapus sesi
header('Location: home.php'); // Redirect ke halaman login
exit();
?>
