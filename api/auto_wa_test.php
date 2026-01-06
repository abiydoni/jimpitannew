<?php
// Ambil konfigurasi dari database
// include __DIR__ . '/get_konfigurasi.php';
include 'get_konfigurasi.php';

$groupId = get_konfigurasi('group_id2');
$gatewayBase = get_konfigurasi('url_group');
$filePesan = get_konfigurasi('report3');

// Ambil pesan dari file
$message = '';
if (!empty($filePesan)) {
    // Coba path relatif dulu
    if (!file_exists($filePesan)) {
        // Coba path absolut
        $filePesan = __DIR__ . '/' . $filePesan;
    }
    if (file_exists($filePesan)) {
        include $filePesan;
        // Ambil variabel $pesan yang sudah di-set oleh file
        $message = isset($pesan) ? trim((string)$pesan) : '';
    }
}

// Validasi pesan tidak kosong
if (empty($message)) {
    error_log("auto_wa_test.php: Pesan kosong, tidak dapat mengirim pesan");
    exit;
}

// Validasi group ID tidak kosong
if (empty($groupId)) {
    error_log("auto_wa_test.php: Group ID kosong, tidak dapat mengirim pesan");
    exit;
}

// Validasi URL gateway tidak kosong
if (empty($gatewayBase)) {
    error_log("auto_wa_test.php: URL gateway kosong, tidak dapat mengirim pesan");
    exit;
}

// Normalisasi group ID
$groupId = trim((string)$groupId);

// URL WAGateway API
// Jika URL belum mengandung endpoint, tambahkan /send-group-message
$gatewayBase = rtrim((string)$gatewayBase, '/');
if (strpos($gatewayBase, '/send-group-message') === false) {
    $url = $gatewayBase . '/send-group-message';
} else {
    $url = $gatewayBase;
}

// Siapkan data untuk WAGateway
// WAGateway menerima 'id' (group ID) atau 'name' (nama group)
$data = [
    'id' => $groupId,
    'message' => $message
];

// Log data yang akan dikirim (untuk debugging)
error_log("auto_wa_test.php: Mengirim ke URL: $url, Group ID: $groupId, Message length: " . strlen($message));

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

// Parse response
$response = json_decode($result, true);

// Log detail untuk debugging
error_log("auto_wa_test.php: URL: $url, HTTP Code: $httpCode, Response: " . substr($result, 0, 500));

// Log error jika gagal
if ($httpCode != 200) {
    $errorMsg = 'Unknown error';
    if ($response && isset($response['message'])) {
        $errorMsg = is_array($response['message']) ? json_encode($response['message']) : $response['message'];
    } elseif ($curlError) {
        $errorMsg = $curlError;
    } elseif ($result) {
        $errorMsg = $result;
    }
    error_log("auto_wa_test.php: Gagal kirim. HTTP: $httpCode, Error: $errorMsg, Group ID: $groupId, URL: $url");
} else {
    // Log sukses
    if ($response && isset($response['status']) && $response['status']) {
        error_log("auto_wa_test.php: Pesan berhasil dikirim ke group ID: $groupId");
    } else {
        error_log("auto_wa_test.php: HTTP 200 tapi status false. Response: " . json_encode($response));
    }
}
?>
