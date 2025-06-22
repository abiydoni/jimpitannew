<?php
// wilayah.php - Proxy untuk EMSIFA API
header('Content-Type: application/json');

// EMSIFA API base URL
$baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api/';

// Ambil action dari parameter
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'provinsi':
            $url = $baseUrl . 'provinces.json';
            break;
            
        case 'kota':
            $provinsi_id = $_GET['provinsi_id'] ?? '';
            if (empty($provinsi_id)) {
                throw new Exception('ID Provinsi diperlukan');
            }
            $url = $baseUrl . "regencies/{$provinsi_id}.json";
            break;
            
        case 'kecamatan':
            $kota_id = $_GET['kota_id'] ?? '';
            if (empty($kota_id)) {
                throw new Exception('ID Kota diperlukan');
            }
            $url = $baseUrl . "districts/{$kota_id}.json";
            break;
            
        case 'kelurahan':
            $kecamatan_id = $_GET['kecamatan_id'] ?? '';
            if (empty($kecamatan_id)) {
                throw new Exception('ID Kecamatan diperlukan');
            }
            $url = $baseUrl . "villages/{$kecamatan_id}.json";
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
    // Ambil data dari EMSIFA API
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception('Gagal mengambil data dari EMSIFA API');
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Response tidak valid JSON');
    }
    
    // Return data
    echo json_encode($data);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 