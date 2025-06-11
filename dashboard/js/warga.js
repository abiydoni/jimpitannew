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
async function loadProvinsi(selected = "") {
  const prov = document.getElementById("provinsi");
  prov.innerHTML = '<option value="">Pilih Provinsi</option>';
  const res = await fetch(
    "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json"
  );
  const data = await res.json();
  data.forEach((d) => {
    const opt = new Option(d.name, d.id);
    if (d.id == selected) opt.selected = true;
    prov.appendChild(opt);
  });
}

async function loadKota(provId, selected = "") {
  const kota = document.getElementById("kota");
  kota.innerHTML = '<option value="">Pilih Kota</option>';
  const res = await fetch(
    `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`
  );
  const data = await res.json();
  data.forEach((d) => {
    const opt = new Option(d.name, d.id);
    if (d.id == selected) opt.selected = true;
    kota.appendChild(opt);
  });
}

async function loadKecamatan(kotaId, selected = "") {
  const kec = document.getElementById("kecamatan");
  kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
  const res = await fetch(
    `https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kotaId}.json`
  );
  const data = await res.json();
  data.forEach((d) => {
    const opt = new Option(d.name, d.id);
    if (d.id == selected) opt.selected = true;
    kec.appendChild(opt);
  });
}

async function loadKelurahan(kecId, selected = "") {
  const kel = document.getElementById("kelurahan");
  kel.innerHTML = '<option value="">Pilih Kelurahan</option>';
  const res = await fetch(
    `https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecId}.json`
  );
  const data = await res.json();
  data.forEach((d) => {
    const opt = new Option(d.name, d.name);
    if (d.name == selected) opt.selected = true;
    kel.appendChild(opt);
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
