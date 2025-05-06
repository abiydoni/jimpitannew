<?php
session_start();

include 'db.php';
include 'ambil_data_jaga.php';

$groupId = "120363398680818900@g.us";
$message = $pesan; // Jangan di-escape jika ingin kirim pesan asli (tanpa htmlspecialchars)

$url = "https://rt07.appsbee.my.id/api/send-wa-group.php";

$data = [
    'groupId' => [$groupId], // Sesuai dengan format API
    'message' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Untuk pengecekan hasil
if ($httpCode == 200) {
    echo "Pesan berhasil dikirim ke grup.";
} else {
    echo "Gagal mengirim pesan. Status HTTP: $httpCode. Respon: $response";
}
?>
