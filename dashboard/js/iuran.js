// iuran.js
function openModal() {
  document.getElementById("modalIuran").classList.remove("hidden");
}

function lihatDetail(nokk, tahun) {
  alert("lihatDetail terpanggil: " + nokk + ", " + tahun);
  window.location.href = `../iuran_detail.php?nokk=${encodeURIComponent(
    nokk
  )}&tahun=${encodeURIComponent(tahun)}`;
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formIuran");
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(form);

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
    }
  });
});
