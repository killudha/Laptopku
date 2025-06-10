<?php
session_start();
include 'C:\xampp\htdocs\Laptopku\config.php'; // Sesuaikan path

// Misal, user ID disimpan di session:
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php?error=notloggedin"); // Ganti dengan halaman login Anda
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_pesanan_diterima'])) {
    if (isset($_POST['id_order']) && isset($_POST['id_status'])) {
        $id_order = (int)$_POST['id_order'];
        $id_status = (int)$_POST['id_status'];
        $current_datetime = date("Y-m-d H:i:s"); // Gunakan DATETIME jika kolom diubah
        // $current_date = date("Y-m-d"); // Gunakan DATE jika kolom tetap DATE

        $event_date_for_query = date("Y-m-d"); // default to DATE
        if (/* kolom Anda DATETIME */ false) { // Ganti 'false' dengan true jika kolom DATETIME
            $event_date_for_query = $current_datetime;
        }


        try {
            // Optional: Verifikasi apakah pesanan ini milik user yang login
            // $stmt_check_owner = $conn->prepare("SELECT o.id_user FROM Orders o
            //                                     JOIN Status_Orders so ON o.id_order = so.id_order
            //                                     WHERE so.id_status = :id_status AND o.id_user = :user_id AND o.id_order = :id_order");
            // $stmt_check_owner->execute([
            //     ':id_status' => $id_status,
            //     ':user_id' => $_SESSION['user_id'],
            //     ':id_order' => $id_order
            // ]);
            // if ($stmt_check_owner->rowCount() == 0) {
            //     header("Location: detail_tracker.php?order_id=$id_order&error=unauthorized");
            //     exit();
            // }

            // Update status di Status_Orders
            // Hanya update jika status saat ini 'shipped'
            $updateStmt = $conn->prepare("UPDATE Status_Orders
                                          SET status_delivery = 'completed', arrived_date = :arrived_date
                                          WHERE id_status = :id_status AND id_order = :id_order AND status_delivery = 'shipped'");
            $updateStmt->execute([
                ':arrived_date' => $event_date_for_query,
                ':id_status' => $id_status,
                ':id_order' => $id_order
            ]);

            if ($updateStmt->rowCount() > 0) {
                header("Location: detail_tracker.php?order_id=$id_order&status=received_confirmed");
                exit();
            } else {
                // Gagal update, mungkin status sudah 'completed' atau tidak 'shipped'
                header("Location: detail_tracker.php?order_id=$id_order&error=confirmation_failed");
                exit();
            }

        } catch (PDOException $e) {
            error_log("Error confirming receipt by user: " . $e->getMessage());
            header("Location: detail_tracker.php?order_id=$id_order&error=db_error");
            exit();
        }
    } else {
        // Data POST tidak lengkap
        header("Location: home-login.html?error=missing_data_confirmation"); // Ganti dengan halaman yang sesuai
        exit();
    }
} else {
    // Akses langsung ke file ini tanpa POST tidak diizinkan
    header("Location: home-login.html"); // Ganti dengan halaman yang sesuai
    exit();
}
?>