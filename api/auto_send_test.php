<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi
$sessionId = get_konfigurasi('session_id');
$groupId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report4');

// Jika tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Validasi
if (empty($sessionId) || empty($groupId)) exit;

// Ambil pesan
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? trim($pesan) : '';
}
if (empty($message)) exit;

// Kirim ke Telegram
$chatId = trim(str_replace('@g.us', '', (string)$groupId));
$apiUrl = 'https://api.telegram.org/bot' . $sessionId . '/sendMessage';

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['chat_id' => $chatId, 'text' => $message]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log
if ($httpCode == 200) {
    error_log('auto_send_test.php: OK');
} else {
    error_log('auto_send_test.php: FAIL - ' . $httpCode);
}
?>
