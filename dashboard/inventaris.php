<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   
   include 'api/db.php';
// Handle Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode'] ?? '';
    $kode_brg = $_POST['kode_brg'] ?: null;
    $nama = trim($_POST['nama']);
    $jumlah = trim($_POST['jumlah']);

    if ($kode) {
        $stmt = $pdo->prepare("UPDATE tb_barang SET kode_brg = ?, nama = ?, jumlah = ? WHERE kode = ?");
        $stmt->execute([$kode_brg, $nama, $jumlah, $kode]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO tb_barang (kode_brg, nama, jumlah) VALUES (?, ?, ?)");
        $stmt->execute([$kode_brg, $nama, $jumlah]);
    }

    header("Location: inventaris.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_barang WHERE kode = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: inventaris.php");
    exit;
}

include 'header.php'; // Sudah termasuk koneksi dan session

// Ambil semua menu
$stmt = $pdo->query("SELECT * FROM tb_barang ORDER BY kode_brg");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

  <script>
    function openModal(data = {}) {
      document.getElementById('modal').classList.remove('hidden');
      document.getElementById('kode').value = data.kode || '';
      document.getElementById('kode_brg').value = data.kode_brg || '';
      document.getElementById('nama').value = data.nama || '';
      document.getElementById('jumlah').value = data.jumlah || '';
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
    }

    // Tambahkan SweetAlert2 jika belum ada
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
    // Konfirmasi hapus dengan SweetAlert2
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('a[href*="delete="]').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          Swal.fire({
            title: 'Yakin hapus menu ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            position: 'top-end',
            toast: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = this.href;
            }
          });
        });
      });
    });
  </script>

<div class="table-data" kode="menu-table">
    <div class="order">
        <div class="head flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">📋 Daftar Inventori Barang</h2>
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah</button>
        </div>
        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
          <thead class="bg-gray-200">
            <tr>
              <th class="border px-3 py-2">Kode</th>
              <th class="border px-3 py-2">Kode Barang</th>
              <th class="border px-3 py-2">Nama</th>
              <th class="border px-3 py-2">Jumlah</th>
              <th class="border px-3 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($menus as $m): ?>
              <tr class="border-b hover:bg-gray-100">
                <td class="px-3 py-2 text-left"><?= $m['kode'] ?></td>
                <td class="px-3 py-2 text-left"><?= $m['kode_brg'] ?? '—' ?></td>
                <td class="px-3 py-2 text-left"><?= htmlspecialchars($m['nama']) ?></td>
                <td class="px-3 py-2 text-left"><?= htmlspecialchars($m['jumlah']) ?></td>
                <td class="flex justify-center space-x-2 px-3 py-2">
                  <button onclick='openModal(<?= json_encode($m) ?>)' title="Edit" class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1"><i class='bx bx-edit'></i></button>
                  <a href="?delete=<?= $m['kode'] ?>" onclick="return confirm('Yakin hapus menu ini?')" title="Hapus" class="text-red-600 hover:text-red-800 font-bold py-1 px-1"><i class='bx bx-trash'></i></a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
    </div>
</div>

  <!-- Modal Form -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-xl relative">
      <h2 class="text-lg font-semibold mb-4">📝 Form Inventori Barang</h2>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="kode" id="kode">

        <div>
          <label class="block font-medium">Kode Barang</label>
          <input type="text" name="kode_brg" id="kode_brg" class="w-full p-2 border rounded" required>
        </div>

        <div>
          <label class="block font-medium">Nama Barang</label>
          <input type="text" name="nama" id="nama" class="w-full p-2 border rounded" required>
        </div>

        <div>
          <label class="block font-medium">Jumlah</label>
          <input type="text" name="jumlah" id="jumlah" class="w-full p-2 border rounded" required>
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">Batal</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
<?php include 'footer.php'; ?>