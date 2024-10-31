// Data untuk grafik
const data = {
    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'],
    datasets: [{
        label: 'Jumlah Penjualan',
        data: [65, 59, 80, 81, 56, 55],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Konfigurasi grafik
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
