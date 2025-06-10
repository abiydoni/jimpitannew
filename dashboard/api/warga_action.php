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
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
            <td class='border px-4 py-2'>{$no}</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['nama']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['nik']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['jenkel']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['tpt_lahir']) . ", " . htmlspecialchars($row['tgl_lahir']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['alamat']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['pekerjaan']) . "</td>
            <td class='border px-4 py-2'>" . htmlspecialchars($row['hp']) . "</td>
            <td class='border px-4 py-2'>
                <button onclick=\"editData('{$row['id_warga']}')\" class='bg-yellow-500 text-white px-2 py-1 rounded text-xs'>Edit</button>
                <button onclick=\"hapusData('{$row['id_warga']}')\" class='bg-red-600 text-white px-2 py-1 rounded text-xs'>Hapus</button>
            </td>
        </tr>";
        $no++;
    }
    exit;
}

if ($aksi == 'get' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
    $stmt->execute([$_POST['id']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    exit;
}

if ($aksi == 'save') {
    $data = [
        'kode' => $_POST['kode'],
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
        'negara' => $_POST['negara'],
        'propinsi' => $_POST['propinsi'],
        'kota' => $_POST['kota'],
        'kecamatan' => $_POST['kecamatan'],
        'kelurahan' => $_POST['kelurahan'],
        'agama' => $_POST['agama'],
        'status' => $_POST['status'],
        'pekerjaan' => $_POST['pekerjaan'],
        'hp' => $_POST['hp']
    ];

    if (!empty($_POST['id_warga'])) {
        $data['id_warga'] = $_POST['id_warga'];
        $stmt = $pdo->prepare("UPDATE tb_warga SET kode=:kode, nama=:nama, nik=:nik, hubungan=:hubungan, nikk=:nikk, jenkel=:jenkel, tpt_lahir=:tpt_lahir, tgl_lahir=:tgl_lahir, alamat=:alamat, rt=:rt, rw=:rw, negara=:negara, propinsi=:propinsi, kota=:kota, kecamatan=:kecamatan, kelurahan=:kelurahan, agama=:agama, status=:status, pekerjaan=:pekerjaan, hp=:hp WHERE id_warga=:id_warga");
        $stmt->execute($data);
    } else {
        $stmt = $pdo->prepare("INSERT INTO tb_warga (kode, nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw, negara, propinsi, kota, kecamatan, kelurahan, agama, status, pekerjaan, hp) VALUES (:kode, :nama, :nik, :hubungan, :nikk, :jenkel, :tpt_lahir, :tgl_lahir, :alamat, :rt, :rw, :negara, :propinsi, :kota, :kecamatan, :kelurahan, :agama, :status, :pekerjaan, :hp)");
        $stmt->execute($data);
    }
    exit;
}

if ($aksi == 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
    $stmt->execute([$_POST['id']]);
    exit;
}

echo "Aksi tidak valid";
exit;
