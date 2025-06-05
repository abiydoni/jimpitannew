<?php
include 'header.php';

$profil = $pdo->query("SELECT * FROM tb_profil LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Handle update
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

    // Optional: handle upload logo
    if (!empty($_FILES['logo']['name'])) {
        $logoName = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['logo']['tmp_name'], 'uploads/' . $logoName);
        $pdo->prepare("UPDATE tb_profil SET logo = ? WHERE kode = ?")->execute([$logoName, $_POST['kode']]);
    }

    // Optional: handle upload gambar
    if (!empty($_FILES['gambar']['name'])) {
        $gambarName = 'gambar_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambarName);
        $pdo->prepare("UPDATE tb_profil SET gambar = ? WHERE kode = ?")->execute([$gambarName, $_POST['kode']]);
    }

    echo "<script>alert('Profil berhasil diperbarui');location.href='profil.php';</script>";
    exit;
}
?>

<div class="max-w-3xl mx-auto bg-white shadow-md rounded p-6 mt-6">
    <h2 class="text-2xl font-semibold mb-4">📝 Profil Perusahaan</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="kode" value="<?= htmlspecialchars($profil['kode']) ?>">

        <div>
            <label class="block font-medium mb-1">Nama Perusahaan</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($profil['nama']) ?>" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="block font-medium mb-1">Alamat</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($profil['alamat']) ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium mb-1">Contact Person</label>
            <input type="text" name="cp" value="<?= htmlspecialchars($profil['cp']) ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium mb-1">No. HP</label>
            <input type="text" name="hp" value="<?= htmlspecialchars($profil['hp']) ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium mb-1">Logo (optional)</label>
            <input type="file" name="logo" class="w-full border px-3 py-2 rounded">
            <?php if (!empty($profil['logo'])): ?>
                <img src="uploads/<?= $profil['logo'] ?>" alt="Logo" class="h-20 mt-2">
            <?php endif; ?>
        </div>

        <div>
            <label class="block font-medium mb-1">Wallpaper / Gambar (optional)</label>
            <input type="file" name="gambar" class="w-full border px-3 py-2 rounded">
            <?php if (!empty($profil['gambar'])): ?>
                <img src="uploads/<?= $profil['gambar'] ?>" alt="Gambar" class="h-20 mt-2">
            <?php endif; ?>
        </div>

        <div>
            <label class="block font-medium mb-1">Catatan</label>
            <textarea name="catatan" rows="4" class="w-full border px-3 py-2 rounded"><?= htmlspecialchars($profil['catatan']) ?></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                💾 Update
            </button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
