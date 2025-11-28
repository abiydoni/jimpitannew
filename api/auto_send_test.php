<?php
ob_start();

include 'get_konfigurasi.php';

// Ambil konfigurasi
$filePesan = get_konfigurasi('report3');
$groupId = get_konfigurasi('group_id2');
$sessionId = get_konfigurasi('session_id');
$gatewayBase = get_konfigurasi('url_group');

// Validasi dasar
if (empty($sessionId) || empty($groupId)) {
    ob_end_clean();
    if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
        error_log('auto_send_test.php: Token atau Group ID tidak ditemukan');
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: Konfigurasi tidak lengkap.";
    }
    exit;
}

// Ambil pesan
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? trim($pesan) : '';
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

// Bangun URL Telegram Bot API dari database
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';
$payload = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

ob_end_clean();

// Log hasil
if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
    if ($httpCode === 200) {
        error_log('auto_send_test.php: SUCCESS - Terkirim ke ' . $chatId);
    } else {
        $error = json_decode($response, true);
        error_log('auto_send_test.php: FAILED - Code: ' . $httpCode . ', Error: ' . (isset($error['description']) ? $error['description'] : 'Unknown'));
    }
    
    // Output JSON jika via HTTP
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $httpCode === 200, 'http_code' => $httpCode]);
    }
}
?>
