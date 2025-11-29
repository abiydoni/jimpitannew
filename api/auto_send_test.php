<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi
$sessionId = get_konfigurasi('session_id');
$groupId = get_konfigurasi('group_id2');
$filePesan = get_konfigurasi('report3');

// Jika dipanggil tanpa parameter send, output pesan
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
    if (!empty($filePesan) && file_exists($filePesan)) {
        include $filePesan;
        echo isset($pesan) ? $pesan : '';
    }
    exit;
}

// Validasi minimal
if (empty($sessionId) || empty($groupId)) {
    exit;
}

// Ambil pesan
$message = '';
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    $message = isset($pesan) ? trim($pesan) : '';
}

if (empty($message)) {
    exit;
}

// Bersihkan chat ID
$chatId = trim(str_replace('@g.us', '', (string)$groupId));

// Ambil gateway base dari database (jika ada)
$gatewayBase = get_konfigurasi('url_group');
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';

// Kirim ke Telegram
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log dengan detail
if ($httpCode == 200) {
    error_log('auto_send_test.php: ✅ SUCCESS - Chat ID: ' . $chatId);
} else {
    $errorData = json_decode($result, true);
    $errorMsg = 'Unknown';
    if ($curlError) {
        $errorMsg = 'cURL: ' . $curlError;
    } elseif ($errorData && isset($errorData['description'])) {
        $errorMsg = $errorData['description'];
    }
    error_log('auto_send_test.php: ❌ FAILED - HTTP: ' . $httpCode . ', Chat ID: ' . $chatId . ', Error: ' . $errorMsg);
    if ($result) {
        error_log('auto_send_test.php: Response: ' . $result);
    }
}

// Output JSON jika via HTTP dengan parameter send
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($result, true);
    echo json_encode([
        'success' => $httpCode == 200,
        'http_code' => $httpCode,
        'chat_id' => $chatId,
        'error' => $httpCode != 200 ? ($errorData && isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null,
        'response' => $errorData
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
