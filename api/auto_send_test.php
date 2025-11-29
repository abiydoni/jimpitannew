<?php
// Set working directory
chdir(__DIR__);

// Include
include __DIR__ . '/get_konfigurasi.php';

// Log file untuk cron job
$logFile = __DIR__ . '/../log_wa/log_wa_test.txt';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Ambil konfigurasi
$gatewayBase = get_konfigurasi('url_group');
$sessionId = get_konfigurasi('session_id');
$groupId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report3');

$isCli = php_sapi_name() === 'cli';

// Jika tanpa parameter send, output pesan (HTTP only)
if (!isset($_GET['send']) && !isset($_POST['send']) && !$isCli) {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        header('Content-Type: text/plain; charset=utf-8');
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Validasi
if (empty($sessionId)) {
    $msg = 'ERROR: Telegram token tidak ditemukan (session_id kosong)';
    writeLog($msg);
    error_log($msg);
    exit(1);
}

if (empty($groupId)) {
    $msg = 'ERROR: Group ID tidak ditemukan (group_id2 kosong)';
    writeLog($msg);
    error_log($msg);
    exit(1);
}

// Ambil pesan
$text = '';
if (!empty($filePesan)) {
    if (!file_exists($filePesan)) {
        $filePesan = __DIR__ . '/' . $filePesan;
    }
    if (file_exists($filePesan)) {
        include $filePesan;
        $text = isset($pesan) ? trim((string)$pesan) : '';
    } else {
        $msg = 'ERROR: File pesan tidak ditemukan: ' . get_konfigurasi('report3');
        writeLog($msg);
        error_log($msg);
        exit(1);
    }
}

if (empty($text)) {
    $msg = 'ERROR: Pesan kosong';
    writeLog($msg);
    error_log($msg);
    exit(1);
}

// Normalisasi chat_id
$chatId = trim((string)$groupId);
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

if (empty($chatId)) {
    $msg = 'ERROR: Chat ID kosong setelah normalisasi';
    writeLog($msg);
    error_log($msg);
    exit(1);
}

// Bangun URL
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

writeLog("INFO: Mengirim ke Chat ID: $chatId, Message length: " . strlen($text));
writeLog("INFO: API URL: $telegramApiBase/bot[TOKEN]/sendMessage");

// Payload
$payload = [
    'chat_id' => $chatId,
    'text' => $text
];

// Kirim
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

// Log hasil
if ($httpCode == 200) {
    $msg = "SUCCESS: Pesan terkirim ke Chat ID: $chatId";
    writeLog($msg);
    error_log($msg);
    exit(0);
} else {
    $error = json_decode($result, true);
    $errorMsg = isset($error['description']) ? $error['description'] : ($curlError ?: 'Unknown error');
    $msg = "FAILED: HTTP $httpCode, Chat ID: $chatId, Error: $errorMsg";
    writeLog($msg);
    if ($result) {
        writeLog("Response: $result");
    }
    if ($curlError) {
        writeLog("cURL Error: $curlError");
    }
    error_log($msg);
    exit(1);
}

// Output JSON untuk HTTP
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($result, true);
    echo json_encode([
        'ok' => $httpCode == 200,
        'chatId' => $chatId,
        'error' => $httpCode != 200 ? (isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
