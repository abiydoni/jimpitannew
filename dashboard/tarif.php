<?php
session_start();
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $kode_tarif = $_GET['delete'];
    $sql = "DELETE FROM tb_tarif WHERE kode_tarif=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode_tarif]);

    $_SESSION['swal'] = ['msg' => 'Data berhasil dihapus!', 'icon' => 'success'];
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
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: '{$icon}',
        title: '{$msg}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });
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
                                <th class="text-center border px-3 py-2">Icon</th>
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
                                <td class="text-center">
                                    <i class='bx <?php echo htmlspecialchars($tarif["icon"] ?? 'bx-money'); ?>' style="font-size: 24px;"></i>
                                </td>
                                <td><?php echo htmlspecialchars($tarif["nama_tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["tarif"]); ?></td>
                                <td><?php echo htmlspecialchars($tarif["metode"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button onclick="openEditTarifModal('<?php echo $tarif['kode_tarif']; ?>', '<?php echo $tarif['nama_tarif']; ?>', '<?php echo $tarif['tarif']; ?>', '<?php echo $tarif['metode']; ?>', '<?php echo $tarif['icon'] ?? ''; ?>')" class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <button onclick="confirmDelete('<?php echo $tarif['kode_tarif']; ?>', '<?php echo $tarif['nama_tarif']; ?>')" class="text-red-600 hover:text-red-800 font-bold py-1 px-1">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
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
                    <div class="bg-white p-1 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Metode:</label>
                        <select name="metode" id="metode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            <option value="">Pilih Metode</option>
                            <option value="0">Tidak Ditampilkan</option>
                            <option value="1">Bulanan</option>
                            <option value="2">Tahunan</option>
                        </select>
                    </div>
                    <div class="bg-white p-1 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Icon:</label>
                        <input type="text" name="icon" placeholder="Contoh: bx-money, bx-home, bx-car" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <small class="text-gray-500 text-xs">Gunakan format Boxicons (bx-*)</small>
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
                        <label class="block text-sm font-medium text-gray-700">Icon:</label>
                        <input type="text" name="icon" id="edit_icon" placeholder="Contoh: bx-money, bx-home, bx-car" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                        <small class="text-gray-500">Gunakan format Boxicons (bx-*)</small>
                    </div>
                    <div class="bg-white p-2 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Tarif:</label>
                        <input type="text" name="tarif" id="edit_tarif" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                    </div>
                    <div class="bg-white p-1 rounded-lg shadow-md">
                        <label class="block text-sm font-medium text-gray-700">Metode:</label>
                        <select name="metode" id="edit_metode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            <option value="">Pilih Metode</option>
                            <option value="0">Tidak Ditampilkan</option>
                            <option value="1">Bulanan</option>
                            <option value="2">Tahunan</option>
                        </select>
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
    function openEditTarifModal(kode_tarif, nama_tarif, tarif, metode, icon) {
        document.getElementById('edit_kode_tarif').value = kode_tarif;
        document.getElementById('edit_nama_tarif').value = nama_tarif;
        document.getElementById('edit_tarif').value = tarif;
        document.getElementById('edit_metode').value = metode;
        document.getElementById('edit_icon').value = icon;

        const modal = document.getElementById("editTarifModal");
        modal.classList.remove("hidden");
    }

    // Close edit modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const closeeditTarifModal = document.getElementById("closeeditTarifModal");
        const editModal = document.getElementById("editTarifModal");
        
        if (closeeditTarifModal) {
            closeeditTarifModal.onclick = function() {
                editModal.classList.add("hidden");
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            const editModal = document.getElementById("editTarifModal");
            
            if (event.target == modal) {
                modal.classList.add("hidden");
            }
            if (event.target == editModal) {
                editModal.classList.add("hidden");
            }
        }
    });
</script>

<script>
    function confirmDelete(kode_tarif, nama_tarif) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Yakin ingin menghapus data "${nama_tarif}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `tarif.php?delete=${kode_tarif}`;
            }
        });
    }
</script>
<?php
// Tutup koneksi
$pdo = null;
?>