<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php'); // Redirect ke login jika belum login
    exit();
}

require 'C:\xampp\htdocs\Laptopku\config.php'; // Memasukkan file konfigurasi

try {
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT username, email, telepon, alamat, image_path FROM users WHERE id_user = :id_user";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah data pengguna ditemukan
    if (!$user) {
        die("User  not found.");
        exit();
    }
    // Gambar default jika tidak ada gambar yang diunggah
    $default_image = "img/pp.png"; // Ganti dengan path gambar default Anda
    $image_path = !empty($user['image_path']) ? $user['image_path'] : $default_image;
 
    if (!$user) {
        echo "User  tidak ditemukan.";
        exit();
    }
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit();
}

// Inisialisasi parameter pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Set limit dan halaman
$limit = 12; // Ubah menjadi 12 produk per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Ambil nomor halaman dari URL
$offset = ($page - 1) * $limit; // Hitung offset untuk query SQL

// Fetch products from the database
$query = "SELECT * FROM products";
$stmt = $conn->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk menghitung total produk sesuai pencarian
$totalQuery = "SELECT COUNT(*) FROM Products WHERE merk ILIKE :search OR variety ILIKE :search";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$totalStmt->execute();
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit); // Hitung total halaman

// Query untuk mengambil data produk dengan pagination dan pencarian
$query = "SELECT id_product, merk, variety, price, image_path 
          FROM Products 
          WHERE merk ILIKE :search OR variety ILIKE :search 
          ORDER BY id_product ASC 
          LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);   
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

try {
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Russo+One&display=swap" rel="stylesheet">
    <script type="text/javascript" src="app.js" defer></script>
    <title>Home - Laptopku.</title>

    <style>
      .pagination {
      display: flex;
      justify-content: center;
      margin: 20px 0;
      gap: 5px; /* Jarak antar item */
    }

    .page-item {
      display: inline-block;
      padding: 10px 15px;
      margin: 0;
      background-color: #f8f9fa; /* Warna latar abu-abu terang */
      color: #007bff; /* Warna teks */
      text-decoration: none;
      border-radius: 4px; /* Sedikit sudut membulat */
      border: 1px solid #dee2e6; /* Garis abu-abu */
      transition: background-color 0.3s, color 0.3s;
    }

    .page-item:hover {
      background-color: #007bff; /* Warna biru saat hover */
      color: white; /* Warna teks putih saat hover */
    }

    .page-item.active {
      background-color: #007bff; /* Warna biru untuk item aktif */
      color: white; /* Warna teks putih untuk item aktif */
      pointer-events: none; /* Nonaktifkan klik */
    }

    .page-item.disabled {
      background-color: #e9ecef; /* Warna abu-abu terang untuk item nonaktif */
      color: #adb5bd; /* Warna teks abu-abu */
      pointer-events: none; /* Nonaktifkan klik */
    }

    /* Styling untuk form container */
    form {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom : 30px;
    }

    /* Styling untuk input group */
    .input-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Styling untuk input text */
    .input-group input[type="text"] {
      width: 300px;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
      outline: none;
      transition: border-color 0.3s ease-in-out;
      margin-top : 15px;
    }

    .input-group input[type="text"]:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    /* Styling untuk tombol */
    .input-group .btn {
      padding: 10px 20px;
      font-size: 16px;
      font-weight: bold;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }

    .input-group .btn-primary {
      background-color: #007bff;
    }

    .input-group .btn-primary:hover {
      background-color: #0056b3;
    }

    .input-group .btn-danger {
      background-color: #dc3545;
    }

    .input-group .btn-danger:hover {
      background-color: #a71d2a;
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

    .modal h2 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #333; /* Warna teks */
        }

        .modal p {
        font-size: 16px;
        color: #666; /* Warna teks sekunder */
        }

    .modal-icon {
        margin-bottom: 20px;
        font-size: 50px; /* Ikon besar */
        color: #007BFF; /* Warna biru */
        }

    .btn-ok {
        background-color: red; /* Warna hijau */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top : 10px;
    }

    .btn-ok:hover {
        background-color: darkred; /* Darker green */
    }

    .btn-ok:active {
        background-color: darkred; /* Even darker green when clicked */
    }
    </style>
    <script>
        function closeModal() {
            document.getElementById("noProductsModal").style.display = "none";
        }

        // Tampilkan modal jika tidak ada produk
        window.onload = function() {
            <?php if (count($products) === 0): ?>
                document.getElementById('noProductsModal').style.display = 'block';
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="logo">LAPTOPKU.</div>
        <div class="nav-links">
            <a href="home-login.php">Home</a>
            <a href="product-login.php">Shopping</a>
            <a href="cart.php" class="cartimg"><img src="img/chart-icon.png" alt="profile user"></a>
            <a href="profile.php"><img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" style="width:25px; height:25px; border-radius:50%"></a>
            <a href="logout.php"><i class="fa fa-sign-out"></i></a>
        </div>
    </nav>


  <div class="container">
    <h2 class="title">Our<span class="blue-text-title"> Product</span></h2>

    <form method="GET">
      <div class="input-group">
      <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" >
          <button class="btn btn-primary" type="submit">Search</button>
          <a href="product-login.php" class="btn btn-danger">Reset</a>
      </div>
    </form>

    <div class="produklaptop">
        <nav id="sidebar">
            <ul>
                <li class="active">
                      <h2>Kategory</h2>
                  </li>
                <li class="active">
                    <h3 style="color: #45A33A;">Simple Filter</h3>
                </li>
              <li>
                <button onclick="toggleSubMenu(this)" class="dropdown-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                      <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h207q16 0 30.5 6t25.5 17l57 57h320q33 0 56.5 23.5T880-640v400q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H447l-80-80H160v480Zm0 0v-480 480Zm400-160v40q0 17 11.5 28.5T600-320q17 0 28.5-11.5T640-360v-40h40q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480h-40v-40q0-17-11.5-28.5T600-560q-17 0-28.5 11.5T560-520v40h-40q-17 0-28.5 11.5T480-440q0 17 11.5 28.5T520-400h40Z" />
                    </svg>
                    <span>Kegunaan</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                      <path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
                    </svg>
                  </button>
                  <ul class="sub-menu">
                    <div>
                      <li>
                        <label>
                          <input type="checkbox" name="kegunaan" value="kuliah">
                          <span>Sekolah</span>
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="kegunaan" value="gaming">
                          <span>Gaming</span>
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="kegunaan" value="design">
                          <span>Design</span>
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="kegunaan" value="kuliah">
                          <span>Bisnis</span>
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="kegunaan" value="kuliah">
                          <span>Multimedia</span>
                        </label>
                      </li>
                    </div>
                  </ul>
                  

              <li>
                <button onclick=toggleSubMenu(this) class="dropdown-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="24" height="24" viewBox="0 0 31.371 31.371">
                        <path d="M24.26 20.34c0 3.42-2.423 6.342-6.845 7.111v3.92h-3.768v-3.648c-2.578-.117-5.076-.811-6.537-1.654l1.154-4.5c1.615.886 3.883 1.693 6.383 1.693 2.191 0 3.691-.848 3.691-2.385 0-1.461-1.23-2.389-4.077-3.348-4.112-1.385-6.921-3.306-6.921-7.033 0-3.386 2.385-6.035 6.499-6.845V0h3.767v3.383c2.576.115 4.309.652 5.576 1.268l-1.115 4.348c-1.649-.424-3.419-1.311-6.188-1.311-2.5 0-3.307 1.076-3.307 2.154 0 1.268 1.346 2.074 4.613 3.307 5.279 1.663 7.123 3.778 7.123 7.241z"/>
                      </svg>                      
                    <span>Harga</span>
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                </button>
                <ul class="sub-menu">
                    <div>
                        <li>
                          <label>
                            <input type="checkbox" name="kegunaan" value="kuliah">
                            <span>0 - 5 Juta</span>
                          </label>
                        </li>
                        <li>
                          <label>
                            <input type="checkbox" name="kegunaan" value="gaming">
                            <span>5 - 10 Juta</span>
                          </label>
                        </li>
                        <li>
                          <label>
                            <input type="checkbox" name="kegunaan" value="design">
                            <span>10 - 15 Juta</span>
                          </label>
                        </li>
                      </div>
                </ul>
              </li>

        <li class="active">
        <h3 style="color: #A33A3A;">Advanced Filter</h3>
        </li>
            <li>
                <button onclick="toggleSubMenu(this)" class="dropdown-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 6.627 5.373 12 12 12s12-5.373 12-12S18.627 0 12 0zm0 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-15h2v6h-2zm0 8h2v2h-2z" fill="#000"/>
                    </svg>
                    <span>Merek</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                      <path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
                    </svg>
                  </button>
                  <ul class="sub-menu">
                    <div class="sub-container">
                        <div class="sub1">
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="kuliah">
                                    <span>Asus</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="gaming">
                                    <span>Lenovo</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="design">
                                    <span>HP</span>
                                </label>
                            </li>
                        </div>
                
                        <div class="sub2">
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="design">
                                    <span>Dell</span>
                                </label>
                            </li>
                
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="design">
                                    <span>Acer</span>
                                </label>
                            </li>
                
                            <li>
                                <label>
                                    <input type="checkbox" name="kegunaan" value="design">
                                    <span>Axioo</span>
                                </label>
                            </li>
                        </div>
                    </div>
                </ul>
                <li>
                  <button onclick="toggleSubMenu(this)" class="dropdown-btn">
                  <svg width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="" class="bi bi-hdd-rack" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.5 5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM3 4.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm2 7a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm-2.5.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"></path> <path d="M2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h1v2H2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2h-1V7h1a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm13 2v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm0 7v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm-3-4v2H4V7h8z"></path> </g></svg>
                      <span>SSH/HDD</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                  </button>
                  <ul class="sub-menu">
                      <div>
                          <li>
                            <label>
                              <input type="checkbox" name="kegunaan" value="kuliah">
                              <span>SSH</span>
                            </label>
                          </li>
                          <li>
                            <label>
                              <input type="checkbox" name="kegunaan" value="gaming">
                              <span>HDD</span>
                            </label>
                          </li>
                        </div>
                      </ul>

                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56216 2.87174C6.14861 2.41455 6.82355 2.25 7.5 2.25H16.5C17.1765 2.25 17.8514 2.41455 18.4378 2.87174C19.0172 3.32344 19.4352 4.0024 19.7154 4.89243L19.7225 4.91513L22.2604 15.2159C22.5738 15.8383 22.75 16.5463 22.75 17.2941C22.75 19.7141 20.887 21.75 18.5 21.75H5.5C3.11298 21.75 1.25 19.7141 1.25 17.2941C1.25 16.5463 1.42621 15.8383 1.73961 15.2159L4.27747 4.91513L4.28461 4.89243C4.56481 4.0024 4.98276 3.32344 5.56216 2.87174ZM3.77626 13.2197C4.30106 12.9752 4.88373 12.8382 5.5 12.8382H18.5C19.1163 12.8382 19.6989 12.9752 20.2237 13.2197L18.2777 5.32094C18.0589 4.63669 17.7822 4.26258 17.5156 4.05473C17.2532 3.85015 16.9281 3.75 16.5 3.75H7.5C7.07188 3.75 6.74682 3.85015 6.48441 4.05473C6.21779 4.26258 5.94107 4.63669 5.72234 5.32094L3.77626 13.2197ZM5.5 14.3382C4.49271 14.3382 3.59139 14.9242 3.10912 15.8329C2.88147 16.2618 2.75 16.7597 2.75 17.2941C2.75 18.9676 4.02103 20.25 5.5 20.25H18.5C19.979 20.25 21.25 18.9676 21.25 17.2941C21.25 16.7597 21.1185 16.2618 20.8909 15.8329C20.4086 14.9242 19.5073 14.3382 18.5 14.3382H5.5ZM10.5 16.25C10.9142 16.25 11.25 16.5858 11.25 17V18C11.25 18.4142 10.9142 18.75 10.5 18.75C10.0858 18.75 9.75 18.4142 9.75 18V17C9.75 16.5858 10.0858 16.25 10.5 16.25ZM13 16.25C13.4142 16.25 13.75 16.5858 13.75 17V18C13.75 18.4142 13.4142 18.75 13 18.75C12.5858 18.75 12.25 18.4142 12.25 18V17C12.25 16.5858 12.5858 16.25 13 16.25ZM15.5 16.25C15.9142 16.25 16.25 16.5858 16.25 17V18C16.25 18.4142 15.9142 18.75 15.5 18.75C15.0858 18.75 14.75 18.4142 14.75 18V17C14.75 16.5858 15.0858 16.25 15.5 16.25ZM18 16.25C18.4142 16.25 18.75 16.5858 18.75 17V18C18.75 18.4142 18.4142 18.75 18 18.75C17.5858 18.75 17.25 18.4142 17.25 18V17C17.25 16.5858 17.5858 16.25 18 16.25Z" fill="#000"></path> </g></svg>
                        <span>Storage</span>
                      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                        <div>
                            <li>
                              <label>
                                <input type="checkbox" name="kegunaan" value="kuliah">
                                <span>256 GB</span>
                              </label>
                            </li>
                            <li>
                              <label>
                                <input type="checkbox" name="kegunaan" value="gaming">
                                <span>512 GB</span>
                              </label>
                            </li>
                            <li>
                              <label>
                                <input type="checkbox" name="kegunaan" value="design">
                                <span>1 T</span>
                              </label>
                            </li>
                          </div>
                        </ul>

                        <li>
                            <button onclick=toggleSubMenu(this) class="dropdown-btn">
                                <svg fill="" width="24px" height="24px" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M223.977 63.69a5.967 5.967 0 0 0-2.295-.446l.001-.001-188.877.77a6.013 6.013 0 0 0-5.984 6.024l.226 98.86c.008 3.314 2.699 6.015 6.01 6.034l15.326.087v10.633c0 3.315 2.683 6.007 5.993 6.007h25.471s6.319-15.38 23.62-15.38c17.301 0 23.783 15.38 23.783 15.38h74.032a6.003 6.003 0 0 0 5.999-6.008v-8.16h14.388a6.004 6.004 0 0 0 6.01-6.003V69.222a5.976 5.976 0 0 0-1.755-4.237 5.978 5.978 0 0 0-1.948-1.294zM42.588 80.586A2.002 2.002 0 0 1 44.002 80v.001h44A1.995 1.995 0 0 1 90 82.002l-.13 69.985s-4.127.137-7.667 2.48S77.76 160 77.76 160H42V81.997c.001-.53.213-1.038.588-1.412zm62.001-.001a2.001 2.001 0 0 1 1.413-.584h44a1.999 1.999 0 0 1 1.847 1.232c.1.243.151.503.151.765l-.141 76.006a2.01 2.01 0 0 1-2.013 1.997h-20.189s-4.827-4.457-10.622-6.472c-5.795-2.014-15.035-2.447-15.035-2.447V81.994c.002-.53.214-1.037.589-1.41zm61.999 0a2.002 2.002 0 0 1 1.414-.584h44a1.999 1.999 0 0 1 1.847 1.232c.1.243.151.503.151.765l-.141 76.006A2.004 2.004 0 0 1 211.86 160H166V81.997c.001-.53.213-1.038.588-1.412z"></path> </g></svg>                 
                                <span>RAM</span>
                              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                            </button>
                            <ul class="sub-menu">
                                <div>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="kuliah">
                                        <span>4 GB</span>
                                      </label>
                                    </li>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="gaming">
                                        <span>8 GB</span>
                                      </label>
                                    </li>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="design">
                                        <span>16 GB</span>
                                      </label>
                                    </li>
                                  </div>
                                </ul>        
            <li>
                <button onclick=toggleSubMenu(this) class="dropdown-btn">
                    <svg width="24px" height="24px" viewBox="0 0 24 24" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:none;}.cls-2{clip-path:url(#clip-path);}</style> <clipPath id="clip-path"> <rect class="cls-1" width="24" height="24"></rect> </clipPath> </defs> <title>processor</title> <g class="cls-2"> <path d="M16.13,19.88H7.88a3.76,3.76,0,0,1-3.75-3.75V7.88A3.75,3.75,0,0,1,7.88,4.13h8.25a3.76,3.76,0,0,1,3.75,3.75v8.25A3.76,3.76,0,0,1,16.13,19.88ZM7.88,5.63A2.24,2.24,0,0,0,5.63,7.88v8.25a2.25,2.25,0,0,0,2.25,2.25h8.25a2.25,2.25,0,0,0,2.25-2.25V7.88a2.25,2.25,0,0,0-2.25-2.25Z"></path> <path d="M9.69,5.57a.74.74,0,0,1-.75-.75V2a.75.75,0,0,1,1.5,0V4.82A.75.75,0,0,1,9.69,5.57Z"></path> <path d="M14.31,5.6a.76.76,0,0,1-.75-.75V2a.75.75,0,0,1,1.5,0V4.85A.75.75,0,0,1,14.31,5.6Z"></path> <path d="M9.69,22.75A.75.75,0,0,1,8.94,22V19.13a.75.75,0,0,1,1.5,0V22A.76.76,0,0,1,9.69,22.75Z"></path> <path d="M14.31,22.78a.76.76,0,0,1-.75-.75v-2.9a.75.75,0,0,1,1.5,0V22A.75.75,0,0,1,14.31,22.78Z"></path> <path d="M22,10.48H19.15a.75.75,0,0,1,0-1.5H22a.75.75,0,0,1,0,1.5Z"></path> <path d="M22,15.1h-2.9a.75.75,0,0,1,0-1.5H22a.75.75,0,0,1,0,1.5Z"></path> <path d="M4.9,10.48H2A.75.75,0,0,1,2,9H4.9a.75.75,0,0,1,0,1.5Z"></path> <path d="M4.9,15.1H2a.75.75,0,0,1,0-1.5H4.9a.75.75,0,0,1,0,1.5Z"></path> </g> </g></svg>
                                <span>Processor</span>
                              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                            </button>
                            <ul class="sub-menu">
                              <div class="sub-container">
                                  <div class="sub1">
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="kuliah">
                                              <span>i3</span>
                                          </label>
                                      </li>
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="gaming">
                                              <span>i5</span>
                                          </label>
                                      </li>
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="design">
                                              <span>i7</span>
                                          </label>
                                      </li>
                                  </div>
                          
                                  <div class="sub2">
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="design">
                                              <span>AMD Ryzen 5</span>
                                          </label>
                                      </li>
                          
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="design">
                                              <span>AMD Ryzen 7</span>
                                          </label>
                                      </li>
                          
                                      <li>
                                          <label>
                                              <input type="checkbox" name="kegunaan" value="design">
                                              <span>i9</span>
                                          </label>
                                      </li>
                                  </div>
                              </div>
                          </ul>
           
                <button onclick=toggleSubMenu(this) class="dropdown-btn">
                  <svg fill="" width="24px" height="24px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M30 2.994h-28c-1.099 0-2 0.9-2 2v17.006c0 1.099 0.9 1.999 2 1.999h13v3.006h-5c-0.552 0-1 0.448-1 1s0.448 1 1 1h12c0.552 0 1-0.448 1-1s-0.448-1-1-1h-5v-3.006h13c1.099 0 2-0.9 2-1.999v-17.006c0-1.1-0.901-2-2-2zM30 22h-28v-17.006h28v17.006z"></path> </g></svg>
                                <span>Screen Size</span>
                              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                            </button>
                            <ul class="sub-menu">
                                <div>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="kuliah">
                                        <span>14 Inch</span>
                                      </label>
                                    </li>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="gaming">
                                        <span>15.6 Inch</span>
                                      </label>
                                    </li>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="design">
                                        <span>13.3 Inch</span>
                                      </label>
                                    </li>
                                    <li>
                                      <label>
                                        <input type="checkbox" name="kegunaan" value="design">
                                        <span>17.3 Inch</span>
                                      </label>
                                    </li>
                                  </div>
                                </ul>   
                              </nav>
        
                    <div class="container-produk2">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card2">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['merk']); ?>" class="product-image2">
                        <h2 class="product-title2"><?php echo htmlspecialchars($product['merk']); ?> - <?php echo htmlspecialchars($product['variety']); ?></h2>
                        <p class="product-price2">Rp. <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                        <div class="button-group2">
                            <button class="details-btn2" onclick="window.location.href='detail-login.php?id=<?php echo $product['id_product']; ?>';">Get Details</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                </div>
                </div>

    <!-- Modal -->
    <div id="noProductsModal" class="modal" style="display: none;">
        <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-times" style="color: red; font-size: 50px;"></i>
        </div>
            <h2>Laptop Not Found</h2>
            <p>Sorry, we couldn't find any laptops matching your search criteria.</p>
            <button class="btn btn-ok" onclick="window.location.href='product-login.php'">OK</button>
        </div>
    </div>

    <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
          <div class="pagination">
              <a class="page-item <?= ($page == 1) ? 'disabled' : '' ?>" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <a class="page-item <?= ($i == $page) ? 'active' : '' ?>" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
              <?php endfor; ?>
              <a class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
          </div>
      <?php endif; ?>
     

    <!--Footer-->
    <section class="footer">
        <div class="brand">LAPTOPKU.</div>
        <div class="tagline">Toko Online Laptop</div>
        <div class="social-icons">
            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="#"><i class="fa-regular fa-envelope"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-discord"></i></a>
        </div>
        <hr class="garis">
        <div class="copyright">
            Copyright © 2024 Laptopku
        </div>
    </section>
</body>
</html>