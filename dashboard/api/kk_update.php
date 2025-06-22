<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['code_id'];
    $kk_name = $_POST['kk_name'];
    $kk_alamat = $_POST['kk_alamat'];
    $kk_hp = $_POST['kk_hp'];
    $kk_foto = $_FILES['kk_foto']['name'];

    // Update data
    if ($kk_foto) {
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['kk_foto']['type'], $allowedTypes)) {
            die('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF');
        }
        
        // Validasi ukuran (max 2MB)
        if ($_FILES['kk_foto']['size'] > 2 * 1024 * 1024) {
            die('Ukuran file terlalu besar. Maksimal 2MB');
        }
        
        // Validasi ukuran minimum (min 10KB)
        if ($_FILES['kk_foto']['size'] < 10 * 1024) {
            die('Ukuran file terlalu kecil. Minimal 10KB');
        }
        
        // Validasi dimensi gambar
        $imageInfo = getimagesize($_FILES['kk_foto']['tmp_name']);
        if ($imageInfo === false) {
            die('File bukan gambar yang valid');
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // Batasi dimensi maksimal (1920x1080)
        if ($width > 1920 || $height > 1080) {
            die('Dimensi gambar terlalu besar. Maksimal 1920x1080 pixel');
        }
        
        // Batasi dimensi minimal (100x100)
        if ($width < 100 || $height < 100) {
            die('Dimensi gambar terlalu kecil. Minimal 100x100 pixel');
        }
        
        move_uploaded_file($_FILES['kk_foto']['tmp_name'], "images/warga/" . $kk_foto);
        $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ?, kk_alamat = ?, kk_hp = ?, kk_foto = ? WHERE code_id = ?");
        $stmt->execute([$kk_name, $kk_alamat, $kk_hp, $kk_foto, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ?, kk_alamat = ?, kk_hp = ? WHERE code_id = ?");
        $stmt->execute([$kk_name, $kk_alamat, $kk_hp, $id]);
    }

    echo "<script>alert('Data berhasil diubah!'); window.location.href='../kk.php';</script>";
}
?>