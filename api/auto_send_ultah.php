<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database
$filePesan = get_konfigurasi('report3');
$groupId = get_konfigurasi('group_id3');
$apiUrl = get_konfigurasi('api_url_group');
$sessionId = get_konfigurasi('session_id');

// Ambil isi pesan dari file konfigurasi
include $filePesan;
$message = $pesan;

// Jika pesan kosong atau mengandung pesan tidak ada ultah sesuai ambil_data_ultah.php, hentikan proses
if (empty(trim($message)) || stripos($message, 'kosong') !== false) {
    // Tidak ada yang ulang tahun, proses dihentikan
    exit;
}

$data = http_build_query([
    'groupId[]' => $groupId,
    'message' => $message
]);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'x-session-id: ' . $sessionId
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Opsional: Tampilkan respons
// echo $response;
?>
