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

function loadProvinsi() {
  fetch(`${API}provinsi.json`)
    .then((res) => res.json())
    .then((data) => {
      const provinsiSelect = document.getElementById("provinsi");
      provinsiSelect.innerHTML =
        '<option value="">-- Pilih Provinsi --</option>';
      data.forEach((item) => {
        provinsiSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
      });
    });
}

function loadKota(provId) {
  fetch(`${API}regencies/${provId}.json`)
    .then((res) => res.json())
    .then((data) => {
      const kota = document.getElementById("kota");
      const kecamatan = document.getElementById("kecamatan");
      const kelurahan = document.getElementById("kelurahan");
      kota.innerHTML = '<option value="">-- Pilih Kota --</option>';
      kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
      kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
      data.forEach((item) => {
        kota.innerHTML += `<option value="${item.id}">${item.name}</option>`;
      });
    });
}

function loadKecamatan(kotaId) {
  fetch(`${API}districts/${kotaId}.json`)
    .then((res) => res.json())
    .then((data) => {
      const kecamatan = document.getElementById("kecamatan");
      const kelurahan = document.getElementById("kelurahan");
      kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
      kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
      data.forEach((item) => {
        kecamatan.innerHTML += `<option value="${item.id}">${item.name}</option>`;
      });
    });
}

function loadKelurahan(kecId) {
  fetch(`${API}villages/${kecId}.json`)
    .then((res) => res.json())
    .then((data) => {
      const kelurahan = document.getElementById("kelurahan");
      kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
      data.forEach((item) => {
        kelurahan.innerHTML += `<option value="${item.id}">${item.name}</option>`;
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
