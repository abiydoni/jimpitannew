<?php
// Eksekusi query
$stmt = $pdo->prepare("
    SELECT 
        collector, 
        COUNT(*) AS jumlah_scan 
    FROM report
    GROUP BY collector
    ORDER BY jumlah_scan DESC
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siapkan data untuk grafik
$labels = [];
$data = [];
foreach ($results as $row) {
    $labels[] = $row['collector'];
    $data[] = $row['jumlah_scan'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="max-w-4xl mx-auto mt-8 p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">Rekor Scan Terbanyak</h1>
        <p class="text-sm text-gray-500 mb-4">Per : <span id="tanggal"></span></p>

        <div class="overflow-y-auto max-h-[calc(100vh-150px)] border rounded-md">
            <table class="min-w-full border-collapse text-sm text-gray-700">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="px-4 py-2 text-left">No.</th>
                        <th class="px-4 py-2 text-left">Nama User</th>
                        <th class="px-4 py-2 text-right">Jumlah Scan</th>
                        <th class="px-4 py-2 text-center">Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($results as $row): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $no++; ?></td>
                            <td class="px-4 py-2"><?= $row['collector']; ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($row['jumlah_scan'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-start space-x-4">
                                    <!-- Menampilkan grafik dan jumlah scan berdampingan -->
                                    <canvas id="chart_<?= $no ?>" class="w-32 h-16"></canvas>
                                    <span class="font-bold"><?= number_format($row['jumlah_scan'], 0, ',', '.'); ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="font-bold text-gray-700 text-right mt-4">Total Scan: <?= number_format(array_sum($data), 0, ',', '.'); ?></div>
    </div>

    <script>
        // Loop untuk menampilkan grafik pada setiap baris
        <?php $no = 1; ?>
        <?php foreach ($results as $row): ?>
            const ctx_<?= $no ?> = document.getElementById('chart_<?= $no ?>').getContext('2d');
            new Chart(ctx_<?= $no ?>, {
                type: 'bar',
                data: {
                    labels: [<?= json_encode($row['collector']); ?>],
                    datasets: [{
                        label: 'Jumlah Scan',
                        data: [<?= $row['jumlah_scan']; ?>],
                        backgroundColor: '#4CAF50',
                        borderColor: '#388E3C',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            <?php $no++; ?>
        <?php endforeach; ?>
    </script>
</body>
</html>
