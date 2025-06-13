<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database
$filePesan = get_konfigurasi('report1');
$groupId = get_konfigurasi('group_id2');
$apiUrl = get_konfigurasi('api_url');
$sessionId = get_konfigurasi('session_id');

// Ambil isi pesan dari file konfigurasi
include $filePesan;
$message = $pesan;

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
