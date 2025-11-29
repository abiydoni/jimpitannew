<?php
// Ambil konfigurasi dari database
include __DIR__ . '/get_konfigurasi.php';

$token = get_konfigurasi('session_id');
$chatId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report3');

// Ambil pesan dari file
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? $pesan : '';
}

// Normalisasi chat_id
$chatId = trim((string)$chatId);
$chatId = is_numeric($chatId) ? (int)$chatId : $chatId;

// Kirim ke Telegram
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);
?>
