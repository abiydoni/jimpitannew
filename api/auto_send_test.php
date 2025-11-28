<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database
$filePesan   = get_konfigurasi('report3');
$groupId     = get_konfigurasi('group_id2');
$gatewayBase = get_konfigurasi('url_group');
$sessionId   = get_konfigurasi('session_id');

// Validasi token bot
if (empty($sessionId)) {
    if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
        error_log('auto_send_test.php: Telegram token tidak ditemukan');
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: Telegram token tidak ditemukan.";
    }
    exit;
}

// Ambil isi pesan dari file konfigurasi
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? trim($pesan) : '';
}

// Jika dipanggil langsung via HTTP tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

// Jika pesan kosong, tidak ada yang dikirim
if (empty($message)) {
    if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
        error_log('auto_send_test.php: Pesan kosong, tidak ada yang dikirim');
    }
    exit;
}

// Validasi group ID
if (empty($groupId)) {
    if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
        error_log('auto_send_test.php: Group ID tidak ditemukan');
    }
    exit;
}

// Normalisasi chat_id grup Telegram
$chatId = str_replace('@g.us', '', trim((string)$groupId));
$chatId = trim($chatId);

if ($chatId === '') {
    exit;
}

// Bangun URL Telegram Bot API
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Payload untuk Telegram Bot API (mengikuti send_wa_group.php)
$payload = [
    'chat_id' => $chatId,
    'text'    => $message,
    'parse_mode' => 'HTML', // Menggunakan HTML seperti send_wa_group.php
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log hasil
if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
    if ($httpCode === 200) {
        error_log('auto_send_test.php: SUCCESS - Pesan berhasil dikirim ke Chat ID: ' . $chatId);
    } else {
        error_log('auto_send_test.php: FAILED - Chat ID: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
    }
    
    // Output JSON jika via HTTP
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'chat_id' => $chatId
        ]);
    }
}
?>
