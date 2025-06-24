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
                    <div class="flex items-center gap-2">
                        <?php if (!empty($profil['logo'])): ?>
                            <img id="logoPreview" src="../assets/image/<?= $profil['logo'] ?>" alt="Logo" class="h-10 mt-1 border rounded">
                        <?php else: ?>
                            <img id="logoPreview" src="../assets/image/jimpitan.png" alt="Logo" class="h-10 mt-1 border rounded">
                        <?php endif; ?>
                        <button type="button" id="btnEditLogo" class="bg-gray-200 px-2 py-1 rounded text-xs hover:bg-blue-200 flex items-center"><i class='bx bx-edit'></i> Edit Logo</button>
                    </div>
                </div>
                <div>
                    <label class="block font-medium mb-1">Wallpaper / Gambar (optional)</label>
                    <div class="flex items-center gap-2">
                        <?php if (!empty($profil['gambar'])): ?>
                            <img id="wallpaperPreview" src="../assets/image/<?= $profil['gambar'] ?>" alt="Wallpaper" class="h-10 mt-1 border rounded">
                        <?php else: ?>
                            <img id="wallpaperPreview" src="../assets/image/walqr.jpg" alt="Wallpaper" class="h-10 mt-1 border rounded">
                        <?php endif; ?>
                        <button type="button" id="btnEditWallpaper" class="bg-gray-200 px-2 py-1 rounded text-xs hover:bg-blue-200 flex items-center"><i class='bx bx-edit'></i> Edit Wallpaper</button>
                    </div>
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

<!-- MODAL LOGO -->
<div id="modalLogo" class="modal-overlay hidden">
  <div class="modal-container max-w-xs">
    <div class="flex justify-between items-center mb-2">
      <h3 class="font-semibold text-base">Upload Logo</h3>
      <button id="closeModalLogo" class="text-xl">&times;</button>
    </div>
    <form id="formLogo" enctype="multipart/form-data">
      <input type="hidden" name="kode" value="<?= htmlspecialchars($profil['kode']) ?>">
      <input type="file" name="logo" id="inputLogo" accept="image/*" class="mb-2 w-full">
      <img id="previewLogoImg" src="<?php echo !empty($profil['logo']) ? '../assets/image/'.$profil['logo'] : '../assets/image/jimpitan.png'; ?>" class="h-16 mx-auto mb-2 border rounded" alt="Preview Logo">
      <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded w-full">Upload</button>
    </form>
  </div>
</div>
<!-- MODAL WALLPAPER -->
<div id="modalWallpaper" class="modal-overlay hidden">
  <div class="modal-container max-w-xs">
    <div class="flex justify-between items-center mb-2">
      <h3 class="font-semibold text-base">Upload Wallpaper</h3>
      <button id="closeModalWallpaper" class="text-xl">&times;</button>
    </div>
    <form id="formWallpaper" enctype="multipart/form-data">
      <input type="hidden" name="kode" value="<?= htmlspecialchars($profil['kode']) ?>">
      <input type="file" name="gambar" id="inputWallpaper" accept="image/*" class="mb-2 w-full">
      <img id="previewWallpaperImg" src="<?php echo !empty($profil['gambar']) ? '../assets/image/'.$profil['gambar'] : '../assets/image/walqr.jpg'; ?>" class="h-16 mx-auto mb-2 border rounded" alt="Preview Wallpaper">
      <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded w-full">Upload</button>
    </form>
  </div>
</div>

<script>
// Modal logic
const modalLogo = document.getElementById('modalLogo');
const btnEditLogo = document.getElementById('btnEditLogo');
const closeModalLogo = document.getElementById('closeModalLogo');
const inputLogo = document.getElementById('inputLogo');
const previewLogoImg = document.getElementById('previewLogoImg');
const logoPreview = document.getElementById('logoPreview');

btnEditLogo.onclick = () => { modalLogo.classList.remove('hidden'); };
closeModalLogo.onclick = () => { modalLogo.classList.add('hidden'); };
modalLogo.onclick = e => { if (e.target === modalLogo) modalLogo.classList.add('hidden'); };
inputLogo.onchange = e => {
  if (e.target.files[0]) {
    previewLogoImg.src = URL.createObjectURL(e.target.files[0]);
  }
};

document.getElementById('formLogo').onsubmit = async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const res = await fetch('profile.php?upload=logo', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    logoPreview.src = previewLogoImg.src;
    alert('Logo berhasil diupload!');
    modalLogo.classList.add('hidden');
  } else {
    alert('Gagal upload logo!');
  }
};

// Modal Wallpaper
const modalWallpaper = document.getElementById('modalWallpaper');
const btnEditWallpaper = document.getElementById('btnEditWallpaper');
const closeModalWallpaper = document.getElementById('closeModalWallpaper');
const inputWallpaper = document.getElementById('inputWallpaper');
const previewWallpaperImg = document.getElementById('previewWallpaperImg');
const wallpaperPreview = document.getElementById('wallpaperPreview');

btnEditWallpaper.onclick = () => { modalWallpaper.classList.remove('hidden'); };
closeModalWallpaper.onclick = () => { modalWallpaper.classList.add('hidden'); };
modalWallpaper.onclick = e => { if (e.target === modalWallpaper) modalWallpaper.classList.add('hidden'); };
inputWallpaper.onchange = e => {
  if (e.target.files[0]) {
    previewWallpaperImg.src = URL.createObjectURL(e.target.files[0]);
  }
};

document.getElementById('formWallpaper').onsubmit = async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const res = await fetch('profile.php?upload=wallpaper', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    wallpaperPreview.src = previewWallpaperImg.src;
    alert('Wallpaper berhasil diupload!');
    modalWallpaper.classList.add('hidden');
  } else {
    alert('Gagal upload wallpaper!');
  }
};
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
