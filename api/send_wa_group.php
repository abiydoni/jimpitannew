<?php
// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

// Ambil dan sanitasi input
$groupList = $_POST['groupId'] ?? [];
$pesangroup = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validasi
if (empty($groupList) || !$pesangroup) {
    echo json_encode(['error' => 'Group dan pesan wajib diisi']);
    exit;
}

$url = "https://wa.appsbee.my.id/send-message-group";
// $url = "https://wapi.appsbee.my.id/send-group-message";

$logAll = "";
$successCount = 0;
$errorCount = 0;

foreach ($groupList as $group) {
    $group = trim($group);

    if (!$group) continue;

    $data = [
        'groupId' => $group,
        'message' => $pesangroup
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Iki header sing bener pak
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-session-id: 91e37fbd895dedf2587d3f506ce1718e'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    // ========================

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

    $logAll .= "[" . date("Y-m-d H:i:s") . "] Group: $group | Pesan: $pesangroup | Status: $status ($httpCode)\n";
}

// ==========


// Simpan log semua
file_put_contents("log-kirim-wa.txt", $logAll, FILE_APPEND);

// Redirect dengan status
if ($successCount > 0 && $errorCount == 0) {
    header('Location: pesan_group.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
    header('Location: pesan_group.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
    header('Location: pesan_group.php?status=error');
}
exit;
?>