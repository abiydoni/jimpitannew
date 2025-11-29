<?php
// Set working directory ke folder script ini
$scriptDir = dirname(__FILE__);
chdir($scriptDir);

// Include file konfigurasi
include __DIR__ . '/get_konfigurasi.php';

// Ambil konfigurasi
$gatewayBase = get_konfigurasi('url_group');
$sessionId = get_konfigurasi('session_id');
$groupId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report3');

// Jika tanpa parameter send, output pesan (hanya untuk HTTP)
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        header('Content-Type: text/plain; charset=utf-8');
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Untuk cron job (CLI), langsung kirim pesan
$isCli = php_sapi_name() === 'cli';

// Validasi
if (empty($sessionId)) {
    if ($isCli) {
        error_log('auto_send_test.php: Telegram token tidak ditemukan');
    }
    exit(1);
}

if (empty($groupId)) {
    if ($isCli) {
        error_log('auto_send_test.php: Group ID tidak ditemukan');
    }
    exit(1);
}

// Ambil pesan
$text = '';
if (!empty($filePesan)) {
    // Gunakan path absolut jika relative
    if (!file_exists($filePesan) && !empty($filePesan)) {
        $filePesan = __DIR__ . '/' . $filePesan;
    }
    if (file_exists($filePesan)) {
        include $filePesan;
        $text = isset($pesan) ? trim((string)$pesan) : '';
    }
}

if (empty($text)) {
    if ($isCli) {
        error_log('auto_send_test.php: Pesan kosong - file: ' . ($filePesan ?: 'NULL'));
    }
    exit(1);
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

// Log untuk debugging (hanya untuk CLI)
if ($isCli) {
    error_log('auto_send_test.php: Config - sessionId: ' . substr($sessionId, 0, 10) . '..., groupId: ' . $groupId . ', filePesan: ' . $filePesan);
    error_log('auto_send_test.php: Chat ID: ' . $chatId . ', Message length: ' . strlen($text));
    error_log('auto_send_test.php: API URL: ' . $telegramApiBase . '/bot[TOKEN]/sendMessage');
}

// Payload sederhana (sama seperti telebot dashboard)
$payload = [
    'chat_id' => $chatId,
    'text' => $text
];

// Kirim ke Telegram
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log hasil dengan detail lengkap
if ($httpCode == 200) {
    error_log('auto_send_test.php: SUCCESS - Chat ID: ' . $chatId . ' | Message length: ' . strlen($text));
    if ($isCli) {
        exit(0); // Success exit code
    }
} else {
    $error = json_decode($result, true);
    $errorMsg = isset($error['description']) ? $error['description'] : ($curlError ?: 'Unknown error');
    error_log('auto_send_test.php: FAILED - HTTP: ' . $httpCode . ', Chat ID: ' . $chatId . ', Error: ' . $errorMsg);
    if ($result) {
        error_log('auto_send_test.php: Response: ' . $result);
    }
    if ($curlError) {
        error_log('auto_send_test.php: cURL Error: ' . $curlError);
    }
    error_log('auto_send_test.php: API URL: ' . $telegramApiBase . '/bot[TOKEN]/sendMessage');
    error_log('auto_send_test.php: Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
    if ($isCli) {
        exit(1); // Error exit code
    }
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
