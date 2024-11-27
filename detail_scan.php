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
    WHERE report.jimpitan_date = CURDATE()
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
    <link rel="stylesheet" href="/css/tailwind.css">
</head>
<body class="bg-gray-50 text-gray-800 font-poppins">
<div class="max-w-4xl mx-auto py-6 px-4">
    <h4 class="text-lg font-semibold mb-4">Data Scan Jimpitan</h4>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm text-gray-700 bg-white border-collapse">
            <thead class="bg-gray-200 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">Nama KK</th>
                    <th class="px-4 py-2 text-center">Nominal</th>
                    <th class="px-4 py-2 text-center">Jaga</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">Tidak ada data jimpitan hari ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data as $tarif): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($tarif["kk_name"]); ?></td> 
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($tarif["nominal"]); ?></td>
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($tarif["collector"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <button 
        class="fixed bottom-4 right-4 w-12 h-12 rounded-full bg-blue-500 text-white text-xl flex items-center justify-center shadow-lg hover:bg-blue-600"
        onclick="window.location.href='index.php'">
        &#8592;
    </button>
</div>
</body>
</html>
