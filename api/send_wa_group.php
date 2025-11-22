<?php
include 'get_konfigurasi.php';

// Ambil konfigurasi dari database (menggunakan field yang sama)
$gatewayBase = get_konfigurasi('url_group'); // berisi base URL Telegram API (default: https://api.telegram.org)
$sessionId   = get_konfigurasi('session_id'); // berisi telegram_token bot
$gatewayKey  = get_konfigurasi('gateway_key'); // opsional (tidak diperlukan untuk Telegram)

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
$groupList  = $_POST['groupId'] ?? [];
$pesangroup = isset($_POST['message']) ? trim((string)$_POST['message']) : '';

// Validasi
if (empty($groupList) || $pesangroup === '') {
	echo json_encode(['error' => 'Group dan pesan wajib diisi']);
	exit;
}

// Bangun URL Telegram Bot API
// Jika url_group kosong atau tidak diisi, gunakan default api.telegram.org
$telegramApiBase = !empty($gatewayBase) ? rtrim((string)$gatewayBase, '/') : 'https://api.telegram.org';
$apiUrl = $telegramApiBase . '/bot' . $sessionId . '/sendMessage';

$logAll = "";
$successCount = 0;
$errorCount = 0;

foreach ($groupList as $group) {
	$group = trim((string)$group);
	if ($group === '') continue;

	// Normalisasi chat_id grup Telegram
	// Hapus format WhatsApp jika ada (@g.us) - untuk kompatibilitas
	$chatId = str_replace('@g.us', '', $group);
	$chatId = trim($chatId);

	if ($chatId === '') continue;

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
		$successCount++;
	} else {
		$errorCount++;
		// Log error detail untuk debugging
		error_log('Gagal mengirim pesan Telegram ke chat_id: ' . $chatId . ', HTTP Code: ' . $httpCode . ', Response: ' . $response . ', Error: ' . $curlError);
	}

	$logAll .= '[' . date('Y-m-d H:i:s') . "] Group: $chatId | Pesan: $pesangroup | Status: $status ($httpCode)\n";
}

// Simpan log semua
file_put_contents(__DIR__ . '/log-kirim-telegram.txt', $logAll, FILE_APPEND);

// Redirect dengan status
if ($successCount > 0 && $errorCount === 0) {
	header('Location: pesan_group.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
	header('Location: pesan_group.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
	header('Location: pesan_group.php?status=error');
}
exit;
?>