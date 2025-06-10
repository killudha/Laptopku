<?php
session_start();

// Hentikan semua sesi aktif
session_unset();
session_destroy();

// Arahkan ke halaman login
header("Location: /Laptopku/PBL-Login/login.php");
exit();
?>