<?php
// File: warga_action.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include 'db.php';
    
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        // Validasi input
        if (empty($_POST['nama']) || empty($_POST['nik']) || empty($_POST['hubungan'])) {
            throw new Exception('Data wajib tidak boleh kosong');
        }
        
        // Validasi NIK (16 digit)
        if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
            throw new Exception('NIK harus 16 digit angka');
        }
        
        // Validasi tanggal lahir
        $tgl_lahir = $_POST['tgl_lahir'] ?? '';
        if ($tgl_lahir && strtotime($tgl_lahir) > time()) {
            throw new Exception('Tanggal lahir tidak boleh di masa depan');
        }

        // Validasi wilayah
        if (empty($_POST['propinsi_nama']) || empty($_POST['kota_nama']) || 
            empty($_POST['kecamatan_nama']) || empty($_POST['kelurahan_nama'])) {
            throw new Exception('Data wilayah tidak lengkap');
        }

        $stmt = $pdo->prepare("INSERT INTO tb_warga (
            nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw,
            kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, foto, hp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $_POST['tgl_lahir'] ?? '', $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan_nama'] ?? '', $_POST['kecamatan_nama'] ?? '', $_POST['kota_nama'] ?? '', $_POST['propinsi_nama'] ?? '', $_POST['negara'] ?? '',
            $_POST['agama'] ?? '', $_POST['status'] ?? '', $_POST['pekerjaan'] ?? '', $_POST['foto'] ?? '', $_POST['hp'] ?? ''
        ]);
        echo 'success';

    } elseif ($action == 'read') {
        // Perbaikan query - menggunakan id_warga sebagai pengurut
        $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($action == 'update') {
        // Validasi input
        if (empty($_POST['nama']) || empty($_POST['nik']) || empty($_POST['hubungan'])) {
            throw new Exception('Data wajib tidak boleh kosong');
        }
        
        // Validasi NIK (16 digit)
        if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
            throw new Exception('NIK harus 16 digit angka');
        }
        
        // Validasi tanggal lahir
        $tgl_lahir = $_POST['tgl_lahir'] ?? '';
        if ($tgl_lahir && strtotime($tgl_lahir) > time()) {
            throw new Exception('Tanggal lahir tidak boleh di masa depan');
        }

        // Validasi wilayah
        if (empty($_POST['propinsi_nama']) || empty($_POST['kota_nama']) || 
            empty($_POST['kecamatan_nama']) || empty($_POST['kelurahan_nama'])) {
            throw new Exception('Data wilayah tidak lengkap');
        }

        $stmt = $pdo->prepare("UPDATE tb_warga SET
            nama=?, nik=?, hubungan=?, nikk=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?,
            kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, foto=?, hp=?
            WHERE id_warga = ?");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $_POST['tgl_lahir'] ?? '', $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan_nama'] ?? '', $_POST['kecamatan_nama'] ?? '', $_POST['kota_nama'] ?? '', $_POST['propinsi_nama'] ?? '', $_POST['negara'] ?? '',
            $_POST['agama'] ?? '', $_POST['status'] ?? '', $_POST['pekerjaan'] ?? '', $_POST['foto'] ?? '', $_POST['hp'] ?? '', $_POST['id_warga'] ?? ''
        ]);
        echo 'updated';

    } elseif ($action == 'delete') {
        if (empty($_POST['id_warga'])) {
            throw new Exception('ID warga tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$_POST['id_warga'] ?? '']);
        echo 'deleted';
    } else {
        echo 'invalid action';
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>