<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
include 'api/db.php';

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = '2024-11-26'
");

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <!-- <link rel="manifest" href="manifest.json"> -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="screen-1">
    <H4>Data Scan Jimpitan</H4>
    <div class="table-container" style="font-size: 10px;">
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Nama KK</th>
                    <th style="text-align: center;">Nominal</th>
                    <th style="text-align: center;">Jaga</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($data as $tarif): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo htmlspecialchars($tarif["kk_name"]); ?></td> 
                    <td><?php echo htmlspecialchars($tarif["nominal"]); ?></td>
                    <td><?php echo htmlspecialchars($tarif["collector"]); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol Bulat -->
    <button class="round-button" onclick="window.location.href='index.php'">
        <span>&#8592;</span> <!-- Ikon panah kiri -->
    </button>
</div>
</body>
</html>