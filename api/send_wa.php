<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$UrlG = get_konfigurasi('url_phone'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId = get_konfigurasi('session_id'); // berisi telegram_token bot

// Validasi token bot
if (empty($sessionId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Telegram token tidak ditemukan. Pastikan konfigurasi "session_id" berisi token bot Telegram.']);
    exit;
}

// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

// Ambil dan sanitasi input
$nomorList = $_POST['phoneNumbers'] ?? [];
$pesan = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validasi
if (empty($nomorList) || !$pesan) {
    echo json_encode(['error' => 'Chat ID dan pesan wajib diisi']);
    exit;
}

// Bangun URL Telegram Bot API
// Jika url_phone kosong atau tidak diisi, gunakan default api.telegram.org
$telegramApiBase = !empty($UrlG) ? rtrim((string)$UrlG, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

$logAll = "";
$successCount = 0;
$errorCount = 0;

foreach ($nomorList as $nomor) {
    // Untuk Telegram, nomor dianggap sebagai chat_id (bisa angka positif untuk private chat)
    $chatId = trim((string)$nomor);
    
    // Hapus karakter non-numerik kecuali tanda minus (untuk grup)
    $chatId = preg_replace('/[^0-9\-]/', '', $chatId);
    
    if (empty($chatId)) continue;

    // Payload untuk Telegram Bot API
    $payload = [
        'chat_id' => $chatId,
        'text'    => $pesan,
        'parse_mode' => 'HTML', // opsional: bisa diganti 'Markdown' atau dihapus
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

    // Logging
    $status = ($httpCode == 200) ? "SUKSES" : "GAGAL";
    if ($status === "SUKSES") {
        $successCount++;
    } else {
        $errorCount++;
        // Log error detail untuk debugging
        error_log('Gagal mengirim pesan Telegram ke chat_id: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
    }

    $logAll .= "[" . date("Y-m-d H:i:s") . "] Chat ID: $chatId | Pesan: $pesan | Status: $status ($httpCode)\n";
}

// Simpan log semua
file_put_contents(__DIR__ . "/log-kirim-telegram.txt", $logAll, FILE_APPEND);

// Redirect dengan status
if ($successCount > 0 && $errorCount == 0) {
    header('Location: pesan.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
    header('Location: pesan.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
    header('Location: pesan.php?status=error');
}
exit;
?>