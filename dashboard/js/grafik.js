// Mengambil data dari API
fetch('api/get_saldo.php')
    .then(response => response.json())
    .then(data => {
        // Konfigurasi grafik dengan data dari database
        createChart(data);
    });

function createChart(saldoData) {
    const data = {
        //labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        //        'Juli', 'Agustus', 'September', 'Oktober', 'Nopember', 'Desember'],
        datasets: [{
            label: 'Analisa Pemasukan dan Pengeluaran per Bulan',
            data: saldoData,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };
    // ... konfigurasi chart lainnya
    const config = {
        type: 'bar',  // Jenis grafik: 'bar', 'line', 'pie', dll.
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };
    // Render grafik ke elemen canvas dengan id "myChart"
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
}