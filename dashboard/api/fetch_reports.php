<?php
// Include the database connection
require 'db.php';

header('Content-Type: application/json');

try {
    // Ambil bulan dan tahun dari query parameter
    $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

    // Pastikan bulan dan tahun valid
    if ($month < 1 || $month > 12 || $year < 2000) {
        throw new Exception('Invalid month or year.');
    }

    // Format tanggal untuk query
    $startDate = "$year-$month-01";
    $endDate = date("Y-m-t", strtotime($startDate));

    // Ambil data dari tabel report dengan LEFT JOIN
    $sql = "SELECT 
        m.kk_name,
        SUM(CASE WHEN DAY(jimpitan_date) = 1 THEN nominal END) AS '1',
        SUM(CASE WHEN DAY(jimpitan_date) = 2 THEN nominal END) AS '2',
        SUM(CASE WHEN DAY(jimpitan_date) = 3 THEN nominal END) AS '3',
        SUM(CASE WHEN DAY(jimpitan_date) = 4 THEN nominal END) AS '4',
        SUM(CASE WHEN DAY(jimpitan_date) = 5 THEN nominal END) AS '5',
        SUM(CASE WHEN DAY(jimpitan_date) = 6 THEN nominal END) AS '6',
        SUM(CASE WHEN DAY(jimpitan_date) = 7 THEN nominal END) AS '7',
        SUM(CASE WHEN DAY(jimpitan_date) = 8 THEN nominal END) AS '8',
        SUM(CASE WHEN DAY(jimpitan_date) = 9 THEN nominal END) AS '9',
        SUM(CASE WHEN DAY(jimpitan_date) = 10 THEN nominal END) AS '10',
        SUM(CASE WHEN DAY(jimpitan_date) = 11 THEN nominal END) AS '11',
        SUM(CASE WHEN DAY(jimpitan_date) = 12 THEN nominal END) AS '12',
        SUM(CASE WHEN DAY(jimpitan_date) = 13 THEN nominal END) AS '13',
        SUM(CASE WHEN DAY(jimpitan_date) = 14 THEN nominal END) AS '14',
        SUM(CASE WHEN DAY(jimpitan_date) = 15 THEN nominal END) AS '15',
        SUM(CASE WHEN DAY(jimpitan_date) = 16 THEN nominal END) AS '16',
        SUM(CASE WHEN DAY(jimpitan_date) = 17 THEN nominal END) AS '17',
        SUM(CASE WHEN DAY(jimpitan_date) = 18 THEN nominal END) AS '18',
        SUM(CASE WHEN DAY(jimpitan_date) = 19 THEN nominal END) AS '19',
        SUM(CASE WHEN DAY(jimpitan_date) = 20 THEN nominal END) AS '20',
        SUM(CASE WHEN DAY(jimpitan_date) = 21 THEN nominal END) AS '21',
        SUM(CASE WHEN DAY(jimpitan_date) = 22 THEN nominal END) AS '22',
        SUM(CASE WHEN DAY(jimpitan_date) = 23 THEN nominal END) AS '23',
        SUM(CASE WHEN DAY(jimpitan_date) = 24 THEN nominal END) AS '24',
        SUM(CASE WHEN DAY(jimpitan_date) = 25 THEN nominal END) AS '25',
        SUM(CASE WHEN DAY(jimpitan_date) = 26 THEN nominal END) AS '26',
        SUM(CASE WHEN DAY(jimpitan_date) = 27 THEN nominal END) AS '27',
        SUM(CASE WHEN DAY(jimpitan_date) = 28 THEN nominal END) AS '28',
        SUM(CASE WHEN DAY(jimpitan_date) = 29 THEN nominal END) AS '29',
        SUM(CASE WHEN DAY(jimpitan_date) = 30 THEN nominal END) AS '30',
        SUM(CASE WHEN DAY(jimpitan_date) = 31 THEN nominal END) AS '31',
        SUM(nominal) AS total
    FROM 
        master_kk m 
    LEFT JOIN 
        report r ON r.report_id = m.code_id AND jimpitan_date >= :start_date AND jimpitan_date <= :end_date
    GROUP BY 
        m.code_id

    UNION ALL

    SELECT 
        'Total' AS kk_name,
        SUM(CASE WHEN DAY(jimpitan_date) = 1 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 2 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 3 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 4 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 5 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 6 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 7 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 8 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 9 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 10 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 11 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 12 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 13 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 14 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 15 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 16 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 17 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 18 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 19 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 20 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 21 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 22 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 23 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 24 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 25 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 26 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 27 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 28 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 29 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 30 THEN nominal END),
        SUM(CASE WHEN DAY(jimpitan_date) = 31 THEN nominal END),
        SUM(nominal) AS total
    FROM 
        report r
    WHERE 
        jimpitan_date >= :start_date 
        AND jimpitan_date <= :end_date;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();

    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Format hasil laporan dengan pemisah ribuan
    foreach ($reports as &$report) {
        foreach ($report as $key => $value) {
            if (is_numeric($value)) {
                $report[$key] = number_format($value, 0, ',', '.'); // Format angka
            }
        }
    }

    echo json_encode($reports);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>