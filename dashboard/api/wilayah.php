<?php
// File: wilayah.php - Menggunakan API EMSIFA
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $action = $_GET['action'] ?? '';
    
    if ($action == 'provinsi') {
        $url = 'https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json';
        $response = file_get_contents($url);
        
        if ($response === false) {
            throw new Exception('Gagal mengambil data provinsi');
        }
        
        $data = json_decode($response, true);
        if (!$data) {
            throw new Exception('Data provinsi tidak valid');
        }
        
        echo json_encode($data);
        
    } elseif ($action == 'kota') {
        $provinsi_id = $_GET['provinsi_id'] ?? '';
        if (empty($provinsi_id)) {
            throw new Exception('ID Provinsi harus dipilih');
        }
        
        $url = "https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinsi_id}.json";
        $response = file_get_contents($url);
        
        if ($response === false) {
            throw new Exception('Gagal mengambil data kota');
        }
        
        $data = json_decode($response, true);
        if (!$data) {
            throw new Exception('Data kota tidak valid');
        }
        
        echo json_encode($data);
        
    } elseif ($action == 'kecamatan') {
        $kota_id = $_GET['kota_id'] ?? '';
        if (empty($kota_id)) {
            throw new Exception('ID Kota harus dipilih');
        }
        
        $url = "https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$kota_id}.json";
        $response = file_get_contents($url);
        
        if ($response === false) {
            throw new Exception('Gagal mengambil data kecamatan');
        }
        
        $data = json_decode($response, true);
        if (!$data) {
            throw new Exception('Data kecamatan tidak valid');
        }
        
        echo json_encode($data);
        
    } elseif ($action == 'kelurahan') {
        $kecamatan_id = $_GET['kecamatan_id'] ?? '';
        if (empty($kecamatan_id)) {
            throw new Exception('ID Kecamatan harus dipilih');
        }
        
        $url = "https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$kecamatan_id}.json";
        $response = file_get_contents($url);
        
        if ($response === false) {
            throw new Exception('Gagal mengambil data kelurahan');
        }
        
        $data = json_decode($response, true);
        if (!$data) {
            throw new Exception('Data kelurahan tidak valid');
        }
        
        echo json_encode($data);
        
    } else {
        throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 