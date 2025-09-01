<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
	header('Location: ../login.php');
	exit;
}

include 'db.php';

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$startDate = $_POST['start_date'] ?? '';
	$endDate = $_POST['end_date'] ?? '';

	if (!$startDate || !$endDate) {
		$error = 'Tanggal mulai dan sampai wajib diisi.';
	} else if (strtotime($endDate) < strtotime($startDate)) {
		$error = 'Tanggal sampai tidak boleh lebih awal dari tanggal mulai.';
	} else {
		try {
			$stmt = $pdo->prepare("UPDATE report SET status = 0 WHERE jimpitan_date >= :start AND jimpitan_date <= :end");
			$stmt->execute([':start' => $startDate, ':end' => $endDate]);
			$affected = $stmt->rowCount();
			$message = "Berhasil mereset status menjadi 0 untuk $affected baris.";
		} catch (Throwable $e) {
			$error = 'Gagal mereset status: ' . $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset Status Report</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
	<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow">
		<h1 class="text-xl font-bold mb-4">Reset Status Report</h1>
		<p class="text-sm text-gray-500 mb-4">Set status menjadi 0 pada rentang tanggal tertentu.</p>

		<?php if ($message): ?>
			<div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>
		<?php if ($error): ?>
			<div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>

		<form method="POST" class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Mulai Tanggal</label>
				<input type="date" name="start_date" class="border rounded w-full px-3 py-2" required>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
				<input type="date" name="end_date" class="border rounded w-full px-3 py-2" required>
			</div>
			<button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">Reset Status</button>
			<a href="../index.php" class="ml-2 inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
		</form>
	</div>
</body>
</html>


