<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$gatewayBase = get_konfigurasi('url_group'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId   = get_konfigurasi('session_id'); // berisi telegram_token bot
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
// Jika url_group kosong atau tidak diisi, gunakan default api.telegram.org
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Normalisasi chat_id grup Telegram
// Hapus format WhatsApp jika ada (@g.us) - untuk kompatibilitas
$chatId = str_replace('@g.us', '', trim((string)$groupId));
$chatId = trim($chatId);

if ($chatId === '') {
    exit;
}

// Konversi chat_id ke integer jika numeric (seperti di telebot)
if (is_numeric($chatId)) {
    $chatIdInt = (int)$chatId;
} else {
    $chatIdInt = $chatId;
}

// Payload untuk Telegram Bot API (sama persis dengan send_wa_group.php)
$payload = [
    'chat_id' => $chatIdInt,
    'text'    => $pesangroup,
    'parse_mode' => 'HTML', // opsional: bisa diganti 'Markdown' atau dihapus
];

$headers = [
    'Content-Type: application/json',
];

// Kirim ke Telegram (sama persis dengan send_wa_group.php)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verifikasi SSL untuk keamanan

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log hasil (sama dengan send_wa_group.php)
$status = ($httpCode === 200) ? 'SUKSES' : 'GAGAL';
if ($status === 'SUKSES') {
    error_log('auto_send_test.php: SUCCESS - Chat ID: ' . $chatIdInt);
} else {
    // Log error detail untuk debugging
    error_log('auto_send_test.php: Gagal mengirim pesan Telegram ke chat_id: ' . $chatIdInt . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
}

// Simpan log ke file (sama dengan send_wa_group.php)
$logAll = '[' . date('Y-m-d H:i:s') . "] Group: $chatIdInt | Pesan: $pesangroup | Status: $status ($httpCode)\n";
file_put_contents(__DIR__ . '/log-kirim-telegram.txt', $logAll, FILE_APPEND);

// Output JSON jika via HTTP dengan parameter send
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($response, true);
    echo json_encode([
        'success' => $httpCode === 200,
        'http_code' => $httpCode,
        'chat_id' => $chatIdInt,
        'chat_id_original' => $chatId,
        'status' => $status,
        'error' => $httpCode !== 200 ? ($errorData && isset($errorData['description']) ? $errorData['description'] : ($curlError ?: 'Unknown')) : null,
        'response' => $errorData
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
