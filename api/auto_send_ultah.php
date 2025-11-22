<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$filePesan   = get_konfigurasi('report3');
$groupId     = get_konfigurasi('group_id3');
$gatewayBase = get_konfigurasi('url_group'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId   = get_konfigurasi('session_id'); // berisi telegram_token bot (contoh: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz)
$gatewayKey  = get_konfigurasi('gateway_key'); // opsional (tidak diperlukan untuk Telegram)

// Validasi token bot
if (empty($sessionId)) {
    error_log('Telegram token tidak ditemukan. Pastikan konfigurasi "session_id" berisi token bot Telegram.');
    exit;
}

// Ambil isi pesan dari file konfigurasi
include $filePesan;
$message = $pesan;

// Jika pesan kosong atau berisi "kosong", hentikan proses
if (empty(trim($message)) || stripos($message, 'kosong') !== false) {
    exit;
}

// Bangun URL Telegram Bot API
// Jika url_group kosong atau tidak diisi, gunakan default api.telegram.org
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Normalisasi chat_id grup Telegram
$chatId = trim((string)$groupId);
if ($chatId === '') {
    exit; // tidak ada tujuan
}

// Hapus format WhatsApp jika ada (@g.us) - untuk kompatibilitas
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

// Payload untuk Telegram Bot API
$payload = [
    'chat_id' => $chatId,
    'text'    => $message,
    'parse_mode' => 'HTML', // opsional: bisa diganti 'Markdown' atau dihapus
];

$headers = [
    'Content-Type: application/json',
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verifikasi SSL untuk keamanan

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);
?>
