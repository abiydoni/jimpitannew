// warga.js

function loadWarga() {
  $.post("warga_action.php", { aksi: "read" }, function (data) {
    let rows = "";
    JSON.parse(data).forEach((w) => {
      rows += `
        <tr class="border-b hover:bg-gray-100">
          <td class="p-2">${w.nama}</td>
          <td class="p-2">${w.nik}</td>
          <td class="p-2">${w.jenkel}</td>
          <td class="p-2">${w.tgl_lahir}</td>
          <td class="p-2">${w.alamat}</td>
          <td class="p-2 flex gap-1">
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded editBtn" data-id="${w.id_warga}">Edit</button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded deleteBtn" data-id="${w.id_warga}">Hapus</button>
          </td>
        </tr>
      `;
    });
    $("#dataWarga").html(rows);
  });
}

// Simpan data (Create / Update)
$("#formWarga").submit(function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append("aksi", $("#id_warga").val() ? "update" : "create");

  $.ajax({
    type: "POST",
    url: "warga_action.php",
    data: formData,
    contentType: false,
    processData: false,
    success: function (res) {
      const hasil = JSON.parse(res);
      if (hasil.status) {
        $("#modalForm").addClass("hidden");
        $("#formWarga")[0].reset();
        $("#id_warga").val("");
        loadWarga();
      } else {
        alert("Gagal menyimpan data.");
      }
    },
  });
});

// Tampilkan modal tambah
$("#btnTambah").click(function () {
  $("#formWarga")[0].reset();
  $("#id_warga").val("");
  $("#modalForm").removeClass("hidden flex");
});

// Tombol edit
$(document).on("click", ".editBtn", function () {
  const id = $(this).data("id");
  $.post("warga_action.php", { aksi: "get", id }, function (res) {
    const data = JSON.parse(res);
    for (const k in data) {
      if (k != "foto") $(`#${k}`).val(data[k]);
    }
    $("#modalForm").removeClass("hidden flex");
  });
});

// Tombol hapus
$(document).on("click", ".deleteBtn", function () {
  if (confirm("Yakin ingin menghapus data ini?")) {
    const id = $(this).data("id");
    $.post("warga_action.php", { aksi: "delete", id }, function (res) {
      const hasil = JSON.parse(res);
      if (hasil.status) loadWarga();
      else alert("Gagal menghapus data.");
    });
  }
});

// Tutup modal
$("#btnCloseModal").click(function () {
  $("#modalForm").addClass("hidden");
  $("#formWarga")[0].reset();
  $("#id_warga").val("");
});

// Load awal data warga
$(document).ready(function () {
  loadWarga();
});
