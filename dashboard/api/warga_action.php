<?php
// warga_action.php
include 'db.php'; // Koneksi database
// Pastikan PDO diatur untuk melempar exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$aksi = $_POST['aksi'] ?? '';

header('Content-Type: application/json'); // Penting: Atur header untuk JSON

if ($aksi == 'kode') {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM tb_warga");
        $count = $stmt->fetchColumn();
        $newCode = 'RT07' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
        echo json_encode(['status' => 'success', 'kode' => $newCode]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat kode warga: ' . $e->getMessage()]);
    }
    exit;
}

// Aksi baru untuk DataTables server-side processing
if ($aksi == 'read_datatable') {
    $draw = $_POST['draw'];
    $start = $_POST['start'];
    $length = $_POST['length'];
    $searchValue = $_POST['search']['value'];
    $orderColumnIndex = $_POST['order'][0]['column'];
    $orderDir = $_POST['order'][0]['dir'];
    $columns = $_POST['columns'];
    $orderColumnName = $columns[$orderColumnIndex]['data'];

    $totalRecords = $pdo->query("SELECT COUNT(*) FROM tb_warga")->fetchColumn();

    $query = "SELECT * FROM tb_warga ";
    $where = [];
    $params = [];

    if (!empty($searchValue)) {
        $where[] = "(nama LIKE :search OR nik LIKE :search OR alamat LIKE :search OR pekerjaan LIKE :search OR hp LIKE :search)";
        $params[':search'] = '%' . $searchValue . '%';
    }

    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    $orderableColumns = [
        'nama' => 'nama',
        'nik' => 'nik',
        'jenkel' => 'jenkel',
        'tpt_lahir' => 'tpt_lahir', // Asumsi kolom ini bisa diurutkan
        'alamat' => 'alamat',
        'pekerjaan' => 'pekerjaan',
        'hp' => 'hp'
    ];

    if (isset($orderableColumns[$orderColumnName])) {
        $query .= " ORDER BY " . $orderableColumns[$orderColumnName] . " " . strtoupper($orderDir);
    } else {
        $query .= " ORDER BY id_warga DESC"; // Default order
    }
    
    $query .= " LIMIT :start, :length";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        if (!empty($searchValue)) {
            $stmt->bindParam(':search', $params[':search'], PDO::PARAM_STR);
        }
        $stmt->execute();
        $filteredRecords = $stmt->rowCount(); // Hanya hitung baris yang difilter saat ini, untuk total filtered harus query terpisah jika pagination aktif

        $data = [];
        $no = $start + 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                'no' => $no++,
                'nama' => htmlspecialchars($row['nama']),
                'nik' => htmlspecialchars($row['nik']),
                'jenkel' => htmlspecialchars($row['jenkel']),
                'ttl' => htmlspecialchars($row['tpt_lahir'] . ", " . $row['tgl_lahir']),
                'alamat' => htmlspecialchars($row['alamat']),
                'pekerjaan' => htmlspecialchars($row['pekerjaan']),
                'hp' => htmlspecialchars($row['hp']),
                'aksi' => "
                    <button onclick=\"editData(" . $row['id_warga'] . ")\" class='text-blue-600 hover:text-blue-400 font-bold py-1 px-1'><i class='bx bx-edit'></i></button>
                    <button onclick=\"hapusData(" . $row['id_warga'] . ")\" class='text-red-600 hover:text-red-400 font-bold py-1 px-1'><i class='bx bx-trash'></i></button>
                "
            ];
        }

        // Hitung total filtered records secara terpisah jika ada search/filter
        $totalFiltered = $totalRecords; // Default
        if (!empty($searchValue)) {
             $stmtFiltered = $pdo->prepare("SELECT COUNT(*) FROM tb_warga WHERE " . implode(" AND ", $where));
             $stmtFiltered->bindParam(':search', $params[':search'], PDO::PARAM_STR);
             $stmtFiltered->execute();
             $totalFiltered = $stmtFiltered->fetchColumn();
        }

        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);

    } catch (PDOException $e) {
        echo json_encode(['draw' => intval($draw), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => 'Gagal memuat data: ' . $e->getMessage()]);
    }
    exit;
}

if ($aksi == 'save') {
    $data = $_POST;
    unset($data['aksi']);

    try {
        if (!empty($data['id_warga'])) {
            // Update
            $id = $data['id_warga'];
            unset($data['id_warga']);
            $sql = "UPDATE tb_warga SET ";
            $params = [];
            foreach ($data as $key => $val) {
                $sql .= "$key = :$key, ";
                $params[":$key"] = $val;
            }
            $sql = rtrim($sql, ', ') . " WHERE id_warga = :id";
            $params[':id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil diupdate.']);
        } else {
            // Insert
            unset($data['id_warga']); // Pastikan id_warga tidak masuk ke kolom insert
            $cols = implode(", ", array_keys($data));
            $place = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO tb_warga ($cols) VALUES ($place)");
            foreach ($data as $key => $val) {
                $stmt->bindValue(":$key", $val);
            }
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil ditambahkan.']);
        }
    } catch (PDOException $e) {
        // Tangani error database
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data warga: ' . $e->getMessage()]);
    }
    exit;
} elseif ($aksi == 'get') {
    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data: ' . $e->getMessage()]);
    }
    exit;
} elseif ($aksi == 'delete') {
    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount()) {
            echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau gagal dihapus.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
    }
    exit;
}
?>