<?php
session_start();
include 'header.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}
    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
include 'api/db.php';
$stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
$wargas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="table-data">
    <div class="order overflow-x-auto">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Data Warga</h1>
        <button id="btnTambah" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          +
        </button>
      </div>
      <a href="api/export_warga.php" target="_blank" class="bg-green-600 text-white px-3 py-1 rounded">Export Excel</a>
      <form action="api/import_warga.php" method="POST" enctype="multipart/form-data" class="inline">
        <input type="file" name="excel_file" required class="text-sm">
        <button type="submit" class="bg-yellow-500 px-3 py-1 text-white rounded">Import Excel</button>
      </form>
      <a href='api/cetak_warga.php?id=<?= $r['id_warga'] ?>' target='_blank' class="bg-gray-600 text-white px-2 py-1 text-xs">Cetak</a>

      <!-- Tabel Data Warga -->
      <div id="tabelWarga" class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
          <thead class="bg-gray-200 text-gray-700">
            <tr>
              <th class="py-2 px-4 border">No</th>
              <th class="py-2 px-4 border">Nama</th>
              <th class="py-2 px-4 border">NIK</th>
              <th class="py-2 px-4 border">HP</th>
              <th class="py-2 px-4 border">Alamat</th>
              <th class="py-2 px-4 border">Aksi</th>
            </tr>
          </thead>
          <tbody id="dataWarga">
            <!-- Data diisi lewat jQuery AJAX -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Warga -->
    <div id="modalWarga" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-3xl rounded shadow p-6 relative overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold mb-4">Form Warga</h2>

        <form id="formWarga" enctype="multipart/form-data">
          <input type="hidden" name="id_warga" id="id_warga">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label>NIK</label>
              <input type="text" name="nik" id="nik" maxlength="16" required class="w-full border rounded px-2 py-1">
            </div>
            <div>
              <label>NIKK</label>
              <input type="text" name="nikk" id="nikk" maxlength="16" required class="w-full border rounded px-2 py-1">
            </div>
            <div>
              <label>Nama</label>
              <input type="text" name="nama" id="nama" required class="w-full border rounded px-2 py-1">
            </div>
            <div>
              <label>HP</label>
              <input type="text" name="hp" id="hp" required class="w-full border rounded px-2 py-1">
            </div>
            <div>
              <label>Hubungan</label>
              <select name="hubungan" id="hubungan" required class="w-full border rounded px-2 py-1">
                <option value="">- Pilih -</option>
                <option value="Suami">Suami</option>
                <option value="Istri">Istri</option>
                <option value="Anak">Anak</option>
                <option value="Keluarga Lain">Keluarga Lain</option>
              </select>
            </div>
            <div>
              <label>Jenis Kelamin</label>
              <select name="jenkel" id="jenkel" required class="w-full border rounded px-2 py-1">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div>
              <label>Tempat Lahir</label>
              <input type="text" name="tpt_lahir" id="tpt_lahir" required class="w-full border rounded px-2 py-1" placeholder="Misal: Surabaya">
            </div>
            <div>
              <label>Tanggal Lahir</label>
              <input type="date" name="tgl_lahir" id="tgl_lahir" required class="w-full border rounded px-2 py-1">
            </div>
            <div class="md:col-span-2">
              <label>Alamat</label>
              <textarea name="alamat" id="alamat" required class="w-full border rounded px-2 py-1" rows="2"></textarea>
            </div>
            <div>
              <label>RT</label>
              <input type="number" name="rt" id="rt" min="1" required class="w-full border rounded px-2 py-1">
            </div>
            <div>
              <label>RW</label>
              <input type="number" name="rw" id="rw" min="1" required class="w-full border rounded px-2 py-1">
            </div>

            <!-- Dropdown Wilayah - provinsi, kota, kecamatan, kelurahan -->
            <div>
              <label>Provinsi</label>
              <select name="propinsi" id="propinsi" required class="w-full border rounded px-2 py-1"></select>
            </div>
            <div>
              <label>Kota/Kabupaten</label>
              <select name="kota" id="kota" required class="w-full border rounded px-2 py-1"></select>
            </div>
            <div>
              <label>Kecamatan</label>
              <select name="kecamatan" id="kecamatan" required class="w-full border rounded px-2 py-1"></select>
            </div>
            <div>
              <label>Kelurahan</label>
              <select name="kelurahan" id="kelurahan" required class="w-full border rounded px-2 py-1"></select>
            </div>

            <div>
              <label>Agama</label>
              <select name="agama" id="agama" class="w-full border rounded px-2 py-1">
                <option>Islam</option>
                <option>Kristen</option>
                <option>Katolik</option>
                <option>Hindu</option>
                <option>Budha</option>
                <option>Lainnya</option>
              </select>
            </div>
            <div>
              <label>Status</label>
              <select name="status" id="status" class="w-full border rounded px-2 py-1">
                <option>Tidak Kawin</option>
                <option>Kawin</option>
                <option>Janda</option>
                <option>Duda</option>
                <option>Lainnya</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label>Pekerjaan</label>
              <input type="text" name="pekerjaan" id="pekerjaan" class="w-full border rounded px-2 py-1" placeholder="Contoh: Petani, Guru, dll">
            </div>
            <div class="md:col-span-2">
              <label>Foto</label>
              <input type="file" name="foto" id="foto" class="w-full border rounded px-2 py-1">
              <img id="previewFoto" src="#" alt="" class="mt-2 w-32 h-auto hidden">
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="btnBatal" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script>
  // Script jQuery untuk buka modal & preview foto
  $('#btnTambah').click(function () {
    $('#formWarga')[0].reset();
    $('#previewFoto').hide();
    $('#modalWarga').removeClass('hidden');
  });

  $('#btnBatal').click(function () {
    $('#modalWarga').addClass('hidden');
  });

  $('#foto').change(function () {
    let reader = new FileReader();
    reader.onload = function (e) {
      $('#previewFoto').attr('src', e.target.result).show();
    };
    reader.readAsDataURL(this.files[0]);
  });
</script>

<script>
$(document).ready(function () {
  // Load provinsi saat halaman dibuka
  $.get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function (data) {
    $('#propinsi').append('<option value="">- Pilih Provinsi -</option>');
    $.each(data, function (i, prov) {
      $('#propinsi').append(`<option value="${prov.id}" data-nama="${prov.name}">${prov.name}</option>`);
    });
  });

  // Load kota ketika provinsi berubah
  $('#propinsi').on('change', function () {
    const provID = $(this).val();
    const provNama = $(this).find(':selected').data('nama');
    $('#kota').html('<option value="">Memuat...</option>');
    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
    $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');

    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provID}.json`, function (data) {
      $('#kota').html('<option value="">- Pilih Kota/Kabupaten -</option>');
      $.each(data, function (i, kota) {
        $('#kota').append(`<option value="${kota.id}" data-nama="${kota.name}">${kota.name}</option>`);
      });
    });
  });

  // Load kecamatan ketika kota berubah
  $('#kota').on('change', function () {
    const kotaID = $(this).val();
    const kotaNama = $(this).find(':selected').data('nama');
    $('#kecamatan').html('<option value="">Memuat...</option>');
    $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');

    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kotaID}.json`, function (data) {
      $('#kecamatan').html('<option value="">- Pilih Kecamatan -</option>');
      $.each(data, function (i, kec) {
        $('#kecamatan').append(`<option value="${kec.id}" data-nama="${kec.name}">${kec.name}</option>`);
      });
    });
  });

  // Load kelurahan ketika kecamatan berubah
  $('#kecamatan').on('change', function () {
    const kecID = $(this).val();
    const kecNama = $(this).find(':selected').data('nama');
    $('#kelurahan').html('<option value="">Memuat...</option>');

    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecID}.json`, function (data) {
      $('#kelurahan').html('<option value="">- Pilih Kelurahan -</option>');
      $.each(data, function (i, kel) {
        $('#kelurahan').append(`<option value="${kel.name}">${kel.name}</option>`);
      });
    });
  });
});
</script>

<script>
  // Saat halaman siap, muat data provinsi
  $(document).ready(function () {
    loadProvinsi();
  });

  function loadProvinsi() {
    $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function (data) {
      $('#propinsi').html('<option value="">Pilih Provinsi</option>');
      $.each(data, function (i, prov) {
        $('#propinsi').append(`<option value="${prov.id}" data-nama="${prov.name}">${prov.name}</option>`);
      });
    });
  }

  $('#propinsi').on('change', function () {
    let idProv = $(this).val();
    let namaProv = $(this).find(':selected').data('nama');
    $('#propinsi option:selected').val(namaProv); // ubah value ke nama

    if (idProv) {
      $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`, function (data) {
        $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>');
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
        $.each(data, function (i, kota) {
          $('#kota').append(`<option value="${kota.id}" data-nama="${kota.name}">${kota.name}</option>`);
        });
      });
    }
  });

  $('#kota').on('change', function () {
    let idKota = $(this).val();
    let namaKota = $(this).find(':selected').data('nama');
    $('#kota option:selected').val(namaKota);

    if (idKota) {
      $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKota}.json`, function (data) {
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
        $.each(data, function (i, kec) {
          $('#kecamatan').append(`<option value="${kec.id}" data-nama="${kec.name}">${kec.name}</option>`);
        });
      });
    }
  });

  $('#kecamatan').on('change', function () {
    let idKec = $(this).val();
    let namaKec = $(this).find(':selected').data('nama');
    $('#kecamatan option:selected').val(namaKec);

    if (idKec) {
      $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${idKec}.json`, function (data) {
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
        $.each(data, function (i, kel) {
          $('#kelurahan').append(`<option value="${kel.name}">${kel.name}</option>`);
        });
      });
    }
  });

  $('#kelurahan').on('change', function () {
    // pastikan value-nya tetap nama
    $('#kelurahan option:selected').val($('#kelurahan option:selected').text());
  });
</script>

<script>
  // Submit Form Tambah/Update
  $('#formWarga').submit(function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
      url: 'warga_action.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        let data = JSON.parse(res);
        if (data.status == 'success') {
          alert(data.message);
          $('#modalWarga').hide();
          $('#formWarga')[0].reset();
          loadWarga();
        } else {
          alert(data.message);
        }
      },
      error: function () {
        alert("Gagal mengirim data.");
      }
    });
  });
</script>

<script>
  function loadWarga() {
    $.ajax({
      url: 'warga_action.php',
      type: 'POST',
      data: { action: 'read' },
      success: function (res) {
        $('#tabelWarga').html(res);
      }
    });
  }

  // Panggil saat page load
  $(document).ready(function () {
    loadWarga();
  });
</script>

<script>
  // Tombol Edit
  $(document).on('click', '.editBtn', function () {
    var id = $(this).data('id');

    $.ajax({
      url: 'warga_action.php',
      type: 'POST',
      data: { action: 'get', id: id },
      success: function (res) {
        // Isi form modal
        $('#id_warga').val(res.id_warga);
        $('#nama').val(res.nama);
        $('#nik').val(res.nik);
        $('#hubungan').val(res.hubungan);
        $('#nikk').val(res.nikk);
        $('#jenkel').val(res.jenkel);
        $('#tpt_lahir').val(res.tpt_lahir);
        $('#tgl_lahir').val(res.tgl_lahir);
        $('#alamat').val(res.alamat);
        $('#rt').val(res.rt);
        $('#rw').val(res.rw);
        $('#kelurahan').val(res.kelurahan);
        $('#kecamatan').val(res.kecamatan);
        $('#kota').val(res.kota);
        $('#propinsi').val(res.propinsi);
        $('#negara').val(res.negara);
        $('#agama').val(res.agama);
        $('#status').val(res.status);
        $('#pekerjaan').val(res.pekerjaan);
        $('#hp').val(res.hp);
        
        $('#modalWarga').show();
      },
      error: function () {
        alert("Gagal mengambil data.");
      }
    });
  });
</script>

<script>
  $(document).on('click', '.hapusBtn', function () {
    var id = $(this).data('id');
    if (confirm('Yakin ingin menghapus data ini?')) {
      $.ajax({
        url: 'warga_action.php',
        type: 'POST',
        data: { action: 'delete', id: id },
        success: function (res) {
          if (res.status === 'success') {
            alert(res.message);
            loadWarga();
          } else {
            alert("Gagal menghapus data.");
          }
        },
        error: function () {
          alert("Terjadi kesalahan.");
        }
      });
    }
  });
</script>

  <?php include 'footer.php'; ?>

