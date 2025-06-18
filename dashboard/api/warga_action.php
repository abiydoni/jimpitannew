<?php
// File: warga_action.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include 'db.php';
    
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $stmt = $pdo->prepare("INSERT INTO tb_warga (
            nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw,
            kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $_POST['tgl_lahir'] ?? '', $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan'] ?? '', $_POST['kecamatan'] ?? '', $_POST['kota'] ?? '', $_POST['propinsi'] ?? '', $_POST['negara'] ?? '',
            $_POST['agama'] ?? '', $_POST['status'] ?? '', $_POST['pekerjaan'] ?? '', $_POST['foto'] ?? ''
        ]);
        echo 'success';

    } elseif ($action == 'read') {
        $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY tgl_warga DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($action == 'update') {
        $stmt = $pdo->prepare("UPDATE tb_warga SET
            nama=?, nik=?, hubungan=?, nikk=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?,
            kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, foto=?
            WHERE id_warga = ?");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $_POST['tgl_lahir'] ?? '', $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan'] ?? '', $_POST['kecamatan'] ?? '', $_POST['kota'] ?? '', $_POST['propinsi'] ?? '', $_POST['negara'] ?? '',
            $_POST['agama'] ?? '', $_POST['status'] ?? '', $_POST['pekerjaan'] ?? '', $_POST['foto'] ?? '', $_POST['id_warga'] ?? ''
        ]);
        echo 'updated';

    } elseif ($action == 'delete') {
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