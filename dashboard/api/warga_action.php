<?php
require 'db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 🔄 Insert atau Update
    if (isset($_FILES['foto']) || $action === 'submit') {
        $required = ['nama','nik','hubungan','nikk','jenkel','tpt_lahir','tgl_lahir','alamat','rt','rw',
                     'kelurahan','kecamatan','kota','propinsi','negara','agama','status','pekerjaan','hp'];
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['status' => 'error', 'message' => "Field $field wajib diisi"]);
                exit;
            }
        }

        $id = $_POST['id_warga'] ?? null;
        $fotoBaru = $_FILES['foto'] ?? null;
        $namaFoto = '';

        if ($fotoBaru && $fotoBaru['name'] != '') {
            $allowedExt = ['jpg','jpeg','png'];
            $ext = strtolower(pathinfo($fotoBaru['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt)) {
                echo json_encode(['status' => 'error', 'message' => 'Format foto tidak didukung']);
                exit;
            }

            if ($fotoBaru['size'] > 2 * 1024 * 1024) {
                echo json_encode(['status' => 'error', 'message' => 'Ukuran foto maksimal 2MB']);
                exit;
            }

            $namaFoto = 'foto_' . time() . '.' . $ext;
            move_uploaded_file($fotoBaru['tmp_name'], 'uploads/' . $namaFoto);
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
        ];

        if ($namaFoto) $data['foto'] = $namaFoto;

        if ($id) {
            // 🔁 UPDATE
            if ($namaFoto) {
                $old = $pdo->prepare("SELECT foto FROM tb_warga WHERE id_warga = ?");
                $old->execute([$id]);
                $oldData = $old->fetch();
                if ($oldData && $oldData['foto'] && file_exists('uploads/' . $oldData['foto'])) {
                    unlink('uploads/' . $oldData['foto']);
                }
            }

            $fields = '';
            foreach ($data as $key => $val) {
                if ($key != 'kode') $fields .= "$key = :$key, ";
            }
            $fields = rtrim($fields, ', ');
            $data['id'] = $id;

            $sql = "UPDATE tb_warga SET $fields WHERE id_warga = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
        } else {
            // ➕ INSERT
            $cols = implode(',', array_keys($data));
            $vals = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO tb_warga ($cols) VALUES ($vals)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        }
        exit;
    }

    // 📄 READ TABEL
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
                    <button class='deleteBtn bg-red-500 text-white px-2 py-1 text-xs' data-id='" . $r['id_warga'] . "'>Hapus</button>
                    <a href='api/cetak_warga.php?id=" . $r['id_warga'] . "' target='_blank' class='bg-gray-600 text-white px-2 py-1 text-xs'>Cetak</a>
                </td>
            </tr>";
        }
        exit;
    }

    // 🔍 GET BY ID
    if ($action == 'get') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit;
    }

    // ❌ DELETE
    if ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT foto FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if ($data && $data['foto'] && file_exists('uploads/' . $data['foto'])) {
            unlink('uploads/' . $data['foto']);
        }

        $del = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
        $del->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        exit;
    }
}
