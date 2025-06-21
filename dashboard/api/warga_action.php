<?php
// File: warga_action.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fungsi untuk memproses tanggal dari berbagai format
function processDate($dateString) {
    if (empty($dateString)) {
        return null;
    }
    
    // Jika sudah dalam format YYYY-MM-DD, return as is
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
        return $dateString;
    }
    
    // Prioritas untuk format DD-MM-YYYY (format yang diinginkan user)
    if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $dateString)) {
        $parts = explode('-', $dateString);
        $day = intval($parts[0]);
        $month = intval($parts[1]);
        $year = intval($parts[2]);
        
        // Validasi tanggal
        if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900 && $year <= 2100) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
    }
    
    // Format lain sebagai fallback
    $formats = [
        'd/m/Y',     // 31/12/2023
        'm/d/Y',     // 12/31/2023
        'Y/m/d',     // 2023/12/31
        'd/m/y',     // 31/12/23
        'm/d/y',     // 12/31/23
        'd-m-y',     // 31-12-23
        'm-d-y'      // 12-31-23
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
    }
    
    // Jika tidak bisa diparse, return null
    return null;
}

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
        
        // Validasi unik NIK
        $cekNIK = $pdo->prepare('SELECT COUNT(*) FROM tb_warga WHERE nik = ?');
        $cekNIK->execute([$_POST['nik']]);
        if ($cekNIK->fetchColumn() > 0) {
            throw new Exception('NIK sudah terdaftar');
        }
        
        // Validasi tanggal lahir
        $original_tgl_lahir = $_POST['tgl_lahir'] ?? '';
        $tgl_lahir = processDate($original_tgl_lahir);
        
        // Debug logging
        error_log("Tanggal lahir - Original: '$original_tgl_lahir', Processed: '$tgl_lahir'");
        
        if ($tgl_lahir && strtotime($tgl_lahir) > time()) {
            throw new Exception('Tanggal lahir tidak boleh di masa depan');
        }
        if ($_POST['tgl_lahir'] && !$tgl_lahir) {
            throw new Exception('Format tanggal lahir tidak valid. Gunakan format DD-MM-YYYY (contoh: 12-05-1992)');
        }

        // Validasi wilayah (nama wilayah)
        if (empty($_POST['propinsi']) || empty($_POST['kota']) || 
            empty($_POST['kecamatan']) || empty($_POST['kelurahan'])) {
            throw new Exception('Data wilayah tidak lengkap');
        }

        $stmt = $pdo->prepare("INSERT INTO tb_warga (
            nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw,
            kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, foto, hp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $tgl_lahir, $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan'] ?? '', $_POST['kecamatan'] ?? '', $_POST['kota'] ?? '', $_POST['propinsi'] ?? '', $_POST['negara'] ?? '',
            $_POST['agama'] ?? '', $_POST['status'] ?? '', $_POST['pekerjaan'] ?? '', $_POST['foto'] ?? '', $_POST['hp'] ?? ''
        ]);
        echo 'success';

    } elseif ($action == 'read') {
        try {
            // Cek apakah tabel tb_warga ada
            $checkTable = $pdo->query("SHOW TABLES LIKE 'tb_warga'");
            if ($checkTable->rowCount() == 0) {
                throw new Exception('Tabel tb_warga tidak ditemukan');
            }
            
            // Perbaikan query - menggunakan id_warga sebagai pengurut
            $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Read action - Found " . count($result) . " records");
            
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error in read action: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }

    } elseif ($action == 'update') {
        // Validasi input
        if (empty($_POST['nama']) || empty($_POST['nik']) || empty($_POST['hubungan'])) {
            throw new Exception('Data wajib tidak boleh kosong');
        }
        
        // Validasi NIK (16 digit)
        if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
            throw new Exception('NIK harus 16 digit angka');
        }
        
        // Validasi unik NIK (kecuali untuk dirinya sendiri)
        $cekNIK = $pdo->prepare('SELECT COUNT(*) FROM tb_warga WHERE nik = ? AND id_warga != ?');
        $cekNIK->execute([$_POST['nik'], $_POST['id_warga']]);
        if ($cekNIK->fetchColumn() > 0) {
            throw new Exception('NIK sudah terdaftar');
        }
        
        // Validasi tanggal lahir
        $original_tgl_lahir = $_POST['tgl_lahir'] ?? '';
        $tgl_lahir = processDate($original_tgl_lahir);
        
        // Debug logging
        error_log("Tanggal lahir - Original: '$original_tgl_lahir', Processed: '$tgl_lahir'");
        
        if ($tgl_lahir && strtotime($tgl_lahir) > time()) {
            throw new Exception('Tanggal lahir tidak boleh di masa depan');
        }
        if ($_POST['tgl_lahir'] && !$tgl_lahir) {
            throw new Exception('Format tanggal lahir tidak valid. Gunakan format DD-MM-YYYY (contoh: 12-05-1992)');
        }

        // Validasi wilayah (nama wilayah)
        if (empty($_POST['propinsi']) || empty($_POST['kota']) || 
            empty($_POST['kecamatan']) || empty($_POST['kelurahan'])) {
            throw new Exception('Data wilayah tidak lengkap');
        }

        $stmt = $pdo->prepare("UPDATE tb_warga SET
            nama=?, nik=?, hubungan=?, nikk=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?,
            kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, foto=?, hp=?
            WHERE id_warga = ?");

        $stmt->execute([
            $_POST['nama'] ?? '', $_POST['nik'] ?? '', $_POST['hubungan'] ?? '', $_POST['nikk'] ?? '', $_POST['jenkel'] ?? '',
            $_POST['tpt_lahir'] ?? '', $tgl_lahir, $_POST['alamat'] ?? '', $_POST['rt'] ?? '', $_POST['rw'] ?? '',
            $_POST['kelurahan'] ?? '', $_POST['kecamatan'] ?? '', $_POST['kota'] ?? '', $_POST['propinsi'] ?? '', $_POST['negara'] ?? '',
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

    } elseif ($action == 'cek_nik') {
        // Cek daftar NIK yang sudah ada di database
        $nikList = isset($_POST['nik_list']) ? json_decode($_POST['nik_list'], true) : [];
        if (!is_array($nikList) || empty($nikList)) {
            echo json_encode([]);
            exit;
        }
        $inQuery = implode(',', array_fill(0, count($nikList), '?'));
        $stmt = $pdo->prepare("SELECT nik, nama FROM tb_warga WHERE nik IN ($inQuery)");
        $stmt->execute($nikList);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        exit;

    } else {
        echo 'invalid action';
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>