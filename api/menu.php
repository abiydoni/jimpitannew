<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 0px;
            background-color: #14505c;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .floating-button a {
            right: 4px;
            color: white;
            font-size: 24px;
            text-decoration: none;
        }

        button {
            margin: 10px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: #14505c;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>

</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-2xl font-bold text-gray-700 mb-2 flex items-center">
            <ion-icon name="information-circle-outline" class="text-3xl mr-2"></ion-icon>           
            Menu Informasi
        </h1>

        <!-- Tanggal dan Waktu -->
        <div class="flex justify-center items-center bg-gray-200 p-4 rounded-lg mb-4">
            <div class="text-lg font-semibold text-gray-700" id="datetime"></div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-4 md:grid-cols-4 gap-4">
                <?php foreach ($menus as $menu) : ?>
                    <a href="<?= htmlspecialchars($menu['alamat_url']) ?>.php" 
                        class="group bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg flex flex-col items-center justify-center transition-transform transform hover:scale-105"
                        title="<?= htmlspecialchars($menu['nama']) ?>">
                        <ion-icon name="<?= $menu['ikon'] ?: 'grid-outline' ?>" class="text-3xl mb-2"></ion-icon>
                        <span class="text-sm text-center"><?= htmlspecialchars($menu['nama']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex flex-col items-center">
            <h1 class="text-center font-bold mb-2 text-gray-700">Scan Disini..!</h1>
            <a href="../index.php" 
               class="w-20 h-20 bg-red-600 hover:bg-red-800 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 motion-safe:animate-[wiggle_2s_infinite]">
                <ion-icon name="barcode-outline" class="text-4xl"></ion-icon>
            </a>
        </div>

        <div class="floating-button" style="margin-right : 70px;">
            <a href="dashboard/logout.php">
                <i class="bx bx-log-out-circle bx-tada bx-flip-horizontal" style="font-size:24px"></i>
            </a>
        </div>
    </div>

    <style>
        @keyframes wiggle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>

    <script>
        // Fungsi untuk mendapatkan tanggal dan waktu saat ini
        function updateTime() {
            const now = new Date();
            const day = now.getDate().toString().padStart(2, '0');
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const year = now.getFullYear();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

            const datetime = `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
            document.getElementById('datetime').textContent = datetime;
        }

        // Update waktu setiap detik
        setInterval(updateTime, 1000);
    </script>

</body>
</html>
