<?php
include 'db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'kode') {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tb_warga");
    $count = $stmt->fetchColumn();
    echo 'RT07' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    exit;
}

if ($aksi == 'read') {
    $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
    $no = 1;
    while ($row = $stmt->fetch()) {
        echo "<tr>
            <td class='border px-4 py-2'>{$no}</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['nama']) . "</td>
            <td class='border px-4 py-2'>{$row['nik']}</td>
            <td class='border px-4 py-2'>{$row['jenkel']}</td>
            <td class='border px-4 py-2'>{$row['tpt_lahir']}, {$row['tgl_lahir']}</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['alamat']) . "</td>
            <td class='border px-4 py-2'>{$row['pekerjaan']}</td>
            <td class='border px-4 py-2'>{$row['hp']}</td>
            <td class='border px-4 py-2'>
                <button onclick='editData({$row['id_warga']})' class='text-blue-600'>Edit</button> |
                <button onclick='hapusData({$row['id_warga']})' class='text-red-600'>Hapus</button>
            </td>
        </tr>";
        $no++;
    }
    exit;
}

if ($aksi == 'get') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data);
    exit;
}

if ($aksi == 'save') {
    $data = [
        'kode' => $_POST['kode'] ?? '',
        'nama' => $_POST['nama'] ?? '',
        'nik' => $_POST['nik'] ?? '',
        'hubungan' => $_POST['hubungan'] ?? '',
        'nikk' => $_POST['nikk'] ?? '',
        'jenkel' => $_POST['jenkel'] ?? '',
        'tpt_lahir' => $_POST['tpt_lahir'] ?? '',
        'tgl_lahir' => $_POST['tgl_lahir'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'rt' => $_POST['rt'] ?? 0,
        'rw' => $_POST['rw'] ?? 0,
        'negara' => $_POST['negara'] ?? '',
        'propinsi' => $_POST['propinsi'] ?? '',
        'kota' => $_POST['kota'] ?? '',
        'kecamatan' => $_POST['kecamatan'] ?? '',
        'kelurahan' => $_POST['kelurahan'] ?? '',
        'agama' => $_POST['agama'] ?? '',
        'status' => $_POST['status'] ?? '',
        'pekerjaan' => $_POST['pekerjaan'] ?? '',
        'hp' => $_POST['hp'] ?? '',
    ];

    $id = $_POST['id_warga'] ?? '';

    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE tb_warga SET 
            kode=?, nama=?, nik=?, hubungan=?, nikk=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?,
            negara=?, propinsi=?, kota=?, kecamatan=?, kelurahan=?, agama=?, status=?, pekerjaan=?, hp=? 
            WHERE id_warga=?");
        $stmt->execute([
            $data['kode'], $data['nama'], $data['nik'], $data['hubungan'], $data['nikk'],
            $data['jenkel'], $data['tpt_lahir'], $data['tgl_lahir'], $data['alamat'],
            $data['rt'], $data['rw'], $data['negara'], $data['propinsi'], $data['kota'],
            $data['kecamatan'], $data['kelurahan'], $data['agama'], $data['status'],
            $data['pekerjaan'], $data['hp'], $id
        ]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO tb_warga (
            kode, nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw,
            negara, propinsi, kota, kecamatan, kelurahan, agama, status, pekerjaan, hp
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )");
        $stmt->execute([
            $data['kode'], $data['nama'], $data['nik'], $data['hubungan'], $data['nikk'],
            $data['jenkel'], $data['tpt_lahir'], $data['tgl_lahir'], $data['alamat'],
            $data['rt'], $data['rw'], $data['negara'], $data['propinsi'], $data['kota'],
            $data['kecamatan'], $data['kelurahan'], $data['agama'], $data['status'],
            $data['pekerjaan'], $data['hp']
        ]);
    }

    echo 'success';
    exit;
}

if ($aksi == 'delete') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
    $stmt->execute([$id]);
    echo 'deleted';
    exit;
}

echo 'Invalid Request';
