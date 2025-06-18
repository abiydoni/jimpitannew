<?php
include 'api/db.php';

// Simpan atau update
if (isset($_POST['simpan'])) {
    $data = $_POST;
    $id = $data['id_warga'] ?? null;
    $foto = $_FILES['foto']['name'] ?? '';

    if ($foto) {
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $namaFoto = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $namaFoto);
    } else {
        $namaFoto = $_POST['old_foto'] ?? '';
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE tb_warga SET kode=?, nama=?, nik=?, hubungan=?, nikk=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?, kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, foto=? WHERE id_warga=?");
        $stmt->execute([
            $data['kode'], $data['nama'], $data['nik'], $data['hubungan'], $data['nikk'], $data['jenkel'], $data['tpt_lahir'], $data['tgl_lahir'], $data['alamat'], $data['rt'], $data['rw'], $data['kelurahan'], $data['kecamatan'], $data['kota'], $data['propinsi'], $data['negara'], $data['agama'], $data['status'], $data['pekerjaan'], $namaFoto, $id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO tb_warga (kode, nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw, kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, foto) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['kode'], $data['nama'], $data['nik'], $data['hubungan'], $data['nikk'], $data['jenkel'], $data['tpt_lahir'], $data['tgl_lahir'], $data['alamat'], $data['rt'], $data['rw'], $data['kelurahan'], $data['kecamatan'], $data['kota'], $data['propinsi'], $data['negara'], $data['agama'], $data['status'], $data['pekerjaan'], $namaFoto
        ]);
    }
    header('Location: ../warga.php');
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga=?");
    $stmt->execute([$id]);
    header('Location: ../warga.php');
    exit;
}
