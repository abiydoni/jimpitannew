<?php
// Hardcode untuk testing
$token = '8582107388:AAHQtI53tspPtZZvj_eHRPKxox8QYqKEl5Y';
$chatId = 8532362380;
$message = "🧪 Test Message\n\n" . date('Y-m-d H:i:s');

// Ambil pesan dari file jika ada
include __DIR__ . '/get_konfigurasi.php';
$filePesan = get_konfigurasi('report3');
if (!empty($filePesan) && file_exists($filePesan)) {
    include $filePesan;
    if (isset($pesan) && !empty(trim($pesan))) {
        $message = $pesan;
    }
}

// Jika HTTP tanpa parameter send, output pesan saja
$isCli = php_sapi_name() === 'cli';
if (!isset($_GET['send']) && !isset($_POST['send']) && !$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

// Kirim ke Telegram
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log untuk cron job
if ($isCli) {
    $logFile = __DIR__ . '/../log_wa/log_wa_test.txt';
    $log = date('Y-m-d H:i:s') . " - HTTP: $httpCode - " . ($httpCode == 200 ? 'SUCCESS' : 'FAILED') . "\n";
    if ($httpCode != 200) {
        $log .= "Response: $result\n";
    }
    file_put_contents($logFile, $log, FILE_APPEND);
}

// Output JSON jika via HTTP
if (isset($_GET['send']) || isset($_POST['send'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $httpCode == 200, 'http_code' => $httpCode]);
}
?>
