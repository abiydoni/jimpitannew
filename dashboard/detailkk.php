<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

// Check if user is admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to unauthorized page
    exit;
}
// Include the database connection
include 'api/db.php';

// Mengambil parameter nama dari URL
$nama_dicari = isset($_GET['nama']) ? $_GET['nama'] : '';

// ... existing code ...

if ($nama_dicari) {
    // Query untuk mencari data berdasarkan nama
    $query = "SELECT * FROM master_kk WHERE kk_name = :nama";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nama', $nama_dicari, PDO::PARAM_STR);
    $stmt->execute();
    
    // Cek apakah data ditemukan
    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    echo "Nama tidak valid.";
    exit;
}

// Menutup statement (tidak perlu menutup koneksi PDO secara manual)
$stmt = null;

// ... existing code ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>KK</title>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-square-rounded'></i>
            <span class="text">Jimpitan</span>
        </a>
        <ul class="side-menu top">
            <li><a href="index.php"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
            <li><a href="jadwal.php"><i class='bx bxs-group'></i><span class="text">Jadwal Jaga</span></a></li>
            <li class="active"><a href="#"><i class='bx bxs-group'></i><span class="text">KK</span></a></li>
            <li><a href="report.php"><i class='bx bxs-report'></i><span class="text">Report</span></a></li>
            <li><a href="keuangan.php"><i class='bx bxs-wallet'></i><span class="text">Keuangan</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="setting.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
            <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu' ></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" id="search-input" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
					<!-- <button type="button" class="clear-btn"><i class='bx bx-reset' ></i></button> -->
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Jimpitan - RT07 Salatiga</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">KK</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="index.php">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
                                <!-- Card Container -->
                    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
                        <!-- Profile Image -->
                        <div class="flex items-center justify-center">
                            <img src="https://via.placeholder.com/100" alt="Profile" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-md">
                        </div>
                        
                        <!-- Name and Position -->
                        <div class="text-center mt-4">
                            <h1 class="text-2xl font-bold text-gray-800">John Doe</h1>
                            <p class="text-blue-500 text-sm font-medium">Senior Developer</p>
                        </div>
                        
                        <!-- Divider -->
                        <hr class="my-4 border-gray-300">
                        
                        <!-- Contact Information -->
                        <div class="text-center text-gray-600">
                            <p><strong>Phone:</strong> (123) 456-7890</p>
                            <p><strong>Email:</strong> johndoe@example.com</p>
                            <p><strong>Address:</strong> 123 Business St, Cityville</p>
                        </div>
                        
                        <!-- Social Media Links -->
                        <div class="flex justify-center space-x-4 mt-4 text-gray-500">
                            <a href="#" class="hover:text-blue-500">LinkedIn</a>
                            <a href="#" class="hover:text-blue-500">Twitter</a>
                            <a href="#" class="hover:text-blue-500">Website</a>
                        </div>
                    </div>

            <ul class="box-info">
                <li>
                    <i class='bx bxs-group bx-lg' ></i>
                    <p>
                        <div class="mb-4">
                            <label class="block text-gray-700">Kode:</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($data['code_id']) ?>" class="w-full p-2 border border-gray-300 rounded" readonly>
                        </div>
                    </p>
                    <p>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nama:</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['kk_name']) ?>" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    </p>
                    <p>
                        <div class="mb-4">
                            <label class="block text-gray-700">Alamat:</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($data['kk_alamat']) ?>" class="w-full p-2 border border-gray-300 rounded" readonly>
                        </div>
                    </p>
                    <p>
                        <div class="mb-4">
                            <label class="block text-gray-700">No HP:</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($data['kk_hp']) ?>" class="w-full p-2 border border-gray-300 rounded" readonly>
                        </div>
                    </p>

                </li>
                <li>
                    <i class='bx bxs-info-circle bx-lg'></i>
                    <span class="text">
                        <h3 id="totalUncheck">0</h3>
                        <p>QR Code</p>
                    </span>
                </li>
            </ul>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script src="js/script.js"></script>
    <script src="js/print.js"></script>
	<script src="js/qrcode.min.js"></script>

    <script>
        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', function (e) {
            if(window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                if(searchForm.classList.contains('show')) {
                    searchButtonIcon.classList.replace('bx-search', 'bx-x');
                } else {
                    searchButtonIcon.classList.replace('bx-x', 'bx-search');
                }
            }
        })

        if(window.innerWidth < 768) {
            sidebar.classList.add('hide');
        } else if(window.innerWidth > 576) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
        }

        window.addEventListener('resize', function () {
            if(this.innerWidth > 576) {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
            }
        })
    </script>
</body>
</html>