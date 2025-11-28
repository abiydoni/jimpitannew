<?php
// Pastikan tidak ada output sebelum header
ob_start();

include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$filePesan   = get_konfigurasi('report2');
$groupId     = get_konfigurasi('group_id1');
$gatewayBase = get_konfigurasi('url_group'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId   = get_konfigurasi('session_id'); // berisi telegram_token bot (contoh: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz)
$gatewayKey  = get_konfigurasi('gateway_key'); // opsional (tidak diperlukan untuk Telegram)

// Validasi token bot
if (empty($sessionId)) {
    error_log('Telegram token tidak ditemukan. Pastikan konfigurasi "session_id" berisi token bot Telegram.');
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: Telegram token tidak ditemukan.";
    exit;
}

// Ambil isi pesan dari file konfigurasi
try {
    if (empty($filePesan) || !file_exists($filePesan)) {
        throw new Exception("File pesan tidak ditemukan: " . $filePesan);
    }
    include $filePesan;
    $message = isset($pesan) ? $pesan : '';
} catch (Exception $e) {
    error_log('Error loading message file: ' . $e->getMessage());
    $message = '';
}

// Jika dipanggil langsung via HTTP (untuk diambil bot), output pesan
if (!isset($_GET['send']) && !isset($_POST['send'])) {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $message;
    exit;
}

// Jika dipanggil untuk auto-send (cron job atau dengan parameter send=1)
if (empty($message)) {
    exit; // tidak ada pesan untuk dikirim
}

// Bangun URL Telegram Bot API
// Jika url_group kosong atau tidak diisi, gunakan default api.telegram.org
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Normalisasi chat_id grup Telegram
$chatId = trim((string)$groupId);
if ($chatId === '') {
    exit; // tidak ada tujuan
}

// Hapus format WhatsApp jika ada (@g.us) - untuk kompatibilitas
$chatId = str_replace('@g.us', '', $chatId);
$chatId = trim($chatId);

// Payload untuk Telegram Bot API - gunakan Markdown
$payload = [
    'chat_id' => $chatId,
    'text'    => $message,
    'parse_mode' => 'Markdown', // Gunakan Markdown untuk format yang benar
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verifikasi SSL untuk keamanan

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Bersihkan output buffer
ob_end_clean();

// Jika dipanggil dengan parameter send, output hasil
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $httpCode === 200,
        'http_code' => $httpCode,
        'error' => $curlError ?: null,
        'response' => $response ? json_decode($response, true) : null
    ]);
}
?>
