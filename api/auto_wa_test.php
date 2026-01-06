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

// Normalisasi group ID
$groupId = trim((string)$groupId);

// URL WAGateway API (sudah lengkap dari database)
$url = rtrim((string)$gatewayBase, '/');

// Siapkan data untuk WAGateway
// WAGateway menerima 'id' (group ID) atau 'name' (nama group)
$data = [
    'id' => $groupId,
    'message' => $message
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

// Parse response
$response = json_decode($result, true);

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
    error_log("auto_wa_test.php: Gagal kirim. HTTP: $httpCode, Error: $errorMsg, Group ID: $groupId");
} else {
    // Log sukses jika diperlukan
    if ($response && isset($response['status']) && $response['status']) {
        error_log("auto_wa_test.php: Pesan berhasil dikirim ke group ID: $groupId");
    }
}
?>
