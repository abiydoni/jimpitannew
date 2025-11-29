<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi
$gatewayBase = get_konfigurasi('url_group');
$sessionId = get_konfigurasi('session_id');
$groupId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report3');

// Jika tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        header('Content-Type: text/plain; charset=utf-8');
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Validasi
if (empty($sessionId) || empty($groupId)) {
    exit;
}

// Ambil pesan
$text = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $text = isset($pesan) ? trim((string)$pesan) : '';
}

if (empty($text)) {
    exit;
}

// Normalisasi chat_id (sama seperti telebot dashboard)
$chatId = trim((string)$groupId);
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

if (empty($chatId)) {
    exit;
}

// Bangun URL Telegram Bot API
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Payload sederhana (sama seperti telebot dashboard)
$payload = [
    'chat_id' => $chatId,
    'text' => $text
];

// Kirim ke Telegram
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log sederhana
if ($httpCode == 200) {
    error_log('auto_send_test.php: OK - ' . $chatId);
} else {
    $error = json_decode($result, true);
    error_log('auto_send_test.php: FAIL - ' . $httpCode . ' - ' . $chatId . ' - ' . (isset($error['description']) ? $error['description'] : $curlError));
}

// Output JSON jika via HTTP
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($result, true);
    echo json_encode([
        'ok' => $httpCode == 200,
        'chatId' => $chatId,
        'text' => substr($text, 0, 50) . '...',
        'error' => $httpCode != 200 ? (isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null
    ], JSON_UNESCAPED_UNICODE);
}
?>
