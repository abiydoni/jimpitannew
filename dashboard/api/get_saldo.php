<?php
include 'db.php';

// Ambil tahun berjalan
$tahun = date('Y');

// Inisialisasi array bulan
$bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$data = array_fill(0, 12, 0);

// Query: Total nominal per bulan di tahun berjalan
$query = "
    SELECT 
        MONTH(jimpitan_date) AS bulan,
        SUM(nominal) AS total
    FROM report
    WHERE YEAR(jimpitan_date) = '$tahun'
    GROUP BY bulan
";
$result = mysqli_query($conn, $query);

// Masukkan data ke array berdasarkan bulan (index 0-11)
while ($row = mysqli_fetch_assoc($result)) {
    $index = $row['bulan'] - 1; // Karena array dimulai dari 0
    $data[$index] = (int)$row['total'];
}

// Siapkan data untuk Chart.js
$output = [
    'labels' => $bulanLabels,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($output);
