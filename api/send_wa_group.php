<?php
// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

// Ambil dan sanitasi input
$groupList = $_POST['groupId'] ?? []; // ✅ TANPA "[]"
$pesangroup = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validasi
if (empty($groupList) || !$pesangroup) {
    echo json_encode(['error' => 'Group dan pesan wajib diisi']);
    exit;
}

$url = "https://wapi.appsbee.my.id/send-group-message";
$sessionId = '91e37fbd895dedf2587d3f506ce1718e';

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "x-session-id: $sessionId"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $status = ($httpCode == 200) ? "SUKSES" : "GAGAL";
    if ($status === "SUKSES") {
        $successCount++;
    } else {
        $errorCount++;
    }

    $logAll .= "[" . date("Y-m-d H:i:s") . "] Group: $group | Status: $status ($httpCode)\n";
    $logAll .= "[DATA] " . json_encode($data) . "\n";
    $logAll .= "[RESPONSE] " . $response . "\n";
    if ($curlError) {
        $logAll .= "[CURL ERROR] $curlError\n";
    }
    $logAll .= str_repeat("-", 60) . "\n";
}

// Simpan log
file_put_contents("log-kirim-wa.txt", $logAll, FILE_APPEND);

// Redirect berdasarkan status
if ($successCount > 0 && $errorCount == 0) {
    header('Location: pesan_group.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
    header('Location: pesan_group.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
    header('Location: pesan_group.php?status=error');
}
exit;
?>
