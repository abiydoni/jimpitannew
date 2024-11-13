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
        echo "<button onclick='window.print()'>Cetak</button>";

        echo "<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #f2f2f2;
                }
                h1 {
                    color: #333;
                }
              </style>";
        echo "<table>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Reff</th>
                    <th>Keterangan</th>
                    <th>Debet</th>
                    <th>Kredit</th>
                </tr>";
                $total_debet = 0;
                $total_kredit = 0;
        
        foreach ($data as $row) {
            $total_debet += htmlspecialchars($row["debet"]);
            $total_kredit += htmlspecialchars($row["kredit"]);

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
        // Tambahkan total debet, kredit, dan saldo
        $saldo = $total_debet - $total_kredit;
        echo "<h3>Total Debet: Rp " . number_format($total_debet, 0, ',', '.') . "</h3>";
        echo "<h3>Total Kredit: Rp " . number_format($total_kredit, 0, ',', '.') . "</h3>";
        echo "<h3>Saldo: Rp " . number_format($saldo, 0, ',', '.') . "</h3>";

    } else {
        echo "<h2>Tidak ada data untuk bulan ini.</h2>";
    }
} else {
    echo "<h2>Bulan tidak ditentukan.</h2>";
}
?>