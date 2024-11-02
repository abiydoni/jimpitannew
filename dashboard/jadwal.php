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

// Mengambil data dari tabel users
$sql = "SELECT * FROM users"; // Pastikan ini sesuai dengan yang diinginkan
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi Insert atau Update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $id_code = $_POST['id_code'] ?? ''; // Tambahkan validasi
    $user_name = $_POST['user_name'] ?? ''; // Tambahkan validasi
    $name = $_POST['name'] ?? ''; // Tambahkan validasi
    $shift = $_POST['shift'] ?? ''; // Tambahkan validasi
    $role = $_POST['role'] ?? ''; // Tambahkan validasi
    if (empty($id_code) || empty($user_name) || empty($name) || empty($shift) || empty($role)) {
        // Tampilkan pesan kesalahan
        echo "Semua field harus diisi!";
        exit();
    }

    // Hanya hash password jika ada perubahan
    $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($id) {
        // Update data
        $sql = "UPDATE users SET id_code=?, user_name=?, name=?, shift=?, role=?". ($password ? ", password=?" : "") ." WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $params = [$id_code, $user_name, $name, $shift, $role];
        if ($password) {
            $params[] = $password; // Tambahkan password jika ada
        }
        $params[] = $id;
        $stmt->execute($params);
    } else {
        // Insert data baru
        $sql = "INSERT INTO users (id_code, user_name, name, password, shift, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_code, $user_name, $name, $password, $shift, $role]);
    }
    header("Location: api/crud_users.php");
    exit();
}

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: crud_users.php");
    exit();
}

// Mengambil data dari tabel users
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <title>Jadwal Jaga</title>
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
            <li class="active"><a href="jadwal.php"><i class='bx bxs-group'></i><span class="text">Jadwal Jaga</span></a></li>
            <li><a href="kk.php"><i class='bx bxs-group'></i><span class="text">KK</span></a></li>
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
                            <a href="#">Users</a>
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
                        <h3>Jadwal Jaga</h3>
                        <div class="mb-4 text-center">
                            <button>
                                <a href="api/print_user.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Print Report
                                </a>
                            </button>
                            </button>
                            <button class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded mb-5" onclick="openModal()">Tambah Data</button>
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th style="text-align: left;">Kode ID</th>
                                <th style="text-align: center;">Nama</th>
                                <th style="text-align: center;">Shift</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td><?php echo htmlspecialchars($user["id_code"]); ?></td>
                                <td><?php echo htmlspecialchars($user["name"]); ?></td>
                                <td><?php echo htmlspecialchars($user["shift"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo $user['id_code']; ?>', '<?php echo $user['user_name']; ?>', '<?php echo $user['name']; ?>', '<?php echo $user['shift']; ?>', '<?php echo $user['role']; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">Edit</button>
                                    <a href="crud_users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

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
        // Tambahkan ini setelah script yang ada
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true
            });
        });
    </script>
    <script>
        function editUser(id, idCode, userName, name, shift, role) {
            document.getElementById('userId').value = id;
            document.getElementById('idCode').value = idCode;
            document.getElementById('userName').value = userName;
            document.getElementById('name').value = name;
            document.getElementById('password').value = ""; // Kosongkan password saat edit
            document.getElementById('shift').value = shift;
            document.getElementById('role').value = role;
            document.getElementById('formTitle').innerText = "Edit Pengguna";
        }

        function cancelEdit() {
            document.getElementById('userId').value = "";
            document.getElementById('idCode').value = "";
            document.getElementById('userName').value = "";
            document.getElementById('name').value = "";
            document.getElementById('password').value = "";
            document.getElementById('shift').value = "";
            document.getElementById('role').value = "";
            document.getElementById('formTitle').innerText = "Tambah Pengguna";
        }
    </script>
</body>
</html>

<?php
// Tutup koneksi
$pdo = null;
?>