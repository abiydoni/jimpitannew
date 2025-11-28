<?php
// Pastikan tidak ada output sebelum header
ob_start();

include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$filePesan   = get_konfigurasi('report1');
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
// Cek jika ada parameter 'send' atau jika ini adalah cron job (CLI)
$isCronJob = php_sapi_name() === 'cli';
$hasSendParam = isset($_GET['send']) || isset($_POST['send']);

if (!$hasSendParam && !$isCronJob) {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $message;
    exit;
}

// Jika dipanggil untuk auto-send (cron job atau dengan parameter send=1)
if (empty(trim($message))) {
    error_log('auto_send_jimpitan.php: Pesan kosong, tidak ada yang dikirim');
    ob_end_clean();
    if ($hasSendParam || $isCronJob) {
        // Jika dipanggil dengan send parameter atau cron, output error
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Pesan kosong']);
    }
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
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // timeout 30 detik

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Jika Markdown error (400 Bad Request), coba tanpa parse_mode
if ($httpCode === 400 && $response) {
    $responseData = json_decode($response, true);
    if (isset($responseData['description']) && 
        (stripos($responseData['description'], 'parse') !== false || 
         stripos($responseData['description'], 'markdown') !== false)) {
        // Coba kirim lagi tanpa parse_mode
        error_log('auto_send_jimpitan.php: Markdown error, mencoba tanpa parse_mode');
        $payloadNoMarkdown = [
            'chat_id' => $chatId,
            'text'    => $message,
        ];
        
        $ch2 = curl_init($apiUrl);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($payloadNoMarkdown));
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch2);
        $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch2);
        curl_close($ch2);
    }
}

// Bersihkan output buffer
ob_end_clean();

// Jika dipanggil dengan parameter send atau cron job, output hasil
if ($hasSendParam || $isCronJob) {
    if (!$isCronJob) {
        header('Content-Type: application/json; charset=utf-8');
    }
    $result = [
        'success' => $httpCode === 200,
        'http_code' => $httpCode,
        'error' => $curlError ?: null,
        'response' => $response ? json_decode($response, true) : null
    ];
    
    if ($isCronJob) {
        // Untuk cron job, log hasil
        if ($httpCode === 200) {
            error_log('auto_send_jimpitan.php: Pesan berhasil dikirim ke grup Telegram (Chat ID: ' . $chatId . ')');
        } else {
            $responseData = $response ? json_decode($response, true) : null;
            $errorMsg = $responseData && isset($responseData['description']) ? $responseData['description'] : ($curlError ?: 'Unknown');
            error_log('auto_send_jimpitan.php: Gagal mengirim pesan. HTTP Code: ' . $httpCode . ', Error: ' . $errorMsg . ', Chat ID: ' . $chatId);
            if ($responseData) {
                error_log('auto_send_jimpitan.php: Response: ' . json_encode($responseData));
            }
        }
    } else {
        echo json_encode($result);
    }
}
?>
