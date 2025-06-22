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
        $id_warga = $_POST['id_warga'] ?? '';
        if (empty($id_warga)) {
            throw new Exception('ID warga tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare('DELETE FROM tb_warga WHERE id_warga = ?');
        $stmt->execute([$id_warga]);
        
        echo 'deleted';
        
    } elseif ($action == 'get_warga_by_nik') {
        $nik = $_POST['nik'] ?? '';
        if (empty($nik)) {
            throw new Exception('NIK tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare('SELECT * FROM tb_warga WHERE nik = ?');
        $stmt->execute([$nik]);
        $warga = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$warga) {
            throw new Exception('Data warga tidak ditemukan');
        }
        
        echo json_encode($warga);
        
    } elseif ($action == 'get_kk_by_nikk') {
        $nikk = $_POST['nikk'] ?? '';
        if (empty($nikk)) {
            throw new Exception('NIKK tidak boleh kosong');
        }
        
        // Ambil data kepala keluarga (yang pertama dengan NIKK tersebut)
        $stmt = $pdo->prepare('SELECT * FROM tb_warga WHERE nikk = ? AND hubungan = "Kepala Keluarga" LIMIT 1');
        $stmt->execute([$nikk]);
        $kepala_keluarga = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$kepala_keluarga) {
            // Jika tidak ada kepala keluarga, ambil data pertama dengan NIKK tersebut
            $stmt = $pdo->prepare('SELECT * FROM tb_warga WHERE nikk = ? LIMIT 1');
            $stmt->execute([$nikk]);
            $kepala_keluarga = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$kepala_keluarga) {
            throw new Exception('Data KK tidak ditemukan');
        }
        
        // Ambil semua anggota keluarga dengan NIKK yang sama
        $stmt = $pdo->prepare('SELECT * FROM tb_warga WHERE nikk = ? ORDER BY 
            CASE hubungan 
                WHEN "Kepala Keluarga" THEN 1
                WHEN "Istri" THEN 2
                WHEN "Anak" THEN 3
                WHEN "Orang Tua" THEN 4
                WHEN "Mertua" THEN 5
                WHEN "Famili Lain" THEN 6
                WHEN "Pembantu" THEN 7
                WHEN "Lainnya" THEN 8
                ELSE 9
            END, nama');
        $stmt->execute([$nikk]);
        $anggota_keluarga = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [
            'kepala_keluarga' => $kepala_keluarga,
            'anggota_keluarga' => $anggota_keluarga,
            'total_anggota' => count($anggota_keluarga)
        ];
        
        echo json_encode($result);

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

    } elseif ($action == 'import_excel') {
        // Import data dari Excel
        $data = isset($_POST['data']) ? json_decode($_POST['data'], true) : [];
        if (!is_array($data) || empty($data)) {
            throw new Exception('Data tidak valid');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                // Validasi data wajib
                if (empty($row['nama']) || empty($row['nik']) || empty($row['hubungan'])) {
                    $errors[] = "Baris " . ($index + 2) . ": Data wajib tidak lengkap";
                    $errorCount++;
                    continue;
                }
                
                // Validasi NIK (16 digit)
                if (!preg_match('/^\d{16}$/', $row['nik'])) {
                    $errors[] = "Baris " . ($index + 2) . ": NIK harus 16 digit angka";
                    $errorCount++;
                    continue;
                }
                
                // Validasi NIK KK (16 digit)
                if (!preg_match('/^\d{16}$/', $row['nik_kk'])) {
                    $errors[] = "Baris " . ($index + 2) . ": NIK KK harus 16 digit angka";
                    $errorCount++;
                    continue;
                }
                
                // Cek apakah NIK sudah ada
                $cekNIK = $pdo->prepare('SELECT COUNT(*) FROM tb_warga WHERE nik = ?');
                $cekNIK->execute([$row['nik']]);
                if ($cekNIK->fetchColumn() > 0) {
                    $errors[] = "Baris " . ($index + 2) . ": NIK sudah terdaftar";
                    $errorCount++;
                    continue;
                }
                
                // Proses tanggal lahir
                $tgl_lahir = '';
                if (!empty($row['tanggal_lahir'])) {
                    $tgl_lahir = processDate($row['tanggal_lahir']);
                    if (!$tgl_lahir) {
                        $errors[] = "Baris " . ($index + 2) . ": Format tanggal lahir tidak valid";
                        $errorCount++;
                        continue;
                    }
                }
                
                // Proses jenis kelamin
                $jenkel = '';
                if (!empty($row['jenis_kelamin'])) {
                    $jenkel = strtoupper(substr($row['jenis_kelamin'], 0, 1));
                    if (!in_array($jenkel, ['L', 'P'])) {
                        $jenkel = '';
                    }
                }
                
                $stmt = $pdo->prepare("INSERT INTO tb_warga (
                    nama, nik, hubungan, nikk, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw,
                    kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, foto, hp
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $row['nama'] ?? '', $row['nik'] ?? '', $row['hubungan'] ?? '', $row['nik_kk'] ?? '', $jenkel,
                    $row['tempat_lahir'] ?? '', $tgl_lahir, $row['alamat'] ?? '', $row['rt'] ?? '', $row['rw'] ?? '',
                    $row['kelurahan'] ?? '', $row['kecamatan'] ?? '', $row['kota'] ?? '', $row['provinsi'] ?? '', $row['negara'] ?? 'Indonesia',
                    $row['agama'] ?? '', $row['status'] ?? '', $row['pekerjaan'] ?? '', '', $row['no_hp'] ?? ''
                ]);
                
                $successCount++;
                
            } catch (Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                $errorCount++;
            }
        }
        
        $result = [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
        
        echo json_encode($result);

    } else {
        echo 'invalid action';
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>