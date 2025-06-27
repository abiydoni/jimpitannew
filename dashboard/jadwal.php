<?php
session_start();
// Sertakan koneksi database
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $id_code = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id_code=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_code]);
    session_start();
    $_SESSION['swal'] = ['msg' => 'User berhasil dihapus!', 'icon' => 'success'];
    header("Location: jadwal.php");
    exit();
}


// Ambil data dari tabel users
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data kk_name dari tabel mater_kk
$sql_kk = "SELECT kk_name FROM master_kk";
$stmt_kk = $pdo->query($sql_kk);
$kk_names = $stmt_kk->fetchAll(PDO::FETCH_ASSOC);

include 'header.php'; // Sudah termasuk koneksi dan session
?>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data User dan Jadwal Jaga</h3>
                        <div class="mb-4 text-center">
                            <button type="button" id="openModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                            </button>
                            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <a href="api/users_print.php" class="flex items-center">
                                    <i class='bx bx-printer' style="font-size:24px"></i> <!-- Ikon untuk print report -->
                                </a>
                            </button>                        
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-3 py-2">ID</th>
                                <th class="border px-3 py-2">Username</th>
                                <th class="border px-3 py-2">Nama</th>
                                <th class="border px-3 py-2">Shift</th>
                                <th class="border px-3 py-2">Role</th>
                                <th class="border px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-3 py-2"><?php echo htmlspecialchars($user["id_code"]); ?></td>
                                <td class="px-3 py-2"><?php echo htmlspecialchars($user["user_name"]); ?></td>
                                <td class="px-3 py-2"><?php echo htmlspecialchars($user["name"]); ?></td>
                                <td class="px-3 py-2"><?php echo htmlspecialchars($user["shift"]); ?></td>
                                <td class="px-3 py-2"><?php echo htmlspecialchars($user["role"]); ?></td>
                                <td class="px-3 py-2 text-center">
                                    <button onclick="openEditUserModal('<?php echo $user['id_code']; ?>', '<?php echo $user['user_name']; ?>', '<?php echo $user['name']; ?>', '<?php echo $user['shift']; ?>', '<?php echo $user['role']; ?>')" class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="jadwal.php?delete=<?php echo $user['id_code']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $user['name']; ?> ?')" class="text-red-600 hover:text-red-800 font-bold py-1 px-1">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                    </a>
                                    <button onclick="openChangePasswordModal('<?php echo $user['id_code']; ?>', '<?php echo $user['name']; ?>', '<?php echo $user['password']; ?>')" class="text-yellow-600 hover:text-yellow-800 font-bold py-1 px-1">
                                        <i class='bx bx-key'></i> <!-- Ikon untuk ubah password -->
                                    </button>                                
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

    <!-- Modal Structure -->
    <div id="myModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="modal-content bg-white p-2 rounded-lg shadow-md w-1/4"> <!-- Mengatur lebar modal lebih kecil -->
            <span id="closeModal" class="close cursor-pointer text-gray-500 float-right">&times;</span>
            <h3 class="text-lg font-bold text-gray-800">Input Data Users</h3>
            <form action="api/users_save.php" method="POST" class="space-y-1"> <!-- Mengurangi jarak antar elemen -->
                <div class="bg-white p-1 rounded-lg shadow-md"> <!-- Mengurangi padding -->
                    <label class="block text-sm font-medium text-gray-700">ID Code:</label>
                    <input type="text" value="USER" name="id_code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>                
                </div>
                <div class="bg-white p-1 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="user_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                </div>
                <div class="bg-white p-1 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <select name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="" disabled selected>Pilih Nama KK</option>
                        <?php foreach ($kk_names as $kk): ?>
                            <option value="<?= htmlspecialchars($kk['kk_name']) ?>"><?= htmlspecialchars($kk['kk_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bg-white p-1 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    <input type="checkbox" id="togglePassword" class="mt-1" onclick="togglePasswordVisibility()">
                    <label for="togglePassword" class="text-sm">Tampilkan Password</label>
                </div>
                <div class="bg-white p-1 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Shift:</label>
                    <select name="shift" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="Monday">Senin</option>
                        <option value="Tuesday">Selasa</option>
                        <option value="Wednesday">Rabu</option>
                        <option value="Thursday">Kamis</option>
                        <option value="Friday">Jumat</option>
                        <option value="Saturday">Sabtu</option>
                        <option value="Sunday">Minggu</option>
                    </select>
                </div>
                <div class="bg-white p-1 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Role:</label>
                    <select name="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="pengurus">Pengurus</option>
                        <option value="admin">Admin</option>
                        <option value="user" selected>User Jaga</option> <!-- Nilai default diatur ke 'user' -->
                    </select>
                </div>                
                <button type="submit" class="mt-1 bg-blue-500 text-white font-semibold py-1 px-2 rounded-md hover:bg-blue-600 transition duration-200">Submit</button> <!-- Mengurangi padding -->
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
                    <select name="name" id="edit_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="" disabled selected>Pilih Nama KK</option>
                        <?php foreach ($kk_names as $kk): ?>
                            <option value="<?= htmlspecialchars($kk['kk_name']) ?>"><?= htmlspecialchars($kk['kk_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Shift:</label>
                    <select name="shift" id="edit_shift" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="Monday">Senin</option>
                        <option value="Tuesday">Selasa</option>
                        <option value="Wednesday">Rabu</option>
                        <option value="Thursday">Kamis</option>
                        <option value="Friday">Jumat</option>
                        <option value="Saturday">Sabtu</option>
                        <option value="Sunday">Minggu</option>
                    </select>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Role:</label>
                    <select name="role" id="edit_role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <option value="pengurus">Pengurus</option>
                        <option value="admin">Admin</option>
                        <option value="user" selected>User Jaga</option> <!-- Nilai default diatur ke 'user' -->
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
                
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" name="name" id="change_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" readonly>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="user_name" id="change_user_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" readonly>
                </div>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-gray-700">Password Baru:</label>
                    <input type="password" name="new_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                </div>
                
                <button type="submit" class="mt-2 bg-blue-500 text-white font-semibold py-1 px-3 rounded-md hover:bg-blue-600 transition duration-200">Ubah Password</button>
            </form>
        </div>
    </div>

<?php include 'footer.php'; ?>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('input[name="password"]');
            const toggle = document.getElementById('togglePassword');
            passwordInput.type = toggle.checked ? 'text' : 'password';
        }
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
        function openChangePasswordModal(id_code, name, user_name) {
            document.getElementById('change_id_code').value = id_code;
            document.getElementById('change_name').value = name;
            document.getElementById('change_user_name').value = user_name;
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

<?php
// Tambahkan script untuk SweetAlert2 toast jika ada notifikasi dari session
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['swal'])) {
  $msg = $_SESSION['swal']['msg'];
  $icon = $_SESSION['swal']['icon'];
  echo "<script>
    if (!window.Swal) {
      var script = document.createElement('script');
      script.src = 'js/sweetalert2.all.min.js';
      document.head.appendChild(script);
    }
    function showToast(msg, icon = 'success') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: msg,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });
    }
    document.addEventListener('DOMContentLoaded', function() {
      showToast('{$msg}', '{$icon}');
    });
  </script>";
  unset($_SESSION['swal']);
}
?>

<?php
// Tutup koneksi
$pdo = null;
?>