// iuran.js
console.log("iuran.js loaded");

function openModal() {
  document.getElementById("modalIuran").classList.remove("hidden");
}

function lihatDetail(nokk, tahun) {
  window.location.href = `../iuran_detail.php?nokk=${encodeURIComponent(
    nokk
  )}&tahun=${encodeURIComponent(tahun)}`;
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formIuran");
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    // Validasi manual sebelum submit
    const nokk = form.nokk.value.trim();
    const jenis = form.jenis_iuran.value.trim();
    const bulan = form.bulan.value.trim();
    const tahun = form.tahun.value.trim();
    const jumlah = form.jumlah.value.trim();
    if (!nokk || !jenis || !bulan || !tahun || !jumlah) {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Semua field wajib diisi!",
      });
      return;
    }
    const formData = new FormData(form);
    try {
      const res = await fetch("../api/iuran_action.php", {
        method: "POST",
        body: formData,
      });
      const json = await res.json();
      if (json.success) {
        Swal.fire({
          icon: "success",
          title: "Sukses",
          text: "Data iuran berhasil disimpan!",
          timer: 2000,
          showConfirmButton: false,
        }).then(() => location.reload());
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: json.message || "Gagal menyimpan data!",
        });
      }
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Terjadi kesalahan koneksi atau server!",
      });
    }
  });
});

window.openModal = openModal;
