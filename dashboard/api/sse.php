<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Koneksi ke database
$servername = "localhost";
$username = "dafm5634_ag";
$password = "Ag7us777__";
$dbname = "dafm5634_funrun";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk mendapatkan data terbaru dari database
function getData($conn) {
    // Query untuk mendapatkan max timestamp dari data yang statusnya 'checked' beserta NAMA_GENG dan BIB_NUMBER
    $sql_max_timestamp = "SELECT NAMA_GENG, BIB_NUMBER, timestamp
                            FROM Funrun 
                            WHERE status = 'checked'
                            ORDER BY timestamp DESC
                            LIMIT 1";
    $result_max_timestamp = $conn->query($sql_max_timestamp);
    
    $max_timestamp_data = ($result_max_timestamp && $result_max_timestamp->num_rows > 0) ? $result_max_timestamp->fetch_assoc() : null;

    $max_timestamp = $max_timestamp_data['timestamp'] ?? null;
    $nama_geng = $max_timestamp_data['NAMA_GENG'] ?? null;
    $bib_number = $max_timestamp_data['BIB_NUMBER'] ?? null;
 
    // Query untuk mengambil fastest_checkin
    $sql_fastest_checkin = "SELECT NAMA_GENG, last_timestamp
                            FROM (
                                SELECT NAMA_GENG, timestamp AS last_timestamp,
                                    ROW_NUMBER() OVER (PARTITION BY NAMA_GENG) AS row_num
                                FROM Funrun
                                WHERE status = 'checked' ORDER BY timestamp ASC
                            ) AS ranked
                            WHERE row_num = 3;";
 
    $result_fastest_checkin = $conn->query($sql_fastest_checkin);
 
    $fastest_checkin = [];
    while ($row = $result_fastest_checkin->fetch_assoc()) {
        $fastest_checkin[] = $row;
    }

    // Query untuk mengambil data dari tabel
    $sql_checked = "SELECT * FROM Funrun WHERE status = 'checked' ORDER BY 'timestamp' ASC";
    $result_checked = $conn->query($sql_checked);

    $checked_data = [];
    while ($row = $result_checked->fetch_assoc()) {
        $checked_data[] = $row;
    }
 
    // Query untuk menghitung jumlah peserta
    $sql_total_peserta = "SELECT COUNT(*) AS total_peserta FROM Funrun";
    $result_total_peserta = $conn->query($sql_total_peserta);
    $total_peserta = ($result_total_peserta && $result_total_peserta->num_rows > 0) ? $result_total_peserta->fetch_assoc()["total_peserta"] : 0;
 
    // Query untuk menghitung jumlah yang checked
    $sql_total_check = "SELECT COUNT(*) AS total_check FROM Funrun WHERE status = 'checked'";
    $result_total_check = $conn->query($sql_total_check);
    $total_check = ($result_total_check && $result_total_check->num_rows > 0) ? $result_total_check->fetch_assoc()["total_check"] : 0;
 
    // Query untuk menghitung jumlah yang unchecked
    $sql_total_uncheck = "SELECT COUNT(*) AS total_uncheck FROM Funrun WHERE status = 'unchecked'";
    $result_total_uncheck = $conn->query($sql_total_uncheck);
    $total_uncheck = ($result_total_uncheck && $result_total_uncheck->num_rows > 0) ? $result_total_uncheck->fetch_assoc()["total_uncheck"] : 0;
 
    return [
        'max_timestamp_data' => [
            'NAMA_GENG' => $nama_geng,
            'BIB_NUMBER' => $bib_number,
            'max_timestamp' => $max_timestamp
        ],
        'checked_data' => $checked_data,
        'fastest_checkin' => $fastest_checkin,
        'total_peserta' => $total_peserta,
        'total_check' => $total_check,
        'total_uncheck' => $total_uncheck
    ];
}

 // Kirim data setiap beberapa detik
while (true) {
    $data = getData($conn);
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush(); // Pastikan output buffer dikirim ke browser
    flush();    // Pastikan output dikirim ke browser

    // Tunggu beberapa detik sebelum mengirim data lagi
    sleep(10); // Sesuaikan interval sesuai kebutuhan
}

// Tutup koneksi (meskipun ini tidak akan pernah dieksekusi dalam loop tak berujung)
$conn->close();
?>