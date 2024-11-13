<?php
include 'db.php'; // Pastikan untuk menyertakan koneksi database

if (isset($_GET['month'])) {
    $month = htmlspecialchars($_GET['month']);
    
    // Siapkan dan eksekusi pernyataan SQL
    $stmt = $pdo->prepare("SELECT * FROM kas_umum WHERE DATE_FORMAT(date_trx, '%Y-%m') = :month");
    $stmt->execute(['month' => $month]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buat tampilan untuk mencetak
    if ($data) {
        echo "<h1>Data Kas Umum untuk Bulan: $month</h1>";
        echo "<table border='1'>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Reff</th>
                    <th>Keterangan</th>
                    <th>Debet</th>
                    <th>Kredit</th>
                </tr>";
        foreach ($data as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["coa_code"]) . "</td>
                    <td>" . htmlspecialchars($row["date_trx"]) . "</td>
                    <td>" . htmlspecialchars($row["reff"]) . "</td>
                    <td>" . htmlspecialchars($row["desc_trx"]) . "</td>
                    <td>" . "Rp " . number_format(htmlspecialchars($row["debet"]), 0, ',', '.') . "</td>
                    <td>" . "Rp " . number_format(htmlspecialchars($row["kredit"]), 0, ',', '.') . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>Tidak ada data untuk bulan ini.</h2>";
    }
} else {
    echo "<h2>Bulan tidak ditentukan.</h2>";
}
?>