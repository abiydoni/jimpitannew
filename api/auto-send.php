<?php
include 'db.php';
include 'ambil_data_jaga.php';

$groupId = "120363398680818900@g.us";
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

// Logging lebih detail
$logMessage = "[" . date("Y-m-d H:i:s") . "] HTTP Code: $httpCode, Response: " . json_encode($response) . "\n";
file_put_contents(_DIR_ . '/../log-cron-test.txt', $logMessage, FILE_APPEND);
?>