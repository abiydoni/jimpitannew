<?php
// Ambil konfigurasi dari database
// include __DIR__ . '/get_konfigurasi.php';
include 'get_konfigurasi.php';

$token = get_konfigurasi('session_id');
$chatId = get_konfigurasi('group_id2');
$gatewayBase = get_konfigurasi('url_group');
$filePesan = get_konfigurasi('report3');

// Ambil pesan dari file (gunakan output buffering karena file langsung output)
$message = '';
if (!empty($filePesan)) {
    // Coba path relatif dulu
    if (!file_exists($filePesan)) {
        // Coba path absolut
        $filePesan = __DIR__ . '/' . $filePesan;
    }
    if (file_exists($filePesan)) {
        // Gunakan output buffering untuk capture output dari file
        ob_start();
        include $filePesan;
        $message = ob_get_clean();
        $message = trim((string)$message);
        
        // Jika tidak ada output, coba ambil variabel $pesan (untuk kompatibilitas)
        if (empty($message) && isset($pesan)) {
            $message = trim((string)$pesan);
        }
    }
}

// Normalisasi chat_id
$chatId = trim((string)$chatId);
$chatId = is_numeric($chatId) ? (int)$chatId : $chatId;

// Bangun URL Telegram API
$telegramApiBase = rtrim((string)$gatewayBase, '/');
$url = "{$telegramApiBase}/bot{$token}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log error jika gagal
if ($httpCode != 200) {
    $error = json_decode($result, true);
    $errorMsg = isset($error['description']) ? $error['description'] : ($curlError ?: 'Unknown');
    error_log("auto_send_test.php: Gagal kirim. HTTP: $httpCode, Error: $errorMsg, Chat ID: $chatId");
}
?>
