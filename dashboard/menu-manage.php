<?php
include 'header.php'; // Sudah termasuk koneksi dan session

// Handle tambah menu
if (isset($_POST['add_menu'])) {
    $stmt = $pdo->prepare("INSERT INTO tb_dashboard_menu (title, icon, url, urutan, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'],
        $_POST['icon'],
        $_POST['url'],
        $_POST['urutan'],
        $_POST['role']
    ]);
    header("Location: menu-manage.php#menu-table");
    exit;
}

// Handle edit menu
if (isset($_POST['edit_menu'])) {
    $stmt = $pdo->prepare("UPDATE tb_dashboard_menu SET title=?, icon=?, url=?, urutan=?, role=? WHERE id=?");
    $stmt->execute([
        $_POST['title'],
        $_POST['icon'],
        $_POST['url'],
        $_POST['urutan'],
        $_POST['role'],
        $_POST['id']
    ]);
    header("Location: menu-manage.php#menu-table");
    exit;
}

// Handle hapus menu
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_dashboard_menu WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: menu-manage.php#menu-table");
    exit;
}

// Ambil semua menu
$menus = $pdo->query("SELECT * FROM tb_dashboard_menu ORDER BY urutan")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="table-data" id="menu-table">
    <div class="order">
        <div class="head flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">📋 Manajemen Menu Dashboard</h2>
        <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Menu</button>
        </div>
        <!-- Tabel -->
        <div class="overflow-x-auto">
          <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 border">#</th>
                        <th class="px-3 py-2 border">Judul</th>
                        <th class="px-3 py-2 border text-center">Icon</th>
                        <th class="px-3 py-2 border">URL</th>
                        <th class="px-3 py-2 border text-center">Urutan</th>
                        <th class="px-3 py-2 border">Role</th>
                        <th class="px-3 py-2 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="border px-3 py-2"><?= $m['id'] ?></td>
                            <td class="border px-3 py-2"><?= $m['title'] ?></td>
                            <td class="border px-3 py-2 text-center"><i class="bx <?= $m['icon'] ?>"></i></td>
                            <td class="border px-3 py-2"><?= $m['url'] ?></td>
                            <td class="border px-3 py-2 text-center"><?= $m['urutan'] ?></td>
                            <td class="border px-3 py-2"><?= $m['role'] ?></td>
                            <td class="border px-1 py-1 text-center">
                                <button onclick='openEditModal(<?= json_encode($m) ?>)' class="text-blue-600">✏️</button>
                                <a onclick="return confirm('Hapus menu ini?')" href="?delete=<?= $m['id'] ?>" class="text-red-600">🗑️</a>
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
    <div class="bg-white p-6 rounded w-full max-w-md">
        <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Menu</h2>
        <form method="post">
            <input type="hidden" name="id" id="menu_id">
            <input type="text" name="title" id="title" placeholder="Judul Menu" class="w-full border p-2 mb-2" required>
            <input type="text" name="icon" id="icon" placeholder="Boxicons Class" class="w-full border p-2 mb-2" required>
            <input type="text" name="url" id="url" placeholder="Link URL" class="w-full border p-2 mb-2" required>
            <input type="number" name="urutan" id="urutan" placeholder="Urutan" class="w-full border p-2 mb-2" required>
            <input type="text" name="role" id="role" placeholder="Role (admin,pengurus,...)" class="w-full border p-2 mb-2" required>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                <button id="submitBtn" type="submit" name="add_menu" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
<!-- Script Modal -->
<script>
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Menu';
    document.getElementById('submitBtn').name = 'add_menu';
    document.querySelector("form").reset();
    document.getElementById('menu_id').value = '';
    document.getElementById('menuModal').classList.remove('hidden');
}

function openEditModal(data) {
    document.getElementById('modalTitle').innerText = 'Edit Menu';
    document.getElementById('submitBtn').name = 'edit_menu';
    document.getElementById('menu_id').value = data.id;
    document.getElementById('title').value = data.title;
    document.getElementById('icon').value = data.icon;
    document.getElementById('url').value = data.url;
    document.getElementById('urutan').value = data.urutan;
    document.getElementById('role').value = data.role;
    document.getElementById('menuModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('menuModal').classList.add('hidden');
}
</script>
