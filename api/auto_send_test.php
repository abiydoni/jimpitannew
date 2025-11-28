<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$gatewayBase = get_konfigurasi('url_group'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId   = get_konfigurasi('session_id'); // berisi telegram_token bot
$gatewayKey  = get_konfigurasi('gateway_key'); // opsional (tidak diperlukan untuk Telegram)

// Ambil file pesan dan group ID
$filePesan = get_konfigurasi('report3');
$groupId   = get_konfigurasi('group_id2');

// Validasi token bot
if (empty($sessionId)) {
	if (php_sapi_name() === 'cli' || isset($_GET['send']) || isset($_POST['send'])) {
		error_log('auto_send_test.php: Telegram token tidak ditemukan. Pastikan konfigurasi "session_id" berisi token bot Telegram.');
	} else {
		header('Content-Type: text/plain; charset=utf-8');
		echo "Error: Telegram token tidak ditemukan.";
	}
	exit;
}

// Jika dipanggil tanpa parameter send, output pesan untuk diambil bot
if (!isset($_GET['send']) && !isset($_POST['send']) && php_sapi_name() !== 'cli') {
	$message = '';
	if (!empty($filePesan) && file_exists($filePesan)) {
		include $filePesan;
		$message = isset($pesan) ? trim($pesan) : '';
	}
	header('Content-Type: text/plain; charset=utf-8');
	echo $message;
	exit;
}

// Ambil isi pesan dari file
$pesangroup = '';
if (!empty($filePesan) && file_exists($filePesan)) {
	include $filePesan;
	$pesangroup = isset($pesan) ? trim((string)$pesan) : '';
}

// Validasi pesan
if ($pesangroup === '') {
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

// Payload untuk Telegram Bot API
$payload = [
	'chat_id' => $chatId,
	'text'    => $pesangroup,
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

$status = ($httpCode === 200) ? 'SUKSES' : 'GAGAL';
if ($status === 'SUKSES') {
	error_log('auto_send_test.php: SUCCESS - Pesan berhasil dikirim ke Chat ID: ' . $chatId);
} else {
	// Log error detail untuk debugging
	error_log('auto_send_test.php: Gagal mengirim pesan Telegram ke chat_id: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
}

// Log ke file (opsional, seperti send_wa_group.php)
$logAll = '[' . date('Y-m-d H:i:s') . "] Group: $chatId | Pesan: $pesangroup | Status: $status ($httpCode)\n";
file_put_contents(__DIR__ . '/log-kirim-telegram.txt', $logAll, FILE_APPEND);

// Output JSON jika via HTTP dengan parameter send
if (isset($_GET['send']) || isset($_POST['send'])) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
		'success' => $httpCode === 200,
		'http_code' => $httpCode,
		'chat_id' => $chatId,
		'status' => $status
	]);
}
?>
