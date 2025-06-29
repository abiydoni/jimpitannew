<?php include 'header.php'; ?>

<?php
// Get the current day of the week (e.g., "Sunday", "Monday", etc.)
$currentDay = date('l'); // 'l' gives full textual representation of the day

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT name, shift 
    FROM users 
    WHERE shift = :currentDay
");

// Bind the parameter
$stmt->bindParam(':currentDay', $currentDay);

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'api/get_info.php';
?>

            <ul class="box-info">
                <li class="box-modern">
                    <i class='bx bxs-group bx-lg icon-blue'></i>
                    <span class="text">
                        <h3 id="totalPeserta" class="text-dark"><?php echo $totalKKj; ?> KK</h3>
                        <a href="kk.php" class="link-modern">Jimpitan</a>
                    </span>
                </li>
                <li class="box-modern">
                    <i class='bx bxs-group bx-lg icon-green'></i>
                    <span class="text">
                        <h3 id="totalPeserta" class="text-dark"><?php echo $totalKK; ?> KK</h3>
                        <a href="warga.php" class="link-modern">Kepala Keluarga</a>
                    </span>
                </li>
                <li class="box-modern">
                    <i class='bx bxs-group bx-lg icon-purple'></i>
                    <span class="text">
                        <h3 id="totalPeserta" class="text-dark"><?php echo $totalWarga; ?> Orang</h3>
                        <a href="warga.php" class="link-modern">Total Warga</a>
                    </span>
                </li>
                <li class="box-modern">
                    <i class='bx bxs-badge-check bx-lg icon-orange'></i>
                    <span class="text">
                        <h3 id="totalSaldo" class="text-dark">
                            <?php 
                                function formatRupiah($angka) {
                                    return "Rp " . number_format($angka, 0, ',', '.');
                                }

                                // Contoh penggunaan
                                $saldo = $totalSaldo;
                                echo formatRupiah($saldo); // Output: Rp 1.500.000
                            ?>
                        </h3>
                        <a href="keuangan.php" class="link-modern">Saldo KAS</a>
                    </span>
                </li>
                <li class="box-modern">
                    <i class='bx bxs-info-circle bx-lg icon-pink'></i>
                    <span class="text">
                        <h3 id="totalUncheck" class="text-dark"><?php echo $totalUsers; ?> Orang</h3>
                        <a href="jadwal.php" class="link-modern">Users JAGA</a>
                    </span>
                </li>
            </ul>

            <div class="wide-container">
                <ul class="box-info2">
                    <li>
                        <!-- <div class="table-data"> -->
                            <div class="order">
                                <div class="head">
                                    <h1 class="judul-jaga">Jaga Malam Hari ini</h1>
                                </div>
                                <?php
                                    // Mengatur locale ke bahasa Indonesia
                                    setlocale(LC_TIME, 'id_ID.UTF-8'); // Untuk sistem berbasis Unix/Linux
                                    // setlocale(LC_TIME, 'ind'); // Untuk Windows

                                    // Mengambil tanggal sekarang
                                    $tanggal_sekarang = strftime("%A, %d %B %Y");

                                    echo "<p>$tanggal_sekarang</p>";
                                ?>
                              <br>
                                <table class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">NAMA</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">SHIFT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if ($data) {
                                            $no = 1;
                                            $hariIndo = [
                                                'Sunday' => 'Minggu',
                                                'Monday' => 'Senin',
                                                'Tuesday' => 'Selasa',
                                                'Wednesday' => 'Rabu',
                                                'Thursday' => 'Kamis',
                                                'Friday' => 'Jumat',
                                                'Saturday' => 'Sabtu',
                                            ];
                                            foreach ($data as $row): ?>
                                                <tr class="border-b hover:bg-gray-100">
                                                    <td style="font-size:0.92rem;"><?php echo $no++; ?></td>
                                                    <td style="font-size:0.92rem;"><?php echo htmlspecialchars($row["name"]); ?></td>
                                                    <td style="font-size:0.92rem;">
                                                        <?php 
                                                            $shift = $row["shift"];
                                                            echo isset($hariIndo[$shift]) ? $hariIndo[$shift] : htmlspecialchars($shift);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        } else {
                                            echo '<tr><td colspan="3" class="text-center">No data available</td></tr>';
                                        }
                                    ?>
                                    </tbody>
                                </table>                        
                            </div>
                        <!-- </div> -->
                    </li>
                    <li>
                        <canvas id="myChart" style="height: 400px;" class="w-full max-w-md mx-auto bg-white p-4 rounded-lg shadow"></canvas>
                    </li>
                </ul>
            </div>
<?php include 'footer.php'; ?>
  <script>
    // Fetch data dari API
    fetch("api/get_saldo.php")
      .then((res) => res.json())
      .then((result) => {
        console.log(result); // DEBUG: cek apakah datanya benar
        createChart(result.labels, result.data);
      });

    let chartInstance = null;

    function createChart(labels, dataPoints) {
      const ctx = document.getElementById("myChart").getContext("2d");

      // Hancurkan chart lama jika ada
      if (chartInstance) {
        chartInstance.destroy();
      }
        if (typeof Chart === 'undefined') {
        alert("Chart.js belum dimuat!");
        }

      chartInstance = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [{
            label: "Pemasukan Jimpitan per Bulan",
            data: dataPoints,
            backgroundColor: "rgba(75, 192, 192, 0.5)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return "Rp" + value.toLocaleString("id-ID");
                }
              }
            }
          }
        }
      });
    }
  </script>

<style>
/* Modern Box Styling */
.box-modern {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
  border: 2px solid #e2e8f0 !important;
  border-radius: 16px !important;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
  padding: 1rem 1.2rem !important;
  min-height: 120px !important;
  max-height: 140px !important;
  transition: all 0.3s ease !important;
  backdrop-filter: blur(10px) !important;
}

.box-modern:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  border-color: #cbd5e1 !important;
}

/* Icon Colors */
.icon-blue {
  color: #3b82f6 !important;
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.icon-green {
  color: #10b981 !important;
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.icon-purple {
  color: #8b5cf6 !important;
  background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
}

.icon-orange {
  color: #f59e0b !important;
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.icon-pink {
  color: #ec4899 !important;
  background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(236, 72, 153, 0.2);
}

/* Text Styling */
.text-dark {
  color: #1e293b !important;
  font-weight: 700 !important;
  margin: 0 !important;
}

.link-modern {
  color: #64748b !important;
  text-decoration: none !important;
  font-weight: 500 !important;
  transition: color 0.3s ease !important;
  font-size: 0.9rem !important;
}

.link-modern:hover {
  color: #475569 !important;
  text-decoration: underline !important;
}

/* Box Info Layout */
.box-info {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  justify-content: center;
  margin-bottom: 2rem;
  padding: 0;
}

.box-info li {
  flex: 1 1 200px;
  min-width: 180px;
  max-width: 250px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  position: relative;
  margin: 0;
}

.box-info li .bx {
  margin-bottom: 0.8rem;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  font-size: 28px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.box-info li .text {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  width: 100%;
}

.box-info li .text h3 {
  font-size: 1.4rem;
  font-weight: 700;
  margin: 0;
}

.box-info li .text a {
  font-size: 0.9rem;
  margin-top: 0.2rem;
}

.wide-container {
  max-width: 1200px;
  margin: 0 auto 2rem auto;
  padding: 1.5rem 1.2rem;
}
.wide-container .box-info {
  flex-wrap: nowrap;
  gap: 2rem;
}
.wide-container .box-info li {
  flex: 1 1 0;
  min-width: 0;
  max-width: 100%;
}
@media (max-width: 900px) {
  .wide-container .box-info {
    flex-wrap: wrap;
    gap: 1rem;
  }
}
@media (max-width: 600px) {
  .wide-container {
    padding: 0.5rem 0.2rem;
  }
}
.box-info2 {
  display: flex;
  flex-wrap: nowrap;
  gap: 2.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
  padding: 0;
}
.box-info2 li {
  flex: 1 1 50%;
  min-width: 320px;
  background: #fff;
  border-radius: 18px;
  /* box-shadow: 0 2px 12px rgba(0,0,0,0.06); */
  padding: 1.5rem 1.2rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  justify-content: flex-start;
  position: relative;
  margin: 0;
  max-width: 100%;
  /* Hapus efek hover/transform */
  box-shadow: none;
  transition: none;
}
.box-info2 li:hover {
  box-shadow: none;
  transform: none;
}
@media (max-width: 900px) {
  .box-info2 {
    flex-wrap: nowrap;
    gap: 1.2rem;
  }
  .box-info2 li {
    flex: 1 1 50%;
    min-width: 0;
    padding: 1rem 0.5rem;
  }
}
@media (max-width: 600px) {
  .box-info2 {
    flex-wrap: wrap;
    gap: 0.7rem;
  }
  .box-info2 li {
    flex: 1 1 100%;
    min-width: 0;
    padding: 0.7rem 0.2rem;
  }
}
.judul-jaga {
  font-size: 2rem;
  font-weight: bold;
  color: #222;
  margin-bottom: 0.7rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .box-info {
    gap: 1rem;
  }
  
  .box-info li {
    flex: 1 1 150px;
    min-width: 140px;
  }
  
  .box-modern {
    padding: 0.8rem 1rem !important;
    min-height: 100px !important;
    max-height: 120px !important;
  }
  
  .box-info li .bx {
    width: 50px;
    height: 50px;
    font-size: 24px;
    margin-bottom: 0.6rem;
  }
  
  .box-info li .text h3 {
    font-size: 1.2rem;
  }
  
  .link-modern {
    font-size: 0.8rem !important;
  }
}
</style>
