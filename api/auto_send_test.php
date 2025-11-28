<?php
ob_start();

include 'get_konfigurasi.php';

// Ambil konfigurasi
$filePesan = get_konfigurasi('report3');
$groupId = get_konfigurasi('group_id2');
$sessionId = get_konfigurasi('session_id');
$gatewayBase = get_konfigurasi('url_group');

// Debug: Log konfigurasi (untuk cron job)
$isCli = php_sapi_name() === 'cli';
if ($isCli) {
    error_log('auto_send_test.php: Config - filePesan=' . ($filePesan ?: 'NULL') . ', groupId=' . ($groupId ?: 'NULL') . ', sessionId=' . ($sessionId ? substr($sessionId, 0, 10) . '...' : 'NULL') . ', gatewayBase=' . ($gatewayBase ?: 'NULL'));
}

// Validasi dasar
if (empty($sessionId)) {
    ob_end_clean();
    $error = 'auto_send_test.php: ERROR - Token bot tidak ditemukan (session_id)';
    error_log($error);
    if (!$isCli && !isset($_GET['send']) && !isset($_POST['send'])) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: Token bot tidak ditemukan.";
    }
    exit;
}

if (empty($groupId)) {
    ob_end_clean();
    $error = 'auto_send_test.php: ERROR - Group ID tidak ditemukan (group_id2)';
    error_log($error);
    if (!$isCli && !isset($_GET['send']) && !isset($_POST['send'])) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: Group ID tidak ditemukan.";
    }
    exit;
}

// Ambil pesan
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? trim($pesan) : '';
    if ($isCli) {
        error_log('auto_send_test.php: Message loaded - length=' . strlen($message) . ' chars');
    }
} else {
    if ($isCli) {
        error_log('auto_send_test.php: ERROR - File pesan tidak ditemukan: ' . ($filePesan ?: 'NULL'));
    }
}

// Jika dipanggil tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

// Jika pesan kosong, tidak kirim
if (empty($message)) {
    ob_end_clean();
    exit;
}

// Bersihkan chat ID
$chatId = trim(str_replace('@g.us', '', (string)$groupId));
// Pastikan chat_id adalah string (Telegram menerima string atau integer)
if (is_numeric($chatId)) {
    $chatId = (string)$chatId;
}

if ($isCli) {
    error_log('auto_send_test.php: Sending - chatId=' . $chatId . ', messageLength=' . strlen($message));
}

// Bangun URL Telegram Bot API dari database
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

$payload = [
    'chat_id' => $chatId,
    'text' => $message
];

if ($isCli) {
    error_log('auto_send_test.php: API URL: ' . $telegramApiBase . '/bot[TOKEN]/sendMessage');
    error_log('auto_send_test.php: Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
}

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($isCli) {
    error_log('auto_send_test.php: Response - HTTP Code: ' . $httpCode);
    if ($curlError) {
        error_log('auto_send_test.php: cURL Error: ' . $curlError);
    }
    if ($response) {
        $responseData = json_decode($response, true);
        if ($responseData) {
            error_log('auto_send_test.php: Response Data: ' . json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            error_log('auto_send_test.php: Response (raw): ' . substr($response, 0, 500));
        }
    }
}

ob_end_clean();

// Log hasil
if ($isCli || isset($_GET['send']) || isset($_POST['send'])) {
    if ($httpCode === 200) {
        error_log('auto_send_test.php: ✅ SUCCESS - Pesan terkirim ke Chat ID: ' . $chatId);
    } else {
        $errorData = $response ? json_decode($response, true) : null;
        $errorMsg = 'Unknown error';
        if ($curlError) {
            $errorMsg = 'cURL Error: ' . $curlError;
        } elseif ($errorData && isset($errorData['description'])) {
            $errorMsg = $errorData['description'];
        }
        error_log('auto_send_test.php: ❌ FAILED - HTTP Code: ' . $httpCode . ', Error: ' . $errorMsg . ', Chat ID: ' . $chatId);
        if ($errorData) {
            error_log('auto_send_test.php: Full Error Response: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
    
    // Output JSON jika via HTTP
    if (!$isCli) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        $errorData = $response ? json_decode($response, true) : null;
        echo json_encode([
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'chat_id' => $chatId,
            'error' => $httpCode !== 200 ? ($errorData && isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null,
            'response' => $errorData
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        ob_end_clean();
    }
}
?>
