<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$gatewayBase = get_konfigurasi('url_group');
$sessionId   = get_konfigurasi('session_id');
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
    if (isset($_GET['send']) || isset($_POST['send'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Telegram token tidak ditemukan (session_id kosong)'], JSON_UNESCAPED_UNICODE);
    }
    error_log('auto_send_test.php: Telegram token tidak ditemukan');
    exit;
}

// Validasi group ID
if (empty($groupId)) {
    if (isset($_GET['send']) || isset($_POST['send'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Group ID tidak ditemukan (group_id2 kosong)'], JSON_UNESCAPED_UNICODE);
    }
    error_log('auto_send_test.php: Group ID tidak ditemukan');
    exit;
}

// Ambil pesan dari file
$pesangroup = '';
$fileExists = false;
$pesanExists = false;

if (empty($filePesan)) {
    if (isset($_GET['send']) || isset($_POST['send'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false, 
            'error' => 'File pesan tidak dikonfigurasi (report4 kosong di database)',
            'debug' => ['filePesan' => null]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    error_log('auto_send_test.php: File pesan tidak dikonfigurasi');
    exit;
}

if (file_exists($filePesan)) {
    $fileExists = true;
    include $filePesan;
    if (isset($pesan)) {
        $pesanExists = true;
        $pesangroup = trim((string)$pesan);
    }
}

// Validasi pesan dengan detail debug
if ($pesangroup === '') {
    if (isset($_GET['send']) || isset($_POST['send'])) {
        header('Content-Type: application/json; charset=utf-8');
        $debugInfo = [
            'filePesan' => $filePesan,
            'fileExists' => $fileExists,
            'pesanExists' => $pesanExists,
            'filePath' => $filePesan ? realpath($filePesan) : null
        ];
        $errorMsg = 'Pesan kosong';
        if (!$fileExists) {
            $errorMsg .= ' - File tidak ditemukan: ' . $filePesan;
        } elseif (!$pesanExists) {
            $errorMsg .= ' - Variabel $pesan tidak ada di file';
        } else {
            $errorMsg .= ' - Variabel $pesan kosong atau hanya whitespace';
        }
        echo json_encode([
            'success' => false, 
            'error' => $errorMsg,
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    error_log('auto_send_test.php: Pesan kosong - file: ' . $filePesan . ', exists: ' . ($fileExists ? 'yes' : 'no') . ', pesan var: ' . ($pesanExists ? 'yes' : 'no'));
    exit;
}

// Bangun URL Telegram Bot API
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

// Normalisasi chat_id
$chatId = trim((string)$groupId);

if ($chatId === '') {
    if (isset($_GET['send']) || isset($_POST['send'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Chat ID kosong setelah trim'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Pastikan chat_id adalah string (Telegram menerima string atau integer)
// Jika numeric, tetap sebagai string untuk konsistensi
if (is_numeric($chatId)) {
    $chatId = (string)$chatId;
}

// Payload untuk Telegram Bot API (sama persis dengan send_wa_group.php)
$payload = [
    'chat_id' => $chatId,
    'text'    => $pesangroup,
    'parse_mode' => 'HTML',
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log hasil dengan detail
$status = ($httpCode === 200) ? 'SUKSES' : 'GAGAL';
if ($status === 'SUKSES') {
    error_log('auto_send_test.php: SUCCESS - Chat ID: ' . $chatId);
} else {
    $errorData = json_decode($response, true);
    $errorMsg = 'Unknown';
    if ($curlError) {
        $errorMsg = 'cURL Error: ' . $curlError;
    } elseif ($errorData && isset($errorData['description'])) {
        $errorMsg = $errorData['description'];
    }
    error_log('auto_send_test.php: FAILED - Chat ID: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Error: ' . $errorMsg);
    if ($response) {
        error_log('auto_send_test.php: Response: ' . $response);
    }
}

// Simpan log ke file
$logAll = '[' . date('Y-m-d H:i:s') . "] Group: $chatId | Status: $status ($httpCode)\n";
file_put_contents(__DIR__ . '/log-kirim-telegram.txt', $logAll, FILE_APPEND);

// Output JSON jika via HTTP dengan parameter send
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    $errorData = json_decode($response, true);
    $errorMsg = null;
    if ($httpCode !== 200) {
        if ($curlError) {
            $errorMsg = 'cURL Error: ' . $curlError;
        } elseif ($errorData && isset($errorData['description'])) {
            $errorMsg = $errorData['description'];
        } else {
            $errorMsg = 'Unknown error (HTTP ' . $httpCode . ')';
        }
    }
    
    // Debug info
    $debugInfo = [
        'chat_id' => $chatId,
        'chat_id_type' => gettype($chatId),
        'api_url' => $telegramApiBase . '/bot[TOKEN]/sendMessage',
        'message_length' => strlen($pesangroup),
        'message_preview' => substr($pesangroup, 0, 50) . '...',
        'payload' => [
            'chat_id' => $chatId,
            'text_length' => strlen($pesangroup),
            'parse_mode' => 'HTML'
        ]
    ];
    
    echo json_encode([
        'success' => $httpCode === 200,
        'http_code' => $httpCode,
        'error' => $errorMsg,
        'response' => $errorData,
        'raw_response' => $response,
        'debug' => $debugInfo
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
