document.addEventListener("DOMContentLoaded", () => {
  loadProvinsi();

  document.getElementById("provinsi").addEventListener("change", () => {
    const id = document.getElementById("provinsi").value;
    loadKota(id);
  });

  document.getElementById("kota").addEventListener("change", () => {
    const id = document.getElementById("kota").value;
    loadKecamatan(id);
  });

  document.getElementById("kecamatan").addEventListener("change", () => {
    const id = document.getElementById("kecamatan").value;
    loadKelurahan(id);
  });

  // Buka modal tambah
  window.openTambah = () => {
    resetForm();
    document.getElementById("modalTitle").innerText = "Tambah Warga";
    document.getElementById("modalWarga").classList.remove("hidden");
  };

  // Tutup modal
  window.closeModal = () => {
    document.getElementById("modalWarga").classList.add("hidden");
    resetForm();
  };

  // Submit form
  document.getElementById("formWarga").addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const nik = formData.get("nik");
    const nokk = formData.get("nokk");

    if (nik.length !== 16 || nokk.length !== 16) {
      alert("NIK dan No KK harus 16 digit.");
      return;
    }

    try {
      const response = await fetch("warga_action.php", {
        method: "POST",
        body: formData,
      });
      const res = await response.json();

      if (res.success) {
        alert(res.message);
        closeModal();
        loadWarga(); // fungsi reload data tabel (buat sendiri di warga.php)
      } else {
        alert(res.message || "Gagal menyimpan data.");
      }
    } catch (err) {
      alert("Terjadi kesalahan saat mengirim data.");
      console.error(err);
    }
  });
});

// Load wilayah dari API EMSIFA
const API = "https://www.emsifa.com/api-wilayah-indonesia/api/";

$(document).ready(function () {
  loadProvinsi();

  $("#provinsi").on("change", function () {
    let provId = $(this).val();
    if (provId) {
      loadKota(provId);
    }
  });

  $("#kota").on("change", function () {
    let kotaId = $(this).val();
    if (kotaId) {
      loadKecamatan(kotaId);
    }
  });

  $("#kecamatan").on("change", function () {
    let kecId = $(this).val();
    if (kecId) {
      loadKelurahan(kecId);
    }
  });
});

function loadProvinsi() {
  $.getJSON(`${API}provinsi.json`, function (data) {
    $("#provinsi").html('<option value="">-- Pilih Provinsi --</option>');
    data.forEach((item) => {
      $("#provinsi").append(`<option value="${item.id}">${item.name}</option>`);
    });
  });
}

function loadKota(provId) {
  $.getJSON(`${API}regencies/${provId}.json`, function (data) {
    $("#kota").html('<option value="">-- Pilih Kota --</option>');
    $("#kecamatan").html('<option value="">-- Pilih Kecamatan --</option>');
    $("#kelurahan").html('<option value="">-- Pilih Kelurahan --</option>');
    data.forEach((item) => {
      $("#kota").append(`<option value="${item.id}">${item.name}</option>`);
    });
  });
}

function loadKecamatan(kotaId) {
  $.getJSON(`${API}districts/${kotaId}.json`, function (data) {
    $("#kecamatan").html('<option value="">-- Pilih Kecamatan --</option>');
    $("#kelurahan").html('<option value="">-- Pilih Kelurahan --</option>');
    data.forEach((item) => {
      $("#kecamatan").append(
        `<option value="${item.id}">${item.name}</option>`
      );
    });
  });
}

function loadKelurahan(kecId) {
  $.getJSON(`${API}villages/${kecId}.json`, function (data) {
    $("#kelurahan").html('<option value="">-- Pilih Kelurahan --</option>');
    data.forEach((item) => {
      $("#kelurahan").append(
        `<option value="${item.id}">${item.name}</option>`
      );
    });
  });
}

// Reset form modal
function resetForm() {
  document.getElementById("formWarga").reset();
  document.getElementById("id").value = "";
  ["provinsi", "kota", "kecamatan", "kelurahan"].forEach((id) => {
    const el = document.getElementById(id);
    if (el)
      el.innerHTML = `<option value="">Pilih ${
        id.charAt(0).toUpperCase() + id.slice(1)
      }</option>`;
  });
}

function bukaModalWarga() {
  // Reset form
  $("#formWarga")[0].reset();
  $("#warga_id").val("");
  $("#modalWargaTitle").text("Tambah Data Warga");
  $("#modalWarga").removeClass("hidden").addClass("flex");
}

function tutupModalWarga() {
  $("#modalWarga").addClass("hidden").removeClass("flex");
}
