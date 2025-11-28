<?php
// Pastikan tidak ada output sebelum header
ob_start();

// Log awal untuk debugging cron job
$isCli = php_sapi_name() === 'cli';
if ($isCli) {
    error_log('auto_send_test.php: CRON JOB STARTED at ' . date('Y-m-d H:i:s'));
}

include 'get_konfigurasi.php';

// Ambil konfigurasi dari database
$filePesan   = get_konfigurasi('report3');
$groupId     = get_konfigurasi('group_id2');
$gatewayBase = get_konfigurasi('url_group');
$sessionId   = get_konfigurasi('session_id');

// Log konfigurasi (tanpa menampilkan token lengkap)
if ($isCli) {
    error_log('auto_send_test.php: Config loaded - filePesan: ' . ($filePesan ?: 'NULL') . ', groupId: ' . ($groupId ?: 'NULL') . ', gatewayBase: ' . ($gatewayBase ?: 'NULL') . ', sessionId: ' . ($sessionId ? substr($sessionId, 0, 10) . '...' : 'NULL'));
}

// Validasi token bot
if (empty($sessionId)) {
    ob_end_clean();
    $errorMsg = 'auto_send_test.php: ERROR - Telegram token tidak ditemukan di database (session_id)';
    error_log($errorMsg);
    if ($isCli || isset($_GET['send']) || isset($_POST['send'])) {
        exit(1);
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: Telegram token tidak ditemukan.";
        exit;
    }
}

// Ambil isi pesan dari file konfigurasi
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    try {
        include $filePesan;
        $message = isset($pesan) ? trim($pesan) : '';
        if ($isCli) {
            error_log('auto_send_test.php: Message loaded from file: ' . $filePesan . ' (length: ' . strlen($message) . ' chars)');
        }
    } catch (Exception $e) {
        $errorMsg = 'auto_send_test.php: ERROR loading message file: ' . $e->getMessage();
        error_log($errorMsg);
        if ($isCli) {
            exit(1);
        }
    }
} else {
    $errorMsg = 'auto_send_test.php: ERROR - File pesan tidak ditemukan: ' . ($filePesan ?: 'NULL');
    error_log($errorMsg);
    if ($isCli) {
        exit(1);
    }
}

// Jika dipanggil langsung via HTTP tanpa parameter send, output pesan untuk diambil bot
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    echo $message;
    exit;
}

// Jika pesan kosong, tidak ada yang dikirim
if (empty($message)) {
    ob_end_clean();
    $errorMsg = 'auto_send_test.php: ERROR - Pesan kosong, tidak ada yang dikirim';
    error_log($errorMsg);
    if ($isCli) {
        exit(1);
    }
    exit;
}

// Validasi group ID
$chatId = trim((string)$groupId);
if (empty($chatId)) {
    ob_end_clean();
    $errorMsg = 'auto_send_test.php: ERROR - Group ID tidak ditemukan di database (group_id2)';
    error_log($errorMsg);
    if ($isCli) {
        exit(1);
    }
    exit;
}

if ($isCli) {
    error_log('auto_send_test.php: Chat ID: ' . $chatId);
}

// Hapus format WhatsApp jika ada (@g.us)
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

// Bangun URL Telegram Bot API
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

if ($isCli) {
    error_log('auto_send_test.php: Sending to URL: ' . $telegramApiBase . '/bot[TOKEN]/sendMessage');
    error_log('auto_send_test.php: Chat ID: ' . $chatId . ', Message length: ' . strlen($message));
}

// Payload untuk Telegram Bot API (tanpa parse_mode dulu)
$payload = [
    'chat_id' => $chatId,
    'text'    => $message,
];

// Kirim pesan ke Telegram
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($isCli) {
    error_log('auto_send_test.php: First attempt - HTTP Code: ' . $httpCode . ($curlError ? ', cURL Error: ' . $curlError : ''));
    if ($response) {
        $responseData = json_decode($response, true);
        if ($responseData && isset($responseData['description'])) {
            error_log('auto_send_test.php: Response: ' . $responseData['description']);
        }
    }
}

// Jika error, coba dengan Markdown (mungkin pesan perlu format)
if ($httpCode !== 200) {
    if ($isCli) {
        error_log('auto_send_test.php: Retrying with Markdown parse_mode...');
    }
    $payload['parse_mode'] = 'Markdown';
    $ch2 = curl_init($apiUrl);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 10);
    
    $response = curl_exec($ch2);
    $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch2);
    curl_close($ch2);
    
    if ($isCli) {
        error_log('auto_send_test.php: Second attempt (Markdown) - HTTP Code: ' . $httpCode . ($curlError ? ', cURL Error: ' . $curlError : ''));
    }
}

// Bersihkan output buffer
ob_end_clean();

// Log hasil untuk cron job
if ($isCli || isset($_GET['send']) || isset($_POST['send'])) {
    if ($httpCode === 200) {
        error_log('auto_send_test.php: ✅ SUCCESS - Pesan berhasil dikirim ke Chat ID: ' . $chatId . ' at ' . date('Y-m-d H:i:s'));
    } else {
        $responseData = $response ? json_decode($response, true) : null;
        $errorMsg = $responseData && isset($responseData['description']) ? $responseData['description'] : ($curlError ?: 'Unknown error');
        error_log('auto_send_test.php: ❌ FAILED - HTTP Code: ' . $httpCode . ', Error: ' . $errorMsg . ', Chat ID: ' . $chatId);
        if ($responseData) {
            error_log('auto_send_test.php: Full Response: ' . json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        if ($curlError) {
            error_log('auto_send_test.php: cURL Error: ' . $curlError);
        }
    }
    
    if ($isCli) {
        error_log('auto_send_test.php: CRON JOB FINISHED at ' . date('Y-m-d H:i:s'));
    }
    
    // Jika dipanggil via HTTP dengan parameter send, output JSON
    if (!$isCli) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        $responseData = $response ? json_decode($response, true) : null;
        echo json_encode([
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'chat_id' => $chatId,
            'error' => $httpCode !== 200 ? ($responseData && isset($responseData['description']) ? $responseData['description'] : $curlError) : null,
            'response' => $responseData
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        ob_end_clean();
        // Exit dengan code 0 jika success, 1 jika failed
        exit($httpCode === 200 ? 0 : 1);
    }
}
?>
