<?php
session_start();
include 'header.php';

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Sertakan koneksi database
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $kode = $_GET['delete'];
    $sql = "DELETE FROM tb_warga WHERE kode=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode]);

    header("Location: warga.php");
    exit();
}


// Ambil data dari tabel users
$sql = "SELECT * FROM tb_warga";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Warga</h3>
                        <div class="mb-4 text-center">
                            <button type="button" id="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                            </button>
                        </div>
                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>NIKK</th>
                                <thstyle="text-align: center;">Jenkel</thstyle=>
                                <th>Tempat Lahir</th>
                                <th>Tgl Lahir</th>
                                <th>Alamat</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $row): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td><?php echo htmlspecialchars($row["kode"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nama"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nik"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nikk"]); ?></td>
                                <td><?php echo htmlspecialchars($row["jenkel"]); ?></td>
                                <td><?php echo htmlspecialchars($row["tpt_lahir"]); ?></td>
                                <td><?php echo htmlspecialchars($row["tgl_lahir"]); ?></td>
                                <td><?php echo htmlspecialchars($row["alamat"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button onclick="openEditTarifModal('<?php echo $row['kode']; ?>', '<?php echo $row['nama']; ?>', '<?php echo $row['nik']; ?>')" class="text-blue-600 hover:text-blue-400 font-bold py-1 px-1">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="warga.php?delete=<?php echo $row['kode']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $row['nama']; ?> ?')" class="text-red-600 hover:text-red-400 font-bold py-1 px-1">
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
        <div id="editTarifModal" class="modal hidden fixed z-50 inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
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