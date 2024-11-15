// Mengambil data dari API
fetch("api/get_saldo.php")
  .then((response) => response.json())
  .then((data) => {
    // Konfigurasi grafik dengan data dari database
    createChart(data);
  });

function createChart(saldoData) {
  const data = {
    labels: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ], // Mengubah nama bulan menjadi tiga huruf
    datasets: [
      {
        label: "Analisa Pemasukan dan Pengeluaran per Bulan",
        data: saldoData,
        backgroundColor: "rgba(75, 192, 192, 0.2)",
        borderColor: "rgba(75, 192, 192, 1)",
        borderWidth: 1,
      },
    ],
  };
  // ... konfigurasi chart lainnya
  const config = {
    type: "bar", // Jenis grafik: 'bar', 'line', 'pie', dll.
    data: data,
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      // Menambahkan pengaturan tinggi grafik
      responsive: true,
      maintainAspectRatio: false, // Mengizinkan perubahan rasio aspek
    },
  };
  // Menentukan tinggi grafik
  document.getElementById("myChart").style.height = "400px"; // Ubah tinggi sesuai kebutuhan
  // Render grafik ke elemen canvas dengan id "myChart"
  const myChart = new Chart(document.getElementById("myChart"), config);
}
