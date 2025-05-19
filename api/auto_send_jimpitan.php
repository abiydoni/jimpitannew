<?php
include 'db.php';
include 'ambil_data_jimpitan.php';
$groupId = "6285729705810-1505093181@g.us"; //warga RT.07
$message = $pesan;

$data = http_build_query([
    'groupId[]' => $groupId,
    'message' => $message
]);

$ch = curl_init("https://rt07.appsbee.my.id/api/send_wa_group.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    // 'Content-Type: application/x-www-form-urlencoded'

    // 'Content-Type: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'x-session-id: 91e37fbd895dedf2587d3f506ce1718e'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
?>