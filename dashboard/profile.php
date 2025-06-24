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
                    <label class="foto-upload-label">
                        <img id="logoPreview" src="<?php echo !empty($profil['logo']) ? '../assets/image/'.$profil['logo'] : '../assets/image/jimpitan.png'; ?>" class="foto-preview" alt="Logo">
                        <span class="text-xs text-gray-500">Klik gambar untuk ganti logo</span>
                        <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none">
                    </label>
                </div>
                <div>
                    <label class="foto-upload-label">
                        <img id="wallpaperPreview" src="<?php echo !empty($profil['gambar']) ? '../assets/image/'.$profil['gambar'] : '../assets/image/walqr.jpg'; ?>" class="foto-preview" alt="Wallpaper">
                        <span class="text-xs text-gray-500">Klik gambar untuk ganti wallpaper</span>
                        <input type="file" name="gambar" id="wallpaperInput" accept="image/*" style="display:none">
                    </label>
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

<style>
.foto-preview {
  width: 120px;
  height: 120px;
  border-radius: 0.375rem;
  object-fit: cover;
  border: 2px solid #ccc;
  cursor: pointer;
  display: block;
  margin: 0 auto 0.5rem auto;
}
.foto-upload-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
}
.foto-upload-label span {
  margin-top: 4px;
  color: #888;
  font-size: 12px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo
    const logoPreview = document.getElementById('logoPreview');
    const logoInput = document.getElementById('logoInput');
    logoPreview.replaceWith(logoPreview.cloneNode(true));
    const newLogoPreview = document.getElementById('logoPreview');
    newLogoPreview.addEventListener('click', function() { logoInput.click(); });
    logoInput.onchange = function(e) {
        if (e.target.files[0]) {
            newLogoPreview.src = URL.createObjectURL(e.target.files[0]);
        }
    };
    // Wallpaper
    const wallpaperPreview = document.getElementById('wallpaperPreview');
    const wallpaperInput = document.getElementById('wallpaperInput');
    wallpaperPreview.replaceWith(wallpaperPreview.cloneNode(true));
    const newWallpaperPreview = document.getElementById('wallpaperPreview');
    newWallpaperPreview.addEventListener('click', function() { wallpaperInput.click(); });
    wallpaperInput.onchange = function(e) {
        if (e.target.files[0]) {
            newWallpaperPreview.src = URL.createObjectURL(e.target.files[0]);
        }
    };
});
</script>

<?php
// Handle AJAX upload logo/wallpaper
if (isset($_GET['upload']) && ($_GET['upload'] === 'logo' || $_GET['upload'] === 'wallpaper')) {
    $uploadPath = '../api/assets/image/';
    $kode = $_POST['kode'] ?? '';
    if ($_GET['upload'] === 'logo' && !empty($_FILES['logo']['name'])) {
        $logoName = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath . $logoName)) {
            $pdo->prepare("UPDATE tb_profil SET logo = ? WHERE kode = ?")->execute([$logoName, $kode]);
            echo json_encode(['success' => true, 'filename' => $logoName]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
    if ($_GET['upload'] === 'wallpaper' && !empty($_FILES['gambar']['name'])) {
        $gambarName = 'gambar_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath . $gambarName)) {
            $pdo->prepare("UPDATE tb_profil SET gambar = ? WHERE kode = ?")->execute([$gambarName, $kode]);
            echo json_encode(['success' => true, 'filename' => $gambarName]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}
