<?php
include 'db.php';
include 'ambil_data_jimpitan.php';
$groupId = '120363398680818900@g.us'; // 'Group WA Q'
$message = $pesan;

$data = http_build_query([
    'groupId[]' => $groupId,
    'message' => $message
]);

$ch = curl_init("https://rt07.appsbee.my.id/api/send-wa-group.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Tanggal dan waktu sekarang
$now = date('Y-m-d H:i:s');

// Path log file (pastikan folder `log_wa` punya permission write)
$logFile = __DIR__ . '/log_wa/log_send_wa.txt';

// Buat isi log
if ($curlError) {
    $logStatus = "[$now] FAILED | CURL Error: $curlError\n";
} else {
    $statusText = ($httpCode === 200) ? 'SUCCESS' : 'FAILED';
    $logStatus = "[$now] $statusText | HTTP $httpCode | Response: $response\n";
}

// Simpan ke log file
file_put_contents($logFile, $logStatus, FILE_APPEND);

?>