<?php
session_start();

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

include 'db.php';

// Query data
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.nominal, report.collector 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variabel untuk menyimpan total nominal dan total scan
$totalNominal = 0;
$totalScan = count($data);

// Tampilkan data sebagai HTML
if ($data) {
    $no = 1; // Inisialisasi nomor urut
    foreach ($data as $row) {
        $totalNominal += $row['nominal']; // Tambahkan nominal ke total
        echo "<tr>
                <td>" . $no++ . "</td> <!-- Nomor Urut -->
                <td>" . htmlspecialchars($row['kk_name']) . "</td>
                <td style='text-align: center;'>" . number_format($row['nominal'], 0, ',', '.') . "</td>
                <td style='text-align: center;'>" . htmlspecialchars($row['collector']) . "</td>
              </tr>";
    }
    // Tampilkan total nominal dan total scan di bawah tabel
    // echo "<tr style='font-weight: bold;'>
    //         <td colspan='2' style='text-align: right;'>Total Yang Disetorkan:</td>
    //         <td style='text-align: center;'>" . number_format($totalNominal, 0, ',', '.') . "</td>
    //         <td></td>
    //       </tr>";
    // echo "<tr style='font-weight: bold;'>
    //         <td colspan='2' style='text-align: right;'>Total Scan:</td>
    //         <td style='text-align: center;'>{$totalScan}</td>
    //         <td></td>
    //       </tr>";
} else {
    // Jika tidak ada data
    echo "<tr><td colspan='4' style='text-align: center;'>Tidak ada data jimpitan hari ini.</td></tr>";
}

echo "<?php if ($data): ?>
    <div class="totals">
        <p><strong>Total Yang Disetorkan:</strong> <?php echo number_format($totalNominal, 0, ',', '.'); ?></p>
        <p><strong>Total Scan:</strong> <?php echo $totalScan; ?></p>
    </div>
<?php endif; ?>
</div>";

?>
