<?php
include 'db.php';

$query = "SELECT MONTH(date_trx) as bulan, 
          (SUM(debet) - SUM(kredit)) AS saldo 
          FROM kas_umum 
          GROUP BY MONTH(date_trx)";
$result = $pdo->query($query);

$data = array();
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $data[$row['bulan']] = $row['saldo'];
}

echo json_encode($data);
?>