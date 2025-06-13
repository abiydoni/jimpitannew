<?php
session_start();
include 'header.php';

// Cek login dan hak akses
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}
if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php');
    exit;
}

include 'api/db.php';

// Ambil dan kelompokkan data berdasarkan `group`
$stmt = $pdo->query("SELECT * FROM tb_konfigurasi ORDER BY `group`, nama ASC");
$konfigurasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan array berdasarkan group
$grouped = [];
foreach ($konfigurasi as $item) {
    $grouped[$item['group']][] = $item;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['value'] as $nama => $value) {
        $stmt = $pdo->prepare("UPDATE tb_konfigurasi SET value = :value WHERE nama = :nama");
        $stmt->execute([':value' => $value, ':nama' => $nama]);
    }
    echo "<div class='bg-green-100 text-green-800 p-2 mb-4 rounded'>✅ Konfigurasi berhasil diperbarui.</div>";
    header("Refresh:1");
}
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">🛠️ Edit Konfigurasi Sistem</h1>
        </div>

        <form method="POST">
            <div class="space-y-5 text-xs">
                <?php foreach ($grouped as $group_id => $items): ?>
                    <fieldset class="border border-gray-300 rounded-md p-4">
                        <legend class="text-sm font-semibold text-gray-700 px-2">🗂️ Grup <?= $group_id ?></legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                            <?php foreach ($items as $item): ?>
                                <div class="mb-1">
                                    <label class="block font-medium text-gray-700 mb-0.5">
                                        <?= htmlspecialchars($item['nama']) ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="value[<?= htmlspecialchars($item['nama']) ?>]"
                                        value="<?= htmlspecialchars($item['value']) ?>"
                                        class="w-full rounded border border-gray-300 px-2 py-1 focus:ring focus:ring-blue-200 focus:outline-none text-xs"
                                        required
                                    >
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>

            <div class="mt-4 text-right">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded hover:bg-blue-700 text-xs">
                    💾 Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
