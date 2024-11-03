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

// Fungsi Insert atau Update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_code = $_POST['id_code'] ?? null; // Tambahkan validasi
    $user_name = $_POST['user_name'] ?? ''; // Tambahkan validasi
    $name = $_POST['name'] ?? ''; // Tambahkan validasi
    $shift = $_POST['shift'] ?? ''; // Tambahkan validasi
    $role = $_POST['role'] ?? ''; // Tambahkan validasi
    if (empty($user_name) || empty($name) || empty($shift) || empty($role)) {
        // Tampilkan pesan kesalahan
        echo "Semua field harus diisi!";
        exit();
    }

    // Hanya hash password jika ada perubahan
    $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($id_code) {
        // Update data
        $sql = "UPDATE users SET user_name=?, name=?, shift=?, role=?". ($password ? ", password=?" : "") ." WHERE id_code=?";
        $stmt = $pdo->prepare($sql);
        $params = [$user_name, $name, $shift, $role];
        if ($password) {
            $params[] = $password; // Tambahkan password jika ada
        }
        $params[] = $id_code;
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
    $id_code = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_code]);

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <title>Report</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Urut Tanggal Terbaru</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
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
            <li class="active"><a href="#"><i class='bx bxs-report'></i><span class="text">Report</span></a></li>
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
                            <a href="#">Report</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="index.php">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Form untuk Menambah atau Edit Data -->
            <div id="userForm" style="margin-bottom: 20px;">
                <h2 id="formTitle">Tambah Pengguna</h2>
                <form method="POST" action="crud_users.php">
            <input type="hidden" id="idCode" name="id_code">
            <label>Username:</label>
            <input type="text" id="userName" name="user_name" required><br><br>
            <label>Nama:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label>Password:</label>
            <input type="password" id="password" name="password"><br><br>
            <label>Shift:</label>
            <input type="text" id="shift" name="shift"><br><br>
            <label>Role:</label>
            <input type="text" id="role" name="role"><br><br>
            <button type="submit">Simpan</button>
        </form>

        <h2>Daftar Pengguna</h2>
        <table border="1">
            <tr>
                <th>ID Code</th>
                <th>Username</th>
                <th>Nama</th>
                <th>Shift</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user["id_code"]); ?></td>
                <td><?php echo htmlspecialchars($user["user_name"]); ?></td>
                <td><?php echo htmlspecialchars($user["name"]); ?></td>
                <td><?php echo htmlspecialchars($user["shift"]); ?></td>
                <td><?php echo htmlspecialchars($user["role"]); ?></td>
                <td>
                    <button onclick="editUser('<?php echo $user['id_code']; ?>', '<?php echo $user['user_name']; ?>', '<?php echo $user['name']; ?>', '<?php echo $user['shift']; ?>', '<?php echo $user['role']; ?>')">Edit</button>
                    <a href="crud_users.php?delete=<?php echo $user['id_code']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $user['name']; ?> ?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
            </div>
        </main>

        <!-- MAIN -->
    </section>
    <!-- CONTENT --> 
    <script src="js/monthSelectPlugin.js"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script src="js/script.js"></script>
    <script src="js/report.js"></script>
    <script src="js/export.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.1/exceljs.min.js"></script>
    <script>
        function editUser(idCode, userName, name, shift, role) {
            document.getElementById('idCode').value = idCode;
            document.getElementById('userName').value = userName;
            document.getElementById('name').value = name;
            document.getElementById('password').value = ""; // Kosongkan password saat edit
            document.getElementById('shift').value = shift;
            document.getElementById('role').value = role;
            document.getElementById('formTitle').innerText = "Edit Pengguna";
        }

        function cancelEdit() {
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