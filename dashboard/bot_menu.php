<?php
include 'api/db.php';

// Handle Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $parent_id = $_POST['parent_id'] ?: null;
    $keyword = trim($_POST['keyword']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE tb_botmenu SET parent_id = ?, keyword = ?, description = ?, url = ? WHERE id = ?");
        $stmt->execute([$parent_id, $keyword, $description, $url, $id]);
        session_start();
        $_SESSION['swal'] = ['msg' => 'Menu berhasil diupdate!', 'icon' => 'success'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO tb_botmenu (parent_id, keyword, description, url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$parent_id, $keyword, $description, $url]);
        session_start();
        $_SESSION['swal'] = ['msg' => 'Menu berhasil ditambah!', 'icon' => 'success'];
    }
    header("Location: bot_menu.php#menu-table");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_botmenu WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    session_start();
    $_SESSION['swal'] = ['msg' => 'Menu berhasil dihapus!', 'icon' => 'success'];
    header("Location: bot_menu.php#menu-table");
    exit;
}

include 'header.php'; // Sudah termasuk koneksi dan session

// Ambil semua menu
$stmt = $pdo->query("SELECT * FROM tb_botmenu ORDER BY parent_id, id");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

  <script>
    function openModal(data = {}) {
      document.getElementById('modal').classList.remove('hidden');
      document.getElementById('id').value = data.id || '';
      document.getElementById('parent_id').value = data.parent_id || '';
      document.getElementById('keyword').value = data.keyword || '';
      document.getElementById('description').value = data.description || '';
      document.getElementById('url').value = data.url || '';
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

<div class="table-data" id="menu-table">
    <div class="order">
        <div class="head flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">📋 Daftar Menu Bot</h2>
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah</button>
        </div>
          <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
            <thead class="bg-gray-200">
              <tr>
                <th class="border px-2 py-1 text-left">ID</th>
                <th class="border px-2 py-1 text-center">Parent</th>
                <th class="border px-2 py-1 text-center">Keyword</th>
                <th class="border px-2 py-1 text-left">Deskripsi</th>
                <th class="border px-2 py-1 text-left">URL</th>
                <th class="border px-2 py-1 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($menus as $m): ?>
                <tr class="border-b hover:bg-gray-100">
                  <td class="px-3 py-2"><?= $m['id'] ?></td>
                  <td class="px-3 py-2 text-center"><?= $m['parent_id'] ?? '—' ?></td>
                  <td class="px-3 py-2 font-mono text-center"><?= htmlspecialchars($m['keyword']) ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($m['description']) ?></td>
                  <td class="px-3 py-2 truncate"><?= htmlspecialchars($m['url']) ?></td>
                  <td class="px-1 py-1 text-center">
                    <button onclick='openModal(<?= json_encode($m) ?>)' title="Edit" class="text-blue-600 hover:text-blue-800"><i class='bx bx-edit'></i></button>
                    <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Yakin hapus menu ini?')" title="Hapus" class="text-red-600 hover:text-red-800"><i class='bx bx-trash'></i></a>
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
      <h2 class="text-lg font-semibold mb-4">📝 Form Menu Bot</h2>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="id" id="id">

        <div>
          <label class="block font-medium">Parent Menu</label>
          <select name="parent_id" id="parent_id" class="w-full p-2 border rounded">
            <option value="">-- Menu Utama --</option>
            <?php foreach ($menus as $menu): ?>
              <?php if ($menu['parent_id'] === null): ?>
                <option value="<?= $menu['id'] ?>">
                  <?= htmlspecialchars($menu['description']) ?> (<?= htmlspecialchars($menu['keyword']) ?>)
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-medium">Keyword</label>
          <input type="text" name="keyword" id="keyword" class="w-full p-2 border rounded" required>
        </div>

        <div>
          <label class="block font-medium">Deskripsi</label>
          <input type="text" name="description" id="description" class="w-full p-2 border rounded" required>
        </div>

        <div>
          <label class="block font-medium">URL (jika ada)</label>
          <input type="text" name="url" id="url" class="w-full p-2 border rounded">
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">Batal</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>

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

<?php include 'footer.php'; ?>