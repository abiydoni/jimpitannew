<?php 
if (php_sapi_name() !== 'cli' {

    // session_start();


    include 'db.php';
    include 'ambil_data_jaga.php';

    $groupId = "120363398680818900@g.us";
    $message = $pesan;

    // Siapkan data POST dengan format x-www-form-urlencoded
    $data = http_build_query([
        'groupId[]' => $groupId, // gunakan array agar sesuai dengan PHP $_POST['groupId']
        'message' => $message
    ]);

    $ch = curl_init("https://rt07.appsbee.my.id/api/send-wa-group.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // <-- Tambahkan
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "Pesan berhasil dikirim.";
    } else {
        echo "Gagal mengirim pesan. Respon: $response";
    }
}
?>
