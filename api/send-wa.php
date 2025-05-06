<?php
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
    echo json_encode(['error' => 'Nomor dan pesan wajib diisi']);
    exit;
}

// URL Web App Google Script
// $url = "https://script.google.com/macros/s/AKfycbxN6_dhycBtaEp2w6M1Je0Uet14KM5C3fadCpeF3-lSbqcUe-lLi534-hdaHNcExl1D/exec";
$url = "https://wa.appsbee.my.id/send-message";

$logAll = "";
$successCount = 0;
$errorCount = 0;

foreach ($nomorList as $nomor) {
    $nomor = filter_var($nomor, FILTER_SANITIZE_NUMBER_INT);

    if (!$nomor) continue;

    $data = [
        'phoneNumber' => $nomor,
        'message' => $pesan
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    //Iki header sing bener pak
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    // ================================

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Logging
    $status = ($httpCode == 200) ? "SUKSES" : "GAGAL";
    if ($status === "SUKSES") {
        $successCount++;
    } else {
        $errorCount++;
    }

    $logAll .= "[" . date("Y-m-d H:i:s") . "] Nomor: $nomor | Pesan: $pesan | Status: $status ($httpCode)\n";
}

// Simpan log semua
file_put_contents("log-kirim-wa.txt", $logAll, FILE_APPEND);

// Redirect dengan status
if ($successCount > 0 && $errorCount == 0) {
    header('Location: pesan.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
    header('Location: pesan.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
    header('Location: kpesan.php?status=error');
}
exit;
?>
