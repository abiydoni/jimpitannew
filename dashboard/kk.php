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
// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $code_id = $_GET['delete'];
    $sql = "DELETE FROM master_kk WHERE code_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code_id]);

    header("Location: kk.php");
    exit();
}

// Prepare and execute the SQL statement
$sql = "SELECT * FROM master_kk";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>KK</h3>
                        <div class="mb-4 text-center">
                            <button type="button" id="addDataBtn" class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200" data-modal-toggle="addModal" onclick="toggleModal('addModal')">
                                <i class='bx bx-plus'></i> <!-- Ikon untuk tambah data -->
                            </button>
                            <button type="button" id="printSelectedBtn" class="btn-download">
                                <i class='bx bxs-printer' style="font-size:24px"></i>
                            </button>
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">Code ID</th>
                                <th class="border px-4 py-2">Nama KK</th>
                                <th class="border px-4 py-2">HP</th>
                                <th style="text-align: center;">
                                    <input type="checkbox" id="selectAllCheckbox" style="display:none">
                                    <label for="selectAllCheckbox" style="font-size:24px"><i class='bx bx-check-double'></i></label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($data) {
                                    foreach ($data as $row): ?>
                                        <tr class="border-b hover:bg-gray-100">
                                            <td>
                                                <a href="detailkk.php?nama=<?= urlencode($row['kk_name']) ?>" class="text-blue-500 hover:underline">
                                                    <?php echo htmlspecialchars($row["kk_name"]); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($row["code_id"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["kk_hp"]); ?></td>
                                            <td class="flex justify-center space-x-2">
                                                <button class="bg-yellow-500 text-white px-2 py-1 rounded" data-modal-toggle="editModal" data-id="<?php echo $row['code_id']; ?>" data-name="<?php echo $row['kk_name']; ?>" data-alamat="<?php echo $row['kk_alamat']; ?>" data-hp="<?php echo $row['kk_hp']; ?>" data-foto="<?php echo $row['kk_foto']; ?>">
                                                    <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                                </button>
                                                <a href="kk.php?delete=<?php echo $row['code_id']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $row['kk_name']; ?> ?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                                                    <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                                </a>
                                                <input type="checkbox" class="print-checkbox">    
                                            </td>
                                        </tr>
                                    <?php endforeach; 
                                } else {
                                    echo '<tr><td colspan="3">No data available</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- Modal Tambah Data -->
    <div id="addModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-3 rounded shadow-lg"> <!-- Mengubah p-5 menjadi p-3 untuk memperkecil padding -->
            <h2 class="text-lg font-bold mb-2">Tambah Data Master KK</h2> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
            <form action="api/kk_insert.php" method="POST" enctype="multipart/form-data">
                <div class="mb-2"> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
                    <label class="block mb-1">Code ID (Awali dengan : RT07 dan 5 angka berikutnya!)</label>
                    <input value="RT07" type="text" name="code_id" class="border rounded w-full p-1" required> <!-- Mengubah p-2 menjadi p-1 untuk memperkecil padding -->
                </div>
                <div class="mb-2"> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" name="kk_name" class="border rounded w-full p-1" required> <!-- Mengubah p-2 menjadi p-1 untuk memperkecil padding -->
                </div>
                <div class="mb-2"> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
                    <label class="block mb-1">Alamat</label>
                    <input type="text" name="kk_alamat" class="border rounded w-full p-1" required> <!-- Mengubah p-2 menjadi p-1 untuk memperkecil padding -->
                </div>
                <div class="mb-2"> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
                    <label class="block mb-1">HP</label>
                    <input type="text" name="kk_hp" class="border rounded w-full p-1" required> <!-- Mengubah p-2 menjadi p-1 untuk memperkecil padding -->
                </div>
                <div class="mb-2"> <!-- Mengubah mb-4 menjadi mb-2 untuk memperkecil margin bawah -->
                    <label class="block mb-1">Foto</label>
                    <input type="file" name="kk_foto" class="border rounded w-full p-1"> <!-- Mengubah p-2 menjadi p-1 untuk memperkecil padding -->
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('addModal')">Tutup</button> <!-- Mengubah px-4 py-2 menjadi px-3 py-1 untuk memperkecil ukuran tombol -->
                    <input type="submit" class="bg-blue-500 text-white px-3 py-1 rounded" value="Tambah"> <!-- Mengubah px-4 py-2 menjadi px-3 py-1 untuk memperkecil ukuran tombol -->
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Edit Data -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-5 rounded shadow-lg">
            <h2 class="text-lg font-bold mb-4">Edit Data Master KK</h2>
            <form action="api/kk_update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="code_id" id="edit_code_id">
                <div class="mb-4">
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" name="kk_name" id="edit_kk_name" class="border rounded w-full p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Alamat</label>
                    <input type="text" name="kk_alamat" id="edit_kk_alamat" class="border rounded w-full p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">HP</label>
                    <input type="text" name="kk_hp" id="edit_kk_hp" class="border rounded w-full p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Foto</label>
                    <input type="file" name="kk_foto" class="border rounded w-full p-2">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded mr-2" onclick="toggleModal('editModal')">Tutup</button>
                    <input type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" value="Update">
                </div>
            </form>
        </div>
    </div>

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
        // Tambahkan ini setelah script yang ada
        $(document).ready(function() {
            // Cek apakah DataTable sudah diinisialisasi
            if (!$.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable({
                    responsive: true
                });
            }
        });
    </script>

    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

        // Script untuk mengisi data modal edit
        document.querySelectorAll('[data-modal-toggle="editModal"]').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit_code_id').value = this.getAttribute('data-id');
                document.getElementById('edit_kk_name').value = this.getAttribute('data-name');
                document.getElementById('edit_kk_alamat').value = this.getAttribute('data-alamat');
                document.getElementById('edit_kk_hp').value = this.getAttribute('data-hp');
                toggleModal('editModal');
            });
        });
    </script>


</body>
</html>