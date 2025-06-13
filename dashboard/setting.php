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

// Ambil semua konfigurasi
$stmt = $pdo->query("SELECT * FROM tb_konfigurasi");
$konfigurasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['value'] as $nama => $value) {
        $stmt = $pdo->prepare("UPDATE tb_konfigurasi SET value = :value WHERE nama = :nama");
        $stmt->execute([':value' => $value, ':nama' => $nama]);
    }
    echo "<div class='bg-green-100 text-green-800 p-2 mb-4'>Konfigurasi berhasil diperbarui.</div>";
    header("Refresh:1"); // Refresh otomatis
}
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">🛠️ Edit Konfigurasi</h1>
        </div>
        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <?php foreach ($konfigurasi as $item): ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700"><?= htmlspecialchars($item['nama']) ?></label>
                            <input
                                type="text"
                                name="value[<?= htmlspecialchars($item['nama']) ?>]"
                                value="<?= htmlspecialchars($item['value']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"
                                required
                            >
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>