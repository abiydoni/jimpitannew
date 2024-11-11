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
    $id_code = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id_code=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_code]);

    header("Location: jadwal.php");
    exit();
}


// Ambil data dari tabel users
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
                        <h3>Data User dan Jadwal Jaga</h3>
                        <div class="mb-4 text-center">
                            <button id="openModal" class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                                <i class='bx bx-plus'></i> <!-- Ikon untuk tambah data -->
                            </button>
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <a href="api/users_print.php" class="flex items-center">
                                    <i class='bx bx-printer'></i> <!-- Ikon untuk print report -->
                                    <span class="ml-2">Print Report</span>
                                </a>
                            </button>                        
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
                                    <button onclick="openEditUserModal('<?php echo $user['id_code']; ?>', '<?php echo $user['user_name']; ?>', '<?php echo $user['name']; ?>', '<?php echo $user['shift']; ?>', '<?php echo $user['role']; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="jadwal.php?delete=<?php echo $user['id_code']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $user['name']; ?> ?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                    </a>
                                    <a href="#" onclick="openChangePasswordModal('<?php echo $user['id_code']; ?>', '<?php echo $user['user_name']; ?>', '<?php echo $user['name']; ?>')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded">
                                        <i class='bx bx-key'></i> <!-- Ikon untuk ubah password -->
                                    </a>                                
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Structure -->
    <div id="myModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="modal-content bg-white p-4 rounded-lg shadow-md w-1/3"> <!-- Mengatur lebar modal -->
            <span id="closeModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
            <h3 class="text-lg font-bold text-gray-800">Input Data Users</h3>
            <form action="api/users_save.php" method="POST" class="space-y-2"> <!-- Mengurangi jarak antar elemen -->
                <div class="bg-white p-2 rounded-lg shadow-md"> <!-- Mengurangi padding -->
                    <label class="block text-sm font-medium text-gray-700">ID Code:</label>
                    <input type="text" value="USER" name="id_code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>                
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="user_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    <input type="checkbox" id="togglePassword" class="mt-2" onclick="togglePasswordVisibility()">
                    <label for="togglePassword" class="text-sm">Tampilkan Password</label>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Shift:</label>
                    <select name="shift" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Role:</label>
                    <select name="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="admin">Admin</option>
                        <option value="user" selected>User</option> <!-- Nilai default diatur ke 'user' -->
                    </select>
                </div>                
                <button type="submit" class="mt-2 bg-blue-500 text-white font-semibold py-1 px-3 rounded-md hover:bg-blue-600 transition duration-200">Submit</button> <!-- Mengurangi padding -->
            </form>
        </div>
    </div>
    <div id="editUserModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="modal-content bg-white p-4 rounded-lg shadow-md w-1/3">
            <span id="closeEditUserModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
            <h3 class="text-lg font-bold text-gray-800">Edit User</h3>
            <form action="api/users_edit.php" method="POST" class="space-y-2">
                <input type="hidden" name="id_code" id="edit_id_code">
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="user_name" id="edit_user_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" name="name" id="edit_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Shift:</label>
                    <select name="shift" id="edit_shift" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Role:</label>
                    <select name="role" id="edit_role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <button type="submit" class="mt-2 bg-blue-500 text-white font-semibold py-1 px-3 rounded-md hover:bg-blue-600 transition duration-200">Update</button>
            </form>
        </div>
    </div>
    <!-- Modal untuk Ubah Password -->
    <div id="changePasswordModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="modal-content bg-white p-4 rounded-lg shadow-md w-1/3">
            <span id="closeChangePasswordModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
            <h3 class="text-lg font-bold text-gray-800">Ubah Password</h3>
            <form action="api/change_password.php" method="POST" class="space-y-2">
                <input type="hidden" name="id_code" id="change_id_code">                
                <label class="block text-sm font-medium text-gray-700">Nama : <span id="change_name"></span></label> <!-- Menggunakan elemen span untuk menampilkan nama pengguna -->
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Password Baru:</label>
                    <input type="password" name="new_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <button type="submit" class="mt-2 bg-blue-500 text-white font-semibold py-1 px-3 rounded-md hover:bg-blue-600 transition duration-200">Ubah Password</button>
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
    <script src="js/print.js"></script>
	<script src="js/qrcode.min.js"></script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('input[name="password"]');
            const toggle = document.getElementById('togglePassword');
            passwordInput.type = toggle.checked ? 'text' : 'password';
        }
    </script>
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
    function openEditUserModal(id_code, user_name, name, shift, role) {
        document.getElementById('edit_id_code').value = id_code;
        document.getElementById('edit_user_name').value = user_name;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_shift').value = shift;
        document.getElementById('edit_role').value = role;

        const modal = document.getElementById("editUserModal");
        modal.classList.remove("hidden");
    }

    const closeEditUserModal = document.getElementById("closeEditUserModal");
    closeEditUserModal.onclick = function() {
        const modal = document.getElementById("editUserModal");
        modal.classList.add("hidden");
    }

    window.onclick = function(event) {
        const modal = document.getElementById("editUserModal");
        if (event.target == modal) {
            modal.classList.add("hidden");
        }
    }
    function toggleEditNewPasswordVisibility() {
            const newPasswordInput = document.getElementById('edit_new_password');
            const toggle = document.getElementById('toggleEditNewPassword');
            newPasswordInput.type = toggle.checked ? 'text' : 'password';
        }

</script>

<script>
        function openChangePasswordModal(id_code) {
            document.getElementById('change_id_code').value = id_code;
            document.getElementById('change_name').value = name; // Menampilkan nama pengguna di elemen span
            const modal = document.getElementById("changePasswordModal");
            modal.classList.remove("hidden");
        }

        const closeChangePasswordModal = document.getElementById("closeChangePasswordModal");
        closeChangePasswordModal.onclick = function() {
            const modal = document.getElementById("changePasswordModal");
            modal.classList.add("hidden");
        }

        window.onclick = function(event) {
            const modal = document.getElementById("changePasswordModal");
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