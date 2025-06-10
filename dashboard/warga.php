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

function generateKodeWarga($prefix = 'RT07') {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM tb_warga");
    $count = $stmt->fetchColumn();
    return $prefix . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
}
?>


<div class="table-data">
    <div class="order overflow-x-auto">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">+ Tambah Warga</button>
        </div>
        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
            <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">NIK</th>
                <th class="px-4 py-2 border">JK</th>
                <th class="px-4 py-2 border">TTL</th>
                <th class="px-4 py-2 border">Alamat</th>
                <th class="px-4 py-2 border">Pekerjaan</th>
                <th class="px-4 py-2 border">No HP</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
            </thead>
            <tbody id="data-warga" class="text-sm">
            <!-- Data warga akan dimuat di sini -->
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 px-2">
  <div class="bg-white p-4 rounded-lg w-full max-w-md relative overflow-y-auto max-h-[90vh] text-sm">
    <h2 class="text-lg font-semibold mb-3">Form Warga</h2>
    <form id="formWarga" class="space-y-3">
      <input type="hidden" name="id_warga" id="id_warga">
      <input type="hidden" name="aksi" value="save">

      <!-- Grid form -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input type="text" name="kode" id="kode" placeholder="Kode: RT0700001" class="border p-1.5 rounded text-sm">
        <input type="text" name="nama" id="nama" placeholder="Nama" class="border p-1.5 rounded text-sm">
        <input type="text" name="nik" id="nik" placeholder="NIK" class="border p-1.5 rounded text-sm">
        <select name="hubungan" id="hubungan" class="border p-1.5 rounded text-sm">
          <option value="Suami">Suami</option>
          <option value="Istri">Istri</option>
          <option value="Anak">Anak</option>
          <option value="Soudara Lain">Saudara Lain</option>
        </select>
        <input type="text" name="nikk" id="nikk" placeholder="NIK KK" class="border p-1.5 rounded text-sm">
        <select name="jenkel" id="jenkel" class="border p-1.5 rounded text-sm">
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>
        <input type="text" name="tpt_lahir" id="tpt_lahir" placeholder="Tempat Lahir" class="border p-1.5 rounded text-sm">
        <input type="date" name="tgl_lahir" id="tgl_lahir" class="border p-1.5 rounded text-sm" value="<?= date('Y-m-d') ?>">
        <textarea name="alamat" id="alamat" placeholder="Alamat: Jl..." class="border p-1.5 rounded col-span-1 sm:col-span-2 text-sm"></textarea>
        <input type="number" name="rt" id="rt" placeholder="RT" class="border p-1.5 rounded text-sm" value="0" min="0">
        <input type="number" name="rw" id="rw" placeholder="RW" class="border p-1.5 rounded text-sm" value="0" min="0">
        <select id="negara" name="negara" class="selectWilayah border p-1.5 rounded text-sm">
        <option value="Indonesia">Indonesia</option>
        </select>
        <select id="propinsi" name="propinsi" class="selectWilayah border p-1.5 rounded text-sm">
        <option value="">Pilih Provinsi</option>
        </select>
        <select id="kota" name="kota" class="selectWilayah border p-1.5 rounded text-sm">
        <option value="">Pilih Kota/Kabupaten</option>
        </select>
        <select id="kecamatan" name="kecamatan" class="selectWilayah border p-1.5 rounded text-sm">
        <option value="">Pilih Kecamatan</option>
        </select>
        <select id="kelurahan" name="kelurahan" class="selectWilayah border p-1.5 rounded text-sm">
        <option value="">Pilih Kelurahan/Desa</option>
        </select>
        <select name="agama" id="agama" class="border p-1.5 rounded text-sm">
          <option value="Islam">Islam</option>
          <option value="Kristen">Kristen</option>
          <option value="Katolik">Katolik</option>
          <option value="Hindu">Hindu</option>
          <option value="Budha">Budha</option>
          <option value="Lainnya">Lainnya</option>
        </select>
        <select name="status" id="status" class="border p-1.5 rounded text-sm">
          <option value="K">Kawin</option>
          <option value="TK">Tidak Kawin</option>
          <option value="J">Janda</option>
          <option value="D">Duda</option>
          <option value="P">Pelajar</option>
          <option value="L">Lainnya</option>
        </select>
        <select name="pekerjaan" id="pekerjaan" class="border p-1.5 rounded text-sm">
          <option value="PNS">PNS</option>
          <option value="Swasta">Karyawan Swasta</option>
          <option value="Wirausaha">Wirausaha</option>
          <option value="Pelajar">Pelajar</option>
          <option value="Lainnya">Lainnya</option>
        </select>
        <input type="tel" name="hp" id="hp" placeholder="Nomor HP: 08..." class="border p-1.5 rounded text-sm">
      </div>

      <!-- Tombol aksi -->
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeModal()" class="px-3 py-1.5 border rounded text-sm">Batal</button>
        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">Simpan</button>
      </div>
    </form>
  </div>
</div>

  <?php include 'footer.php'; ?>

  <script>
    function loadData() {
      $.post('api/warga_action.php', { aksi: 'read' }, function(data) {
        $('#data-warga').html(data);
      });
    }

    $('#formWarga').on('submit', function(e) {
      e.preventDefault();
      $.post('api/warga_action.php', $(this).serialize(), function() {
        loadData();
        closeModal();
        $('#formWarga')[0].reset();
        $('#id_warga').val('');
        $('.selectWilayah').val(null).trigger('change');
      });
    });

    function editData(id) {
    $.post('api/warga_action.php', { aksi: 'get', id }, function(data) {
        const obj = JSON.parse(data);

        // Isi field biasa
        for (let key in obj) {
        $('#' + key).val(obj[key]);
        }

        // Isi dropdown wilayah secara berurutan
        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function (provinsiData) {
        $('#propinsi').html('<option value="">Pilih Provinsi</option>');
        $.each(provinsiData, function (i, p) {
            $('#propinsi').append(`<option value="${p.id}">${p.name}</option>`);
        });

        $('#propinsi').val(obj.propinsi).trigger('change');

        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/" + obj.propinsi + ".json", function (kotaData) {
            $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>');
            $.each(kotaData, function (i, k) {
            $('#kota').append(`<option value="${k.id}">${k.name}</option>`);
            });

            $('#kota').val(obj.kota).trigger('change');

            $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/" + obj.kota + ".json", function (kecData) {
            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $.each(kecData, function (i, kc) {
                $('#kecamatan').append(`<option value="${kc.id}">${kc.name}</option>`);
            });

            $('#kecamatan').val(obj.kecamatan).trigger('change');

            $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/villages/" + obj.kecamatan + ".json", function (kelData) {
                $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');
                $.each(kelData, function (i, kel) {
                $('#kelurahan').append(`<option value="${kel.name}">${kel.name}</option>`);
                });

                $('#kelurahan').val(obj.kelurahan).trigger('change');
            });
            });
        });
        });

        openModal();
    });
    }

    function hapusData(id) {
      if (confirm('Yakin hapus data ini?')) {
        $.post('api/warga_action.php', { aksi: 'delete', id }, function() {
          loadData();
        });
      }
    }

    function openModal() {
    $('#modal').removeClass('hidden').addClass('flex');

    // Inisialisasi Select2 saat modal dibuka
    $('.selectWilayah').select2({
        width: '100%',
        dropdownParent: $('#modal'), // agar tidak tertutup modal
        placeholder: 'Pilih opsi',
        allowClear: true
    });
    }

    function closeModal() {
      $('#modal').addClass('hidden').removeClass('flex');
    }

    // Load data on page load
    $(document).ready(loadData);
  </script>

<script>
$(document).ready(function () {
  // Load provinsi
  $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function (data) {
    $.each(data, function (i, provinsi) {
      $('#propinsi').append($('<option>', {
        value: provinsi.id,
        text: provinsi.name
      }));
    });
  });

  // Load kota/kabupaten saat provinsi dipilih
  $('#propinsi').on('change', function () {
    var provinsiId = $(this).val();
    $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>');
    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');
    $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/" + provinsiId + ".json", function (data) {
      $.each(data, function (i, kota) {
        $('#kota').append($('<option>', {
          value: kota.id,
          text: kota.name
        }));
      });
    });
  });

  // Load kecamatan saat kota dipilih
  $('#kota').on('change', function () {
    var kotaId = $(this).val();
    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');
    $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/" + kotaId + ".json", function (data) {
      $.each(data, function (i, kec) {
        $('#kecamatan').append($('<option>', {
          value: kec.id,
          text: kec.name
        }));
      });
    });
  });

  // Load kelurahan saat kecamatan dipilih
  $('#kecamatan').on('change', function () {
    var kecamatanId = $(this).val();
    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');
    $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/villages/" + kecamatanId + ".json", function (data) {
      $.each(data, function (i, kel) {
        $('#kelurahan').append($('<option>', {
          value: kel.name,
          text: kel.name
        }));
      });
    });
  });
});

// $(document).ready(function () {
//   $('.selectWilayah').select2({
//     width: '100%',
//     placeholder: 'Pilih opsi',
//     allowClear: true
//   });
// });
</script>
