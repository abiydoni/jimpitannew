// Mengambil data dari API
fetch("api/get_saldo.php")
  .then((response) => response.json())
  .then((result) => {
    createChart(result.labels, result.data);
  });

function createChart(labels, dataPoints) {
  const data = {
    labels: labels,
    datasets: [
      {
        label: "Pemasukan Jimpitan per Bulan",
        data: dataPoints,
        backgroundColor: "rgba(75, 192, 192, 0.5)",
        borderColor: "rgba(75, 192, 192, 1)",
        borderWidth: 1,
      },
    ],
  };

  const config = {
    type: "bar",
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Nominal (Rp)",
          },
        },
        x: {
          title: {
            display: true,
            text: "Bulan",
          },
        },
      },
    },
  };

  const ctx = document.getElementById("myChart").getContext("2d");
  document.getElementById("myChart").style.height = "400px";
  new Chart(ctx, config);
}
