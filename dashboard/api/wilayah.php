<?php
// File: wilayah.php - Menggunakan API EMSIFA
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=jimpitan', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    if ($action == 'provinsi') {
        $stmt = $pdo->query("SELECT id, nama as name FROM wilayah_provinsi ORDER BY nama");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        
    } elseif ($action == 'kota') {
        $provinsi_id = $_GET['provinsi_id'] ?? '';
        if (empty($provinsi_id)) {
            throw new Exception('ID Provinsi tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare("SELECT id, nama as name FROM wilayah_kabupaten WHERE provinsi_id = ? ORDER BY nama");
        $stmt->execute([$provinsi_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        
    } elseif ($action == 'kecamatan') {
        $kota_id = $_GET['kota_id'] ?? '';
        if (empty($kota_id)) {
            throw new Exception('ID Kota tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare("SELECT id, nama as name FROM wilayah_kecamatan WHERE kabupaten_id = ? ORDER BY nama");
        $stmt->execute([$kota_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        
    } elseif ($action == 'kelurahan') {
        $kecamatan_id = $_GET['kecamatan_id'] ?? '';
        if (empty($kecamatan_id)) {
            throw new Exception('ID Kecamatan tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare("SELECT id, nama as name FROM wilayah_desa WHERE kecamatan_id = ? ORDER BY nama");
        $stmt->execute([$kecamatan_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        
    } else {
        throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 