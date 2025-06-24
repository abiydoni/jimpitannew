<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);

   include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE tb_profil SET 
        nama = ?, alamat = ?, cp = ?, hp = ?, catatan = ? WHERE kode = ?");
    $stmt->execute([
        $_POST['nama'],
        $_POST['alamat'],
        $_POST['cp'],
        $_POST['hp'],
        $_POST['catatan'],
        $_POST['kode']
    ]);

    $uploadPath = '../api/assets/image/';

    if (!empty($_FILES['logo']['name'])) {
        $logoName = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath . $logoName);
        $pdo->prepare("UPDATE tb_profil SET logo = ? WHERE kode = ?")->execute([$logoName, $_POST['kode']]);
    }

    if (!empty($_FILES['gambar']['name'])) {
        $gambarName = 'gambar_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath . $gambarName);
        $pdo->prepare("UPDATE tb_profil SET gambar = ? WHERE kode = ?")->execute([$gambarName, $_POST['kode']]);
    }

    echo "<script>alert('Profil diperbarui!');location.href='profile.php';</script>";
    exit;
}

include 'header.php';

$profil = $pdo->query("SELECT * FROM tb_profil LIMIT 1")->fetch(PDO::FETCH_ASSOC);

?>

<!-- <div class="max-w-4xl mx-auto bg-white shadow-md rounded p-4 mt-6 text-sm"> -->
<div class="table-data">
    <div class="order">
        <h2 class="text-xl font-semibold mb-4">📝 Edit Profil Perusahaan</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kode" value="<?= htmlspecialchars($profil['kode']) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-medium mb-1">Nama Perusahaan</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($profil['nama']) ?>" class="w-full border px-2 py-1 rounded text-xs" required>
                </div>
                <div>
                    <label class="block font-medium mb-1">Alamat</label>
                    <input type="text" name="alamat" value="<?= htmlspecialchars($profil['alamat']) ?>" class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="block font-medium mb-1">Contact Person</label>
                    <input type="text" name="cp" value="<?= htmlspecialchars($profil['cp']) ?>" class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="block font-medium mb-1">No. HP</label>
                    <input type="text" name="hp" value="<?= htmlspecialchars($profil['hp']) ?>" class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="block font-medium mb-1">Logo (optional)</label>
                    <input type="file" name="logo" class="w-full border px-2 py-1 rounded text-xs">
                    <?php if (!empty($profil['logo'])): ?>
                        <img src="../assets/image/<?= $profil['logo'] ?>" alt="Logo" class="h-10 mt-1">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block font-medium mb-1">Wallpaper / Gambar (optional)</label>
                    <input type="file" name="gambar" class="w-full border px-2 py-1 rounded text-xs">
                    <?php if (!empty($profil['gambar'])): ?>
                        <img src="../assets/image/<?= $profil['gambar'] ?>" alt="Gambar" class="h-10 mt-1">
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium mb-1">Catatan</label>
                    <textarea name="catatan" rows="2" class="w-full border px-2 py-1 rounded text-xs"><?= htmlspecialchars($profil['catatan']) ?></textarea>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 text-xs">
                    💾 Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
