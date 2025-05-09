<?php
session_start();

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

// Periksa apakah pengguna adalah admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Sertakan koneksi database
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $kode_tarif = $_GET['delete'];
    $sql = "DELETE FROM tb_tarif WHERE kode_tarif=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode_tarif]);

    header("Location: setting.php");
    exit();
}


// Ambil data dari tabel users
$sql = "SELECT * FROM tb_tarif";
$stmt = $pdo->query($sql);
$tarif_1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <title>Setting</title>
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
            <li><a href="kk.php"><i class='bx bxs-group'></i><span class="text">KK</span></a></li>
            <li><a href="report.php"><i class='bx bxs-report'></i><span class="text">Report</span></a></li>
            <li><a href="keuangan.php"><i class='bx bxs-wallet'></i><span class="text">Keuangan</span></a></li>
        </ul>
        <ul class="side-menu">
            <li class="active"><a href="setting.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
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
                            <a href="#">Setting</a>
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
                        <h3>Daftar Tarif</h3>
                        <div class="mb-4 text-center">
                            <button type="button" id="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                            </button>
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th style="text-align: left;">Kode Tarif</th>
                                <th style="text-align: center;">Nama Tarif</th>
                                <th style="text-align: center;">Tarif</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($tarif_1 as $tarif): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td><?php echo htmlspecialchars($tarif["kode_tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["nama_tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["tarif"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button onclick="openEditTarifModal('<?php echo $tarif['kode_tarif']; ?>', '<?php echo $tarif['nama_tarif']; ?>', '<?php echo $tarif['tarif']; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="setting.php?delete=<?php echo $tarif['kode_tarif']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $tarif['nama_tarif']; ?> ?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!-- Modal Structure -->
        <div id="myModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="modal-content bg-white p-2 rounded-lg shadow-md w-1/4"> <!-- Mengatur lebar modal lebih kecil -->
                <span id="closeModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
                <h3 class="text-lg font-bold text-gray-800">Input Data Tarif</h3>
                <form action="api/tarif_save.php" method="POST" class="space-y-1"> <!-- Mengurangi jarak antar elemen -->
                    <div class="bg-white p-1 rounded-lg shadow-md"> <!-- Mengurangi padding -->
                        <label class="block text-sm font-medium text-gray-700">Kode Tarif:</label>
                        <input type="text" name="kode_tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>                
                    </div>
                    <div class="bg-white p-1 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Nama Tarif:</label>
                        <input type="text" name="nama_tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    </div>
                    <div class="bg-white p-1 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Tarif:</label>
                        <input type="text" name="tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    </div>
                    <button type="submit" class="mt-1 bg-blue-500 text-white font-semibold py-1 px-2 rounded-md hover:bg-blue-600 transition duration-200">Submit</button> <!-- Mengurangi padding -->
                </form>
            </div>
        </div>
        <div id="editTarifModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="modal-content bg-white p-4 rounded-lg shadow-md w-1/3">
                <span id="closeeditTarifModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
                <h3 class="text-lg font-bold text-gray-800">Edit Tarif</h3>
                <form action="api/tarif_edit.php" method="POST" class="space-y-2">
                    <input type="hidden" name="kode_tarif" id="edit_kode_tarif">
                    <div class="bg-white p-2 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Nama Tarif:</label>
                        <input type="text" name="nama_tarif" id="edit_nama_tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    </div>
                    <div class="bg-white p-2 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Tarif:</label>
                        <input type="text" name="tarif" id="edit_tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    </div>
                    <button type="submit" class="mt-2 bg-blue-500 text-white font-semibold py-1 px-3 rounded-md hover:bg-blue-600 transition duration-200">Update</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script src="js/script.js"></script>

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
    const modal = document.getElementById("myModal");
    const openModal = document.getElementById("openModal");
    const closeModal = document.getElementById("closeModal");

    openModal.onclick = function() {
        modal.classList.remove("hidden");
    }

    closeModal.onclick = function() {
        modal.classList.add("hidden");
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.add("hidden");
        }
    }
</script>

<script>
    function openEditTarifModal(kode_tarif, nama_tarif, tarif) {
        document.getElementById('edit_kode_tarif').value = kode_tarif;
        document.getElementById('edit_nama_tarif').value = nama_tarif;
        document.getElementById('edit_tarif').value = tarif;

        const modal = document.getElementById("editTarifModal");
        modal.classList.remove("hidden");
    }

    const closeeditTarifModal = document.getElementById("closeeditTarifModal");
    closeeditTarifModal.onclick = function() {
        const modal = document.getElementById("editTarifModal");
        modal.classList.add("hidden");
    }

    window.onclick = function(event) {
        const modal = document.getElementById("editTarifModal");
        if (event.target == modal) {
            modal.classList.add("hidden");
        }
    }
</script>

</body>
</html>

<?php
// Tutup koneksi
$pdo = null;
?>