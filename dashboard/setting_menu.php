<?php
  ob_start();
  include 'header.php';

// Proses value checkbox agar tetap 0 jika tidak dicentang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['status'] = isset($_POST['status']) ? 1 : 0;
    $_POST['pengurus'] = isset($_POST['pengurus']) ? 1 : 0;
    $_POST['admin'] = isset($_POST['admin']) ? 1 : 0;
    $_POST['s_admin'] = isset($_POST['s_admin']) ? 1 : 0;
    $_POST['warga'] = isset($_POST['warga']) ? 1 : 0;
}

// Handle tambah menu
if (isset($_POST['add_menu'])) {
    $stmt = $pdo->prepare("INSERT INTO tb_menu (nama, alamat_url, ikon, status, pengurus, admin, s_admin, warga) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nama'],
        $_POST['alamat_url'],
        $_POST['ikon'],
        $_POST['status'],
        $_POST['pengurus'],
        $_POST['admin'],
        $_POST['s_admin'],
        $_POST['warga']
    ]);
    header("Location: setting_menu.php#menu-table");
    exit;
}

// Handle edit menu
if (isset($_POST['edit_menu'])) {
    $stmt = $pdo->prepare("UPDATE tb_menu SET nama=?, alamat_url=?, ikon=?, status=?, pengurus=?, admin=?, s_admin=?, warga=? WHERE kode=?");
    $stmt->execute([
        $_POST['nama'],
        $_POST['alamat_url'],
        $_POST['ikon'],
        $_POST['status'],
        $_POST['pengurus'],
        $_POST['admin'],
        $_POST['s_admin'],
        $_POST['warga'],
        $_POST['kode']
    ]);
    header("Location: setting_menu.php#menu-table");
    exit;
}

// Handle hapus menu
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_menu WHERE kode=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: setting_menu.php#menu-table");
    exit;
}

// Ambil semua menu
$menus = $pdo->query("SELECT * FROM tb_menu ORDER BY kode DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-data" id="menu-table">
    <div class="order">
        <div class="head flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">📋 Manajemen Menu (tb_menu)</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Menu</button>
        </div>
        <div class="overflow-x-auto">
            <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="text-left border px-3 py-2">Kode</th>
                        <th class="text-left border px-3 py-2">Nama</th>
                        <th class="text-left border px-3 py-2">URL</th>
                        <th class="text-left border px-3 py-2">Ikon</th>
                        <th class="text-center border px-3 py-2">Warga</th>
                        <th class="text-center border px-3 py-2">User</th>
                        <th class="text-center border px-3 py-2">Pengurus</th>
                        <th class="text-center border px-3 py-2">Admin</th>
                        <th class="text-center border px-3 py-2">S Admin</th>
                        <th class="text-center border px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="border px-3 py-2"><?= $m['kode'] ?></td>
                            <td class="border px-3 py-2"><?= htmlspecialchars($m['nama']) ?></td>
                            <td class="border px-3 py-2"><?= htmlspecialchars($m['alamat_url']) ?></td>
                            <td class="border px-3 py-2"><i class="bx <?= htmlspecialchars($m['ikon']) ?> text-lg"></i> <span class="text-xs text-gray-500"><?= htmlspecialchars($m['ikon']) ?></span></td>
                            <td class="border px-3 py-2 text-center"><?= $m['warga'] ?></td>
                            <td class="border px-3 py-2 text-center"><?= $m['status'] ?></td>
                            <td class="border px-3 py-2 text-center"><?= $m['pengurus'] ?></td>
                            <td class="border px-3 py-2 text-center"><?= $m['admin'] ?></td>
                            <td class="border px-3 py-2 text-center"><?= $m['s_admin'] ?></td>
                            <td class="border px-1 py-1 text-center">
                                <button onclick='openEditModal(<?= json_encode($m) ?>)' class="text-blue-600 hover:text-blue-800">✏️</button>
                                <a onclick="return confirm('Hapus menu ini?')" href="?delete=<?= $m['kode'] ?>" class="text-red-600 hover:text-red-800">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="menuModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-full max-w-lg">
        <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Menu</h2>
        <form method="post">
            <input type="hidden" name="kode" id="menu_kode">
            <input type="text" name="nama" id="nama" placeholder="Nama Menu" class="w-full border p-2 mb-2" required>
            <input type="text" name="alamat_url" id="alamat_url" placeholder="Alamat URL" class="w-full border p-2 mb-2" required>
            <input type="text" name="ikon" id="ikon" placeholder="Boxicons Class" class="w-full border p-2 mb-2" required>
            <div class="grid grid-cols-2 gap-2 mb-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="warga" id="warga" value="1">
                    <span>Warga</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="status" id="status" value="1">
                    <span>User</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="pengurus" id="pengurus" value="1">
                    <span>Pengurus</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="admin" id="admin" value="1">
                    <span>Admin</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="s_admin" id="s_admin" value="1">
                    <span>Super Admin</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                <button id="submitBtn" type="submit" name="add_menu" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
<script>
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Menu';
    document.getElementById('submitBtn').name = 'add_menu';
    document.querySelector("form").reset();
    document.getElementById('menu_kode').value = '';
    // Uncheck all checkboxes
    document.getElementById('status').checked = false;
    document.getElementById('warga').checked = false;
    document.getElementById('pengurus').checked = false;
    document.getElementById('admin').checked = false;
    document.getElementById('s_admin').checked = false;
    document.getElementById('menuModal').classList.remove('hidden');
}

function openEditModal(data) {
    document.getElementById('modalTitle').innerText = 'Edit Menu';
    document.getElementById('submitBtn').name = 'edit_menu';
    document.getElementById('menu_kode').value = data.kode;
    document.getElementById('nama').value = data.nama;
    document.getElementById('alamat_url').value = data.alamat_url;
    document.getElementById('ikon').value = data.ikon;
    document.getElementById('status').checked = data.status == 1;
    document.getElementById('pengurus').checked = data.pengurus == 1;
    document.getElementById('admin').checked = data.admin == 1;
    document.getElementById('s_admin').checked = data.s_admin == 1;
    document.getElementById('warga').checked = data.warga == 1;
    document.getElementById('menuModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('menuModal').classList.add('hidden');
}
</script> 

<?php
  ob_end_flush();
  ?>