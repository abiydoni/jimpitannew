<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database
$gatewayBase = get_konfigurasi('url_group');
$sessionId   = get_konfigurasi('session_id');
$groupId     = get_konfigurasi('group_id2');
$filePesan   = get_konfigurasi('report3');

// Jika dipanggil tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        header('Content-Type: text/plain; charset=utf-8');
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Validasi token bot
if (empty($sessionId)) {
    error_log('auto_send_test.php: Telegram token tidak ditemukan');
    exit;
}

// Validasi group ID
if (empty($groupId)) {
    error_log('auto_send_test.php: Group ID tidak ditemukan');
    exit;
}

// Ambil pesan dari file
$pesangroup = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $pesangroup = isset($pesan) ? trim((string)$pesan) : '';
}

// Validasi pesan
if ($pesangroup === '') {
    error_log('auto_send_test.php: Pesan kosong');
    exit;
}

// Bangun URL Telegram Bot API
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Normalisasi chat_id (sama seperti telebot dashboard)
$chatId = trim((string)$groupId);
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

if ($chatId === '') {
    exit;
}

// Payload untuk Telegram Bot API
// Sama seperti telebot dashboard yang tidak menggunakan parse_mode
$payload = [
    'chat_id' => $chatId,
    'text'    => $pesangroup,
];

$headers = [
    'Content-Type: application/json',
];

// Kirim ke Telegram (sama seperti telebot)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log hasil
$status = ($httpCode === 200) ? 'SUKSES' : 'GAGAL';
if ($status === 'SUKSES') {
    error_log('auto_send_test.php: SUCCESS - Chat ID: ' . $chatId);
} else {
    error_log('auto_send_test.php: Gagal mengirim pesan Telegram ke chat_id: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
}

// Simpan log ke file
$logAll = '[' . date('Y-m-d H:i:s') . "] Group: $chatId | Pesan: $pesangroup | Status: $status ($httpCode)\n";
file_put_contents(__DIR__ . '/log-kirim-telegram.txt', $logAll, FILE_APPEND);

// Output JSON jika via HTTP dengan parameter send
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($response, true);
    echo json_encode([
        'success' => $httpCode === 200,
        'http_code' => $httpCode,
        'chat_id' => $chatId,
        'status' => $status,
        'error' => $httpCode !== 200 ? ($errorData && isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null,
        'response' => $errorData
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
