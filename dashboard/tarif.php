<?php
session_start();
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $kode_tarif = $_GET['delete'];
    $sql = "DELETE FROM tb_tarif WHERE kode_tarif=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode_tarif]);

    header("Location: tarif.php");
    exit();
}

include 'header.php';

// Ambil data dari tabel users
$sql = "SELECT * FROM tb_tarif";
$stmt = $pdo->query($sql);
$tarif_1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

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


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Tarif</h3>
                        <div class="mb-4 text-center">
                            <button type="button" id="openModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                            </button>
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="text-left border px-3 py-2">Kode Tarif</th>
                                <th class="text-center border px-3 py-2">Nama Tarif</th>
                                <th class="text-center border px-3 py-2">Tarif</th>
                                <th class="text-center border px-3 py-2">Metode</th>
                                <th class="text-center border px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($tarif_1 as $tarif): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td><?php echo htmlspecialchars($tarif["kode_tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["nama_tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["metode"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button onclick="openEditTarifModal('<?php echo $tarif['kode_tarif']; ?>', '<?php echo $tarif['nama_tarif']; ?>', '<?php echo $tarif['tarif']; ?>')" class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="setting.php?delete=<?php echo $tarif['kode_tarif']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $tarif['nama_tarif']; ?> ?')" class="text-red-600 hover:text-red-800 font-bold py-1 px-1">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                    </a>
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
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Jenis Kelamin *</label>
                        <select name="jenkel" id="jenkel" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                            <option value="">Pilih Metode</option>
                            <option value="0">Bulanan</option>
                            <option value="1">Tahunan</option>
                        </select>
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
<?php include 'footer.php'; ?>
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
<?php
// Tutup koneksi
$pdo = null;
?>