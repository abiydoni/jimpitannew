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
    <script src="js/qrcode.min.js"></script>

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
            
            <div class="flex flex-wrap justify-center gap-8 p-4">
            <!-- Card Container -->
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
                <!-- Profile Image -->
                <div class="flex items-center justify-center">
                    <img src="<?= htmlspecialchars($data['kk_foto']) ?>" alt="Profile" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-md">
                </div>
                
                <!-- Name and Position -->
                <div class="text-center mt-4">
                    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($data['kk_name']) ?></h1>
                    <p class="text-blue-500 text-sm font-medium"><?= htmlspecialchars($data['code_id']) ?></p>
                </div>
                
                <!-- Divider -->
                <hr class="my-4 border-gray-300">
                
                <!-- Contact Information -->
                <div class="text-center text-gray-600">
                    <p><strong>Alamat : </strong><?= htmlspecialchars($data['kk_alamat']) ?></p>
                    <p><strong>No HP : </strong><?= htmlspecialchars($data['kk_hp']) ?></p>
                </div>
                <hr class="my-4 border-gray-300">

            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full flex flex-col items-center space-y-2">

                <div id="qrcode-container" class="space-y-4 flex items-center justify-center"></div>
                <hr class="my-4 border-gray-300">

                <hr class="my-4 border-gray-300">
                <!-- Tombol Cetak -->
                <button onclick="printPage()" 
                    class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out w-full max-w-[200px]">
                    <!-- Ikon Cetak -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18h12v-4H6v4zM6 22h12M8 18v4m8-4v4" />
                    </svg>
                    Cetak
                </button>
                <hr class="my-4 border-gray-300">
                <a href="javascript:history.back()" 
                    class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out">
                    <!-- Icon Panah -->
                    <svg xmlns="kk.php" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-2 w-full max-w-[200px]">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>

            </div>
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
    <script>
    function generateQRCodes() {
        // Ambil code_id langsung dari data PHP
        const codeId = "<?= htmlspecialchars($data['code_id']) ?>";
        
        // Kosongkan konten QR code sebelumnya
        const qrContainer = document.getElementById("qrcode-container");
        qrContainer.innerHTML = "";

        // Buat elemen div untuk QR code
        const qrDiv = document.createElement("div");
        qrContainer.appendChild(qrDiv);

        // Generate QR code
        new QRCode(qrDiv, {
            text: codeId,
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // Panggil generateQRCodes() saat halaman selesai dimuat
    window.onload = generateQRCodes;

    </script>


    <script>
        function printPage() {
            const printContent = document.querySelector('.flex.flex-wrap.justify-center.gap-8.p-4'); // Ambil konten yang ingin dicetak
            const originalContent = document.body.innerHTML; // Simpan konten asli
            
            // Tambahkan gaya untuk tampilan cetak
            const printStyles = `
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                    }
                    .flex {
                        display: flex;
                        flex-wrap: wrap; /* Pastikan konten bisa berjajar */
                        justify-content: center; /* Pusatkan konten */
                    }
                    .gap-8 {
                        gap: 2rem; /* Atur jarak antar elemen */
                    }
                    .max-w-xs { /* Ubah ukuran maksimum container */
                        max-width: 200px; /* Sesuaikan ukuran sesuai kebutuhan */
                    }
                </style>            
            `;
            
            document.body.innerHTML = printStyles + printContent.innerHTML; // Ganti konten dengan yang ingin dicetak
            //window.print(); // Panggil fungsi cetak
            document.body.innerHTML = originalContent; // Kembalikan konten asli
        }
    </script>    
</body>
</html>