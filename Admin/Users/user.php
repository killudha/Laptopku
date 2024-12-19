<?php
session_start(); // Memulai sesi
include 'C:\xampp\htdocs\Laptopku\Admin\config.php'; // Hubungkan ke database

// Default parameter pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian pengguna
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

try {
    // Hitung total pengguna
    $totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Query data pengguna dengan pagination dan pencarian
    $sql = "SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON u.role_id_user = r.role_id"; // Ganti r.role_name dengan r.name
    if (!empty($search)) {
        $sql .= " WHERE u.username LIKE :search OR u.email LIKE :search OR r.name LIKE :search";
    }
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($sql);
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total data untuk pagination
    $countSql = "SELECT COUNT(*) FROM users";
    if (!empty($search)) {
        $countSql .= " WHERE username LIKE :search OR email LIKE :search";
    }
    $countStmt = $conn->prepare($countSql);
    if (!empty($search)) {
        $countStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalData = $countStmt->fetchColumn();
    $totalPages = ceil($totalData / $limit);

} catch (PDOException $e) {
    $users = [];
    $totalData = $totalPages = 0;
    $errorMessage = $e->getMessage();
    echo "Error: " . $errorMessage; // Tampilkan pesan kesalahan
}

// Menangani pesan sukses
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
$showModal = !empty($successMessage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Laptopku</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background-color: #f8f9fa;
            padding: 15px; /* Sesuaikan padding */
            border-right: 1px solid #dee2e6; /* Jika ada border */
            overflow-y: auto; /* Tambahkan scroll jika konten melebihi tinggi */
        }

        .sidebar a {
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            color: #000;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .active {
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }

        .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0, 0, 0, 0.5); /* Gelap semi-transparan */
        backdrop-filter: blur(5px); /* Efek blur */
        }

        .modal-content {
        background-color: #ffffff;
        margin: 10% auto; 
        padding: 30px;
        border-radius: 10px; /* Membuat sudut melengkung */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Memberikan bayangan */
        width: 60%; 
        max-width: 500px; /* Batas maksimal lebar */
        text-align: center;
        animation: modalFadeIn 0.4s ease-out; /* Animasi fade-in */
        position: relative; /* Untuk posisi ikon tutup */
        }

        .close {
        color: #555;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s ease-in;
        }

        .close:hover {
        color: #ff0000; /* Warna merah saat hover */
        }

        .modal-icon {
        margin-bottom: 20px;
        font-size: 50px; /* Ikon besar */
        color: #007BFF; /* Warna biru */
        }

        .modal h2 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #333; /* Warna teks */
        }

        .modal p {
        font-size: 16px;
        color: #666; /* Warna teks sekunder */
        }


        .modal-button-green {
        background-color: #28a745; /* Warna hijau */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .modal-button-green:hover {
        background-color: #218838; /* Warna hijau lebih gelap saat hover */
        transform: scale(1.05); /* Efek membesar sedikit */
        }

        .modal-button-green:active {
        background-color: #1e7e34; /* Warna hijau lebih gelap saat tombol ditekan */
        transform: scale(0.95); /* Efek mengecil saat ditekan */
        }

        .table th, .table td {
        padding: 20px; /* Atur padding sesuai kebutuhan */
        }
    </style>
    <script>
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        // Tampilkan modal jika ada pesan sukses
        window.onload = function() {
            <?php if ($showModal): ?>
                document.getElementById('successModal').style.display = 'block';
            <?php endif; ?>
        };
    </script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h4 class="p-3">Laptopku</h4>
                <a href="http://localhost/Laptopku/Admin/Dashboard/dashboard.php" class="active">Dashboard</a>
                <a href="http://localhost/Laptopku/PBL/product-admin.php">Products</a>
                <a href="http://localhost/Laptopku/Admin\Order\order.php">Orders</a>
                <a href="http://localhost/Laptopku/Admin\Users\user.php">Users</a>
                <a href="http://localhost/Laptopku/Admin/StatusOrder/status_order.php">Order Status</a>
                <a href="http://localhost/Laptopku/Admin/InOutOrder/product_flow.php">Product In/Out</a>
                <a href="http://localhost/Laptopku/Admin/logout.php" class="text-danger">Logout</a>
            </div>

                <!-- Content -->
                <div class="col-md-9 col-lg-10 content"> <!-- Tambahkan margin-left untuk menghindari konten tertutup sidebar -->
                <h3>Pengguna</h3>

                <!-- Cards Section -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Pengguna</h5>
                                <p class="card-text">
                                    <?= $totalUsers ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" class="d-flex w-50">
                        <input type="text" id="searchUser " name="search" class="form-control me-2" placeholder="Cari pengguna... (nama, username, email, role)"
                               value="<?= htmlspecialchars($search) ?>">
                        <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="user.php" class="btn btn-danger">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover mt-3 mb-5">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Kode Role</th>
                                <th>Role</th>
                                <th>log Edit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php if (!empty($users)): ?>
                                <?php
                                $no = $offset + 1;
                                foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['password']) ?></td>
                                        <td><?= htmlspecialchars($user['role_id_user']) ?></td>
                                        <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['updated_at']); ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="http://localhost/Laptopku/Admin/Users/edit.php?id=<?= $user['id_user'] ?>" class="btn btn-primary btn-sm mb-2">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="http://localhost/Laptopku/Admin/Users/delete.php?id=<?= $user['id_user'] ?>" class="btn btn-danger btn-sm mb-2">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                                <a href="http://localhost/Laptopku/Admin/Users/jadiadminn.php?id=<?= $user['id_user'] ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-user-shield"></i> Jadikan Admin
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data pengguna ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <!-- Modal -->
            <div id="successModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <div class="modal-icon">
                        <i class="fas fa-check-circle" style="color: green; font-size: 50px;"></i>
                    </div>
                    <h2>Berhasil jadikan Admin!</h2>
                    <p><?= htmlspecialchars($successMessage) ?></p>
                    <button class="modal-button-green" onclick="closeModal()">OK</button>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center mt-5">
                        <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>