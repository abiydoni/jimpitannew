// // Function to show or hide the landscape blocker
// function updateLandscapeBlocker() {
//     let landscapeBlocker = document.getElementById('landscapeBlocker');
//     if (landscapeBlocker) {
//         if (window.orientation === 90 || window.orientation === -90) {
//             landscapeBlocker.style.display = 'flex';
//             html5QrCode.stop().catch(function(err) {
//                 console.error('Error stopping QR Code scanner:', err);
//             });
//         } else {
//             landscapeBlocker.style.display = 'none';
//             startScanning();
//         }
//     }
// }

// // Start scanning when document is loaded
// document.addEventListener('DOMContentLoaded', function() {
//     updateLandscapeBlocker();
// });

// // Handle orientation change
// window.addEventListener('orientationchange', function() {
//     updateLandscapeBlocker();
// });

// // Function to play audio
// function playAudio() {
//     const audio = document.getElementById('audio');
//     if (audio) {
//         audio.play().catch(function(error) {
//             console.error('Error playing audio:', error);
//         });
//     }
// }

// function onScanSuccess(decodedText, decodedResult) {
//     const id = decodedText; // Ambil ID dari QR code

//     // Play audio
//     playAudio();

//     // Kirim ID ke server GAS
//     fetch('https://script.google.com/macros/s/AKfycbxD7iXEFOCCOrX5Ryln_NrzptYjtWf6Ia_WJu-j8Gtfgv3cefqdHIg4KL9N-5U4n60d/exec', {
//         method: 'POST',
//         mode: 'no-cors',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({ id: id })
//     })
//     .then(response => {
//         // Jika perlu, tambahkan logika untuk memeriksa status respons
//         console.log('Data sent to GAS:', id);
//     })
//     .catch(error => {
//         console.error('Error sending data to GAS:', error);
//     });

//     // Fetch ke API get_kk.php untuk mendapatkan nama
//     fetch('../api/get_kk.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({ code_id: id })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success && data.kk_name) {
//             const options = { day: 'numeric', month: 'long', year: 'numeric' };
//             const today = new Date().toLocaleDateString('id-ID', options); // Format tanggal
//             const nominal = 'Rp500'; // Nominal yang ingin ditampilkan

//             Swal.fire({
//                 icon: 'success',
//                 title: `${data.kk_name}`,
//                 text: `Jimpitan tanggal ${today} tercatat dengan nominal ${nominal}`,
//                 timer: 10000,
//                 timerProgressBar: true,
//                 customClass: {
//                     popup: 'rounded',
//                     timerProgressBar: 'custom-timer-progress-bar',
//                     confirmButton: 'roundedBtn'
//                 },
//                 willClose: startScanning // Mulai kembali pemindaian setelah alert ditutup
//             });
//         } else {
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Not Found',
//                 text: 'No record found for the scanned ID.',
//                 confirmButton: 'OK',
//                 willClose: startScanning // Mulai kembali pemindaian setelah alert ditutup
//             });
//         }
//     })
//     .catch(error => {
//         console.error('Error fetching data:', error);
//         Swal.fire({
//             icon: 'error',
//             title: 'Fetch Error',
//             text: 'Could not retrieve data from server.',
//             confirmButton: 'OK',
//             willClose: startScanning // Mulai kembali pemindaian setelah alert ditutup
//         });
//     });

//     // Stop scanning after successful read
//     html5QrCode.stop();
// }

//   function onScanError(errorMessage) {
//       // Handle scan error (optional)
//       console.warn(`Scan error: ${errorMessage}`);
//   }

//   function startScanning() {
//       // Initialize the QR code scanner
//       html5QrCode.start(
//           { facingMode: "environment" }, // Use rear camera
//           {
//               fps: 20, // Frame rate
//               qrbox: 200 // QR code scanning box size
//           },
//           onScanSuccess,
//           onScanError
//       );
//   }

//   // Initialize the QR code scanner instance
//   const html5QrCode = new Html5Qrcode("qr-reader");

//   // Start scanning with the camera
//   startScanning();

// let isScanning = false; // Menandakan apakah pemindaian aktif
// const html5QrCode = new Html5Qrcode("qr-reader"); // Inisialisasi pemindai QR

// // Fungsi untuk menampilkan atau menyembunyikan blokir orientasi
// function updateLandscapeBlocker() {
//     const landscapeBlocker = document.getElementById('landscapeBlocker');
//     if (landscapeBlocker) {
//         if (window.orientation === 90 || window.orientation === -90) {
//             landscapeBlocker.style.display = 'flex'; // Tampilkan blokir jika dalam mode lanskap
//             stopScanning(); // Hentikan pemindaian
//         } else {
//             landscapeBlocker.style.display = 'none'; // Sembunyikan blokir
//         }
//     }
// }

// // Mulai pemindaian saat dokumen dimuat
// document.addEventListener('DOMContentLoaded', updateLandscapeBlocker);
// window.addEventListener('orientationchange', updateLandscapeBlocker);

// // Fungsi untuk memutar audio
// function playAudio() {
//     const audio = document.getElementById('audio');
//     if (audio) {
//         audio.play().catch(error => console.error('Error playing audio:', error));
//     }
// }

// // Fungsi untuk menangani hasil pemindaian
// function onScanSuccess(decodedText) {
//     console.log('Teks yang dipindai:', decodedText);
//     const id = decodedText; // Ambil ID dari kode QR
//     const nominal = 500; // Tetapkan nilai nominal
//     const today = new Date();
//     const jimpitanDate = today.toLocaleDateString('id-ID', {
//         year: 'numeric',
//         month: '2-digit',
//         day: '2-digit'
//     });
//     const [day, month, year] = jimpitanDate.split('/');
//     const formattedDate = `${year}-${month}-${day}`; // Format ke YYYY-MM-DD

//     playAudio(); // Putar audio

//     // Kirim data ke input_jimpitan.php
//     fetch('../api/input_jimpitan.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({
//             report_id: id,
//             jimpitan_date: formattedDate,
//             nominal: nominal
//             // collector: collector
//         })
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Respon jaringan tidak ok');
//         }
//         return response.json();
//     })
//     .then(data => console.log('Data berhasil dikirim:', data))
//     .catch(error => console.error('Error mengirim data:', error));

//     // Ambil nama dari API
//     fetch('../api/get_kk.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({ code_id: id })
//     })
//     .then(response => response.json())
//     .then(data => {
//         const today = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
//         const nominalFormatted = 'Rp500';
//         if (data.success && data.kk_name) {
//             Swal.fire({
//                 icon: 'success',
//                 title: `${data.kk_name}`,
//                 text: `Jimpitan tanggal ${today} tercatat dengan nominal ${nominalFormatted}`,
//                 timer: 10000,
//                 timerProgressBar: true,
//                 customClass: {
//                     popup: 'rounded',
//                     timerProgressBar: 'custom-timer-progress-bar',
//                     confirmButton: 'roundedBtn'
//                 },
//                 willClose: startScanning // Mulai pemindaian ulang saat dialog ditutup
//             });
//         } else {
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Tidak Ditemukan',
//                 text: 'Tidak ada catatan untuk ID yang dipindai.',
//                 confirmButton: 'OK',
//                 willClose: startScanning
//             });
//         }
//     })
//     .catch(error => {
//         console.error('Error fetching data:', error);
//         Swal.fire({
//             icon: 'error',
//             title: 'Kesalahan Fetch',
//             text: 'Tidak dapat mengambil data dari server.',
//             confirmButton: 'OK',
//             willClose: startScanning
//         });
//     });

//     // Hentikan pemindaian setelah pemindaian sukses
//     stopScanning();
// }

// function onScanError(errorMessage) {
//     console.warn(`Kesalahan pemindaian: ${errorMessage}`);
// }

// function startScanning() {
//     if (!isScanning) {
//         isScanning = true;
//         html5QrCode.start(
//             { facingMode: "environment" }, // Menggunakan kamera belakang
//             { fps: 20, qrbox: 200 }, // Pengaturan pemindaian
//             onScanSuccess,
//             onScanError
//         ).catch(err => console.error('Kesalahan memulai pemindaian QR code:', err));
//     }
// }

// function stopScanning() {
//     if (isScanning) {
//         isScanning = false;
//         html5QrCode.stop().catch(err => console.error('Kesalahan menghentikan pemindaian QR code:', err));
//     }
// }

// // Mengelola pemilihan file
// const fileinput = document.getElementById('qr-input-file');
// const fileInputLabel = document.getElementById('fileInputLabel');

// fileInputLabel.addEventListener('click', (e) => {
//     e.preventDefault();
//     stopScanning(); // Hentikan pemindaian saat ini
//     fileinput.click(); // Buka dialog pemilihan file
// });

// fileinput.addEventListener('change', e => {
//     // Cek apakah ada file yang dipilih
//     if (e.target.files.length === 0) {
//         // Tidak ada file dipilih, mulai pemindaian kamera
//         startScanning();
//         return;
//     }

//     const imageFile = e.target.files[0];
//     if (imageFile.type.startsWith('image/')) { // Memeriksa apakah file adalah gambar
//         html5QrCode.scanFile(imageFile, false)
//             .then(qrCodeMessage => {
//                 onScanSuccess(qrCodeMessage); // Jika pemindaian sukses
//             })
//             .catch(err => {
//                 console.error(`Kesalahan pemindaian file. Alasan: ${err}`);
//                 alert('Gagal memindai kode QR. Silakan coba lagi.');
//             });
//     } else {
//         alert('Silakan unggah file gambar yang valid.'); // Berikan umpan balik jika bukan gambar
//     }

//     // Reset file input untuk pemindaian berikutnya
//     fileinput.value = '';
// });

// // Mulai pemindaian dengan kamera
// startScanning();

let isScanning = false; // Menandakan apakah pemindaian aktif
const html5QrCode = new Html5Qrcode("qr-reader"); // Inisialisasi pemindai QR

// Fungsi untuk menampilkan atau menyembunyikan blokir orientasi
function updateLandscapeBlocker() {
  const landscapeBlocker = document.getElementById("landscapeBlocker");
  if (landscapeBlocker) {
    if (window.orientation === 90 || window.orientation === -90) {
      landscapeBlocker.style.display = "flex"; // Tampilkan blokir jika dalam mode lanskap
      stopScanning(); // Hentikan pemindaian
    } else {
      landscapeBlocker.style.display = "none"; // Sembunyikan blokir
    }
  }
}

// Mulai pemindaian saat dokumen dimuat
document.addEventListener("DOMContentLoaded", updateLandscapeBlocker);
window.addEventListener("orientationchange", updateLandscapeBlocker);

// Fungsi untuk memutar audio
function playAudio() {
  const audio = document.getElementById("audio");
  if (audio) {
    audio.play().catch((error) => console.error("Error playing audio:", error));
  }
}

// Fungsi untuk menangani hasil pemindaian
function onScanSuccess(decodedText) {
  console.log("Teks yang dipindai:", decodedText);
  const id = decodedText; // Ambil ID dari kode QR
  //const nominal = 500; // Tetapkan nilai nominal
  const today = new Date();
  const jimpitanDate = today.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });

  // Menjadikan fungsi ini async
  const nominal = await fetch('../api/get_tarif.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ kode_tarif: 'JIMPIT' }) // Mengirim kode_tarif sebagai parameter
    })
    .then(response => response.json())
    .then(data => data.nominal) // Asumsikan API mengembalikan objek dengan properti 'nominal'
    .catch(error => {
        console.error('Error fetching nominal:', error);
    });

  const [day, month, year] = jimpitanDate.split("/");
  const formattedDate = `${year}-${month}-${day}`; // Format ke YYYY-MM-DD

  playAudio(); // Putar audio

  // Log data yang akan dikirim
  console.log("Data yang akan dikirim:", {
    report_id: id,
    jimpitan_date: formattedDate,
    nominal: nominal,
  });

  fetch("../api/input_jimpitan.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      report_id: id,
      jimpitan_date: formattedDate,
      nominal: nominal,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Tampilkan pesan sukses
        Swal.fire({
          icon: "success",
          title: "Sukses",
          text: data.message,
          customClass: {
            popup: "rounded",
            confirmButton: "roundedBtn",
          },
          willClose: startScanning, // Mulai pemindaian ulang saat dialog ditutup
        });
      } else {
        // Tampilkan pesan error dengan konfirmasi untuk menghapus
        Swal.fire({
          icon: "warning",
          title: "Ooops!",
          text: data.message,
          showCancelButton: true,
          confirmButtonText: "Hapus Data",
          cancelButtonText: "Batal",
          customClass: {
            popup: "rounded",
            confirmButton: "stopBtn",
            cancelButton: "roundedBtn",
          },
        }).then((result) => {
          if (result.isConfirmed) {
            // Panggil endpoint untuk menghapus data yang sudah ada
            fetch("../api/delete_jimpitan.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                report_id: id,
                jimpitan_date: formattedDate,
              }),
            })
              .then((response) => response.json())
              .then((deleteData) => {
                if (deleteData.success) {
                  // Tampilkan pesan sukses jika data dihapus
                  Swal.fire({
                    icon: "success",
                    title: "Data Dihapus",
                    text: "Data yang sudah ada telah dihapus",
                    customClass: {
                      popup: "rounded",
                      confirmButton: "roundedBtn",
                    },
                    willClose: startScanning, // Mulai pemindaian ulang saat dialog ditutup
                  });
                } else {
                  // Tampilkan pesan error jika penghapusan gagal
                  Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                    text: deleteData.message,
                    customClass: {
                      popup: "rounded",
                      confirmButton: "roundedBtn",
                    },
                    willClose: startScanning,
                  });
                }
              });
          } else {
            // Jika batal, kembali ke pemindaian
            startScanning();
          }
        });
      }
    })
    .catch((error) => console.error("Kesalahan Fetch:", error));

  // Hentikan pemindaian setelah pemindaian sukses
  stopScanning();
}

function onScanError(errorMessage) {
  console.warn(`Kesalahan pemindaian: ${errorMessage}`);
}

function startScanning() {
  if (!isScanning) {
    isScanning = true;
    html5QrCode
      .start(
        { facingMode: "environment" }, // Menggunakan kamera belakang
        { fps: 20, qrbox: 200 }, // Pengaturan pemindaian
        onScanSuccess,
        onScanError
      )
      .catch((err) =>
        console.error("Kesalahan memulai pemindaian QR code:", err)
      );
  }
}

function stopScanning() {
  if (isScanning) {
    isScanning = false;
    html5QrCode
      .stop()
      .catch((err) =>
        console.error("Kesalahan menghentikan pemindaian QR code:", err)
      );
  }
}

// Mengelola pemilihan file
const fileinput = document.getElementById("qr-input-file");
const fileInputLabel = document.getElementById("fileInputLabel");

fileInputLabel.addEventListener("click", (e) => {
  e.preventDefault();
  stopScanning(); // Hentikan pemindaian saat ini
  fileinput.click(); // Buka dialog pemilihan file
});

fileinput.addEventListener("change", (e) => {
  // Cek apakah ada file yang dipilih
  if (e.target.files.length === 0) {
    // Tidak ada file dipilih, mulai pemindaian kamera
    startScanning();
    return;
  }

  const imageFile = e.target.files[0];
  if (imageFile.type.startsWith("image/")) {
    // Memeriksa apakah file adalah gambar
    html5QrCode
      .scanFile(imageFile, false)
      .then((qrCodeMessage) => {
        onScanSuccess(qrCodeMessage); // Jika pemindaian sukses
      })
      .catch((err) => {
        console.error(`Kesalahan pemindaian file. Alasan: ${err}`);
        alert("Gagal memindai kode QR. Silakan coba lagi.");
      });
  } else {
    alert("Silakan unggah file gambar yang valid."); // Berikan umpan balik jika bukan gambar
  }

  // Reset file input untuk pemindaian berikutnya
  fileinput.value = "";
});

// Mulai pemindaian dengan kamera
startScanning();
