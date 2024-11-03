<?php
session_start();
include 'db.php'; // Sertakan koneksi database

// Fungsi Insert atau Update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_code = $_POST['id_code'] ?? null; // Tambahkan validasi
    $user_name = $_POST['user_name'] ?? ''; // Tambahkan validasi
    $name = $_POST['name'] ?? ''; // Tambahkan validasi
    $shift = $_POST['shift'] ?? ''; // Tambahkan validasi
    $role = $_POST['role'] ?? ''; // Tambahkan validasi

    if (empty($user_name) || empty($name) || empty($shift) || empty($role)) {
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
    header("Location: crud_users.php");
    exit();
}

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $id_code = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id_code=?";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Users</title>
</head>
<body>
    <h1>CRUD Users</h1>
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

    <script>
        function editUser(idCode, userName, name, shift, role) {
            document.getElementById('idCode').value = idCode;
            document.getElementById('userName').value = userName;
            document.getElementById('name').value = name;
            document.getElementById('password').value = ""; // Kosongkan password saat edit
            document.getElementById('shift').value = shift;
            document.getElementById('role').value = role;
        }
    </script>
</body>
</html>