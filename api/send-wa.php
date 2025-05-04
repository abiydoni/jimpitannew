<?php
// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

// Ambil dan sanitasi input
$nomor = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_NUMBER_INT);
$pesan = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validasi sederhana
if (!$nomor || !$pesan) {
    echo json_encode(['error' => 'Nomor dan pesan wajib diisi']);
    exit;
}

// URL Web App Google Script
$url = "https://script.google.com/macros/s/AKfycbxN6_dhycBtaEp2w6M1Je0Uet14KM5C3fadCpeF3-lSbqcUe-lLi534-hdaHNcExl1D/exec"; // ganti dengan URL kamu

$data = [
    'phoneNumber' => $nomor,
    'message' => $pesan
];

// Kirim request ke Web App
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // <- ini penting!
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Simpan log ke file
$log = "[" . date("Y-m-d H:i:s") . "] Nomor: $nomor | Pesan: $pesan | Status: $httpCode\n";
file_put_contents("log-kirim-wa.txt", $log, FILE_APPEND);

// Tampilkan hasil ke user
echo "<h3>Respon dari server:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";