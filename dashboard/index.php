<?php include 'header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <li>
                    <i class='bx bxs-group bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalPeserta"><?php echo $totalKKj; ?> KK</h3>
                        <a href="kk.php">Jimpitan</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-group bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalPeserta"><?php echo $totalKK; ?> KK</h3>
                        <a href="warga.php">Kepala Keluarga</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-group bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalPeserta"><?php echo $totalWarga; ?> Orang</h3>
                        <a href="warga.php">Total Warga</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-badge-check bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalSaldo">
                            <?php 
                                function formatRupiah($angka) {
                                    return "Rp " . number_format($angka, 0, ',', '.');
                                }

                                // Contoh penggunaan
                                $saldo = $totalSaldo;
                                echo formatRupiah($saldo); // Output: Rp 1.500.000
                            ?>
                        </h3>
                        <a href="keuangan.php">Saldo KAS</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-info-circle bx-lg'></i>
                    <span class="text">
                        <h3 id="totalUncheck"><?php echo $totalUsers; ?> Orang</h3>
                        <a href="jadwal.php">Users JAGA</a>
                    </span>
                </li>
            </ul>

            <div class="wide-container">
                <ul class="box-info2">
                    <li>
                        <!-- <div class="table-data"> -->
                            <div class="order">
                                <div class="head">
                                    <h3>Jaga Malam Hari ini</h3>
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
                                <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th class="border border-gray-300 px-4 py-2 text-left">NAMA</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">SHIFT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if ($data) {
                                            foreach ($data as $row): ?>
                                                <tr class="border-b hover:bg-gray-100">
                                                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                                                    <td><?php echo htmlspecialchars($row["shift"]); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        } else {
                                            echo '<tr><td colspan="2" class="text-center">No data available</td></tr>';
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
</style>
