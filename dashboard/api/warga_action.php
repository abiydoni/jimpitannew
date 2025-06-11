<?php
require 'db.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid'];

try {
    // TAMBAH / EDIT WARGA
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id         = $_POST['id'] ?? '';
        $nik        = $_POST['nik'] ?? '';
        $nokk       = $_POST['nokk'] ?? '';
        $nama       = $_POST['nama'] ?? '';
        $hubungan   = $_POST['hubungan'] ?? '';
        $jenkel     = $_POST['jenkel'] ?? '';
        $tpt_lahir  = $_POST['tpt_lahir'] ?? '';
        $tgl_lahir  = $_POST['tgl_lahir'] ?? '';
        $alamat     = $_POST['alamat'] ?? '';
        $rt         = $_POST['rt'] ?? '';
        $rw         = $_POST['rw'] ?? '';
        $provinsi   = $_POST['provinsi'] ?? '';
        $kota       = $_POST['kota'] ?? '';
        $kecamatan  = $_POST['kecamatan'] ?? '';
        $kelurahan  = $_POST['kelurahan'] ?? '';
        $agama      = $_POST['agama'] ?? '';
        $status     = $_POST['status'] ?? '';
        $pekerjaan  = $_POST['pekerjaan'] ?? '';
        $hp         = $_POST['hp'] ?? '';
        $foto       = $_FILES['foto'] ?? null;

        // Validasi panjang NIK/NOKK
        if (strlen($nik) !== 16 || strlen($nokk) !== 16) {
            throw new Exception("NIK dan No KK harus 16 digit.");
        }

        // Simpan file foto jika diupload
        $fotoPath = null;
        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                throw new Exception("Format foto harus JPG atau PNG.");
            }

            if ($foto['size'] > 1024 * 1024) {
                throw new Exception("Ukuran foto maksimal 1MB.");
            }

            $fotoName = uniqid() . '.' . $ext;
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fotoPath = $uploadDir . $fotoName;
            move_uploaded_file($foto['tmp_name'], $fotoPath);
        }

        if ($id === '') {
            // INSERT
            $stmt = $pdo->prepare("INSERT INTO warga 
                (nik, nokk, nama, hubungan, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw, provinsi, kota, kecamatan, kelurahan, agama, status, pekerjaan, hp, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nik, $nokk, $nama, $hubungan, $jenkel, $tpt_lahir, $tgl_lahir, $alamat, $rt, $rw,
                $provinsi, $kota, $kecamatan, $kelurahan, $agama, $status, $pekerjaan, $hp, $fotoPath
            ]);
            $response = ['success' => true, 'message' => 'Data warga berhasil ditambahkan.'];
        } else {
            // UPDATE
            $sql = "UPDATE warga SET nik=?, nokk=?, nama=?, hubungan=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?, provinsi=?, kota=?, kecamatan=?, kelurahan=?, agama=?, status=?, pekerjaan=?, hp=?";
            $params = [$nik, $nokk, $nama, $hubungan, $jenkel, $tpt_lahir, $tgl_lahir, $alamat, $rt, $rw, $provinsi, $kota, $kecamatan, $kelurahan, $agama, $status, $pekerjaan, $hp];

            if ($fotoPath) {
                $sql .= ", foto=?";
                $params[] = $fotoPath;
            }

            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $response = ['success' => true, 'message' => 'Data warga berhasil diperbarui.'];
        }

    }

    // HAPUS DATA
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("SELECT foto FROM warga WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row && $row['foto'] && file_exists($row['foto'])) {
            unlink($row['foto']);
        }

        $stmt = $pdo->prepare("DELETE FROM warga WHERE id=?");
        $stmt->execute([$id]);
        $response = ['success' => true, 'message' => 'Data warga berhasil dihapus.'];
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
