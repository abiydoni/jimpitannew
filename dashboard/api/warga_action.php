<?php
require 'db.php'; // pastikan file ini koneksi dengan PDO sudah tersedia
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fungsi Insert / Update
    if (isset($_FILES['foto'])) {
        // Validasi input dasar
        $required = ['nama', 'nik', 'hubungan', 'nikk', 'jenkel', 'tpt_lahir', 'tgl_lahir', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'agama', 'status', 'pekerjaan', 'hp'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['status' => 'error', 'message' => 'Lengkapi semua data!']);
                exit;
            }
        }

        $id = $_POST['id_warga'] ?? null;
        $foto = $_FILES['foto'];
        $namaFoto = '';

        if ($foto['name'] != '') {
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $namaFoto = 'foto_' . time() . '.' . $ext;
            move_uploaded_file($foto['tmp_name'], 'uploads/' . $namaFoto);
        }

        $data = [
            'kode' => uniqid('W'),
            'nama' => $_POST['nama'],
            'nik' => $_POST['nik'],
            'hubungan' => $_POST['hubungan'],
            'nikk' => $_POST['nikk'],
            'jenkel' => $_POST['jenkel'],
            'tpt_lahir' => $_POST['tpt_lahir'],
            'tgl_lahir' => $_POST['tgl_lahir'],
            'alamat' => $_POST['alamat'],
            'rt' => $_POST['rt'],
            'rw' => $_POST['rw'],
            'kelurahan' => $_POST['kelurahan'],
            'kecamatan' => $_POST['kecamatan'],
            'kota' => $_POST['kota'],
            'propinsi' => $_POST['propinsi'],
            'negara' => $_POST['negara'],
            'agama' => $_POST['agama'],
            'status' => $_POST['status'],
            'pekerjaan' => $_POST['pekerjaan'],
            'hp' => $_POST['hp'],
            'foto' => $namaFoto
        ];

        if ($id) {
            // Update
            $sql = "UPDATE tb_warga SET
                    nama=:nama, nik=:nik, hubungan=:hubungan, nikk=:nikk, jenkel=:jenkel, tpt_lahir=:tpt_lahir, 
                    tgl_lahir=:tgl_lahir, alamat=:alamat, rt=:rt, rw=:rw, kelurahan=:kelurahan, kecamatan=:kecamatan,
                    kota=:kota, propinsi=:propinsi, negara=:negara, agama=:agama, status=:status, pekerjaan=:pekerjaan, hp=:hp" .
                    ($namaFoto ? ", foto=:foto" : "") .
                    " WHERE id_warga=:id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
        } else {
            // Insert
            $cols = implode(',', array_keys($data));
            $vals = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO tb_warga ($cols) VALUES ($vals)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        }
        exit;
    }

    if ($action == 'read') {
        $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $r) {
            echo "<tr>
                <td>" . htmlspecialchars($r['nama']) . "</td>
                <td>" . htmlspecialchars($r['nik']) . "</td>
                <td>" . htmlspecialchars($r['hp']) . "</td>
                <td>" . htmlspecialchars($r['alamat']) . "</td>
                <td>
                    <button class='editBtn bg-blue-500 text-white px-2 py-1 text-xs' data-id='" . $r['id_warga'] . "'>Edit</button>
                    <button class='hapusBtn bg-red-500 text-white px-2 py-1 text-xs' data-id='" . $r['id_warga'] . "'>Hapus</button>
                </td>
            </tr>";
        }

        exit;
    }

    // Ambil Data Warga by ID
    if ($action == 'get') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data);
        exit;
    }

    // Hapus
    if ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        exit;
    }
}
