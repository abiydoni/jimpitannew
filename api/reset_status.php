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
	$statusValue = $_POST['status_value'] ?? '0';

	if (!$startDate || !$endDate) {
		$error = 'Tanggal mulai dan sampai wajib diisi.';
	} else if (strtotime($endDate) < strtotime($startDate)) {
		$error = 'Tanggal sampai tidak boleh lebih awal dari tanggal mulai.';
	} else {
		try {
			$stmt = $pdo->prepare("UPDATE report SET status = :status WHERE jimpitan_date >= :start AND jimpitan_date <= :end");
			$stmt->execute([':status' => $statusValue, ':start' => $startDate, ':end' => $endDate]);
			$affected = $stmt->rowCount();
			$statusText = $statusValue == '1' ? 'true (1)' : 'false (0)';
			$message = "Berhasil mengubah status menjadi $statusText untuk $affected baris.";
		} catch (Throwable $e) {
			$error = 'Gagal mengubah status: ' . $e->getMessage();
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
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
	<div id="overlayDiv" class="fixed inset-0 -z-10 pointer-events-none"></div>
	
	<div class="relative z-10 flex flex-col max-w-xl mx-auto p-4 shadow-lg rounded-lg mt-10">
		<h1 class="text-xl font-bold text-gray-700 mb-2 flex items-center gap-2">
			<ion-icon name="settings-outline" class="text-xl"></ion-icon>
			Reset Status Report
		</h1>
		<p class="text-sm text-gray-500 mb-4">Ubah status pada rentang tanggal tertentu.</p>

		<?php if ($message): ?>
			<script>
				Swal.fire({
					icon: 'success',
					title: 'Berhasil!',
					text: '<?php echo addslashes($message); ?>',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					timer: 3000,
					timerProgressBar: true
				});
			</script>
		<?php endif; ?>
		<?php if ($error): ?>
			<script>
				Swal.fire({
					icon: 'error',
					title: 'Gagal!',
					text: '<?php echo addslashes($error); ?>',
					showConfirmButton: true,
					confirmButtonText: 'OK'
				});
			</script>
		<?php endif; ?>

		<div class="bg-white bg-opacity-50 p-6 rounded-lg shadow">
			<form id="resetForm" method="POST" class="space-y-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Mulai Tanggal</label>
					<input type="date" name="start_date" class="border rounded w-full px-3 py-2" required>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
					<input type="date" name="end_date" class="border rounded w-full px-3 py-2" required>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
					<select name="status_value" class="border rounded w-full px-3 py-2" required>
						<option value="0">False (0) - Reset untuk hitung ulang</option>
						<option value="1">True (1) - Aktif untuk perhitungan</option>
					</select>
				</div>
				<div class="flex space-x-2">
					<button type="button" onclick="confirmReset()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded flex items-center gap-2">
						<ion-icon name="refresh-outline"></ion-icon>
						Ubah Status
					</button>
					<a href="../index.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded flex items-center gap-2">
						<ion-icon name="arrow-back-outline"></ion-icon>
						Kembali
					</a>
				</div>
			</form>
		</div>
	</div>

	<script>
		// Color picker overlay control sesuai halaman lain
		const overlay = document.getElementById('overlayDiv');
		const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
		overlay.style.backgroundColor = savedColor;

		function confirmReset() {
			const startDate = document.querySelector('input[name="start_date"]').value;
			const endDate = document.querySelector('input[name="end_date"]').value;
			const statusValue = document.querySelector('select[name="status_value"]').value;
			
			if (!startDate || !endDate) {
				Swal.fire({
					icon: 'warning',
					title: 'Peringatan!',
					text: 'Harap isi tanggal mulai dan sampai.',
					confirmButtonText: 'OK'
				});
				return;
			}

			const statusText = statusValue == '1' ? 'true (1)' : 'false (0)';
			
			Swal.fire({
				title: 'Konfirmasi Perubahan',
				text: `Yakin ingin mengubah status menjadi ${statusText} untuk rentang ${startDate} sampai ${endDate}?`,
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#d97706',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Ya, Ubah!',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					document.getElementById('resetForm').submit();
				}
			});
		}
	</script>
</body>
</html>


