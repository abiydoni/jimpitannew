<?php
session_start();
setlocale(LC_TIME, 'id_ID.utf8');

include 'header.php';

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Sertakan koneksi database
include 'api/db.php';

// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $kode = $_GET['delete'];
    $sql = "DELETE FROM tb_warga WHERE kode=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode]);

    header("Location: warga.php");
    exit();
}


// Ambil data dari tabel users
$sql = "SELECT * FROM tb_warga";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Data Warga</h3>
            <div class="mb-4 text-center">
                <button type="button" id="modalWarga" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                </button>
            </div>
        </div>
        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
            <thead class="bg-gray-200">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>NIKK</th>
                    <th>Jenkel</th>
                    <th>Tempat/Tanggal Lahir</th>
                    <th>RT</th>
                    <th>RW</th>
                    <th>No.HP</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($data as $row):
                $timestamp = strtotime($row['tgl_lahir']);
                $tanggal = strftime('%e %B %Y', $timestamp);
            ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo htmlspecialchars($row["kode"]); ?></td>
                    <td><?php echo htmlspecialchars($row["nama"]); ?></td>
                    <td><?php echo htmlspecialchars($row["nik"]); ?></td>
                    <td><?php echo htmlspecialchars($row["nikk"]); ?></td>
                    <td><?php echo htmlspecialchars($row["jenkel"]); ?></td>
                    <td><?php echo htmlspecialchars($row['tpt_lahir'] . ', ' . $tanggal); ?></td>
                    <td><?php echo htmlspecialchars($row["rt"]); ?></td>
                    <td><?php echo htmlspecialchars($row["rw"]); ?></td>
                    <td><?php echo htmlspecialchars($row["hp"]); ?></td>
                    <td class="flex justify-center space-x-2">
                        <button onclick="openEditTarifModal('<?php echo $row['kode']; ?>', '<?php echo $row['nama']; ?>', '<?php echo $row['nik']; ?>')" class="text-blue-600 hover:text-blue-400 font-bold py-1 px-1">
                            <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                        </button>
                        <a href="warga.php?delete=<?php echo $row['kode']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $row['nama']; ?> ?')" class="text-red-600 hover:text-red-400 font-bold py-1 px-1">
                            <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Overlay -->
<div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center overflow-auto">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 relative">
    <h2 class="text-2xl font-bold mb-4">Form Data Warga</h2>
    
    <form id="formWarga" enctype="multipart/form-data">
      <input type="hidden" name="id_warga" id="id_warga">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label>Kode</label>
          <input type="text" name="kode" id="kode" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>Nama</label>
          <input type="text" name="nama" id="nama" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>NIK</label>
          <input type="text" name="nik" id="nik" maxlength="16" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>NIKK</label>
          <input type="text" name="nikk" id="nikk" maxlength="16" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>Hubungan</label>
          <select name="hubungan" id="hubungan" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih --</option>
            <option>Suami</option>
            <option>Istri</option>
            <option>Anak</option>
            <option>Keluarga Lain</option>
          </select>
        </div>
        <div>
          <label>Jenis Kelamin</label>
          <select name="jenkel" id="jenkel" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih --</option>
            <option>Laki-laki</option>
            <option>Perempuan</option>
          </select>
        </div>
        <div>
          <label>Tempat Lahir</label>
          <input type="text" name="tpt_lahir" id="tpt_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>Tanggal Lahir</label>
          <input type="date" name="tgl_lahir" id="tgl_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="md:col-span-2">
          <label>Alamat</label>
          <textarea name="alamat" id="alamat" class="w-full border rounded px-3 py-2" required></textarea>
        </div>
        <div>
          <label>RT</label>
          <input type="text" name="rt" id="rt" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>RW</label>
          <input type="text" name="rw" id="rw" class="w-full border rounded px-3 py-2" required>
        </div>

        <!-- Dropdown wilayah -->
        <div>
          <label>Negara</label>
          <input type="text" name="negara" id="negara" value="Indonesia" class="w-full border rounded px-3 py-2" readonly>
        </div>
        <div>
          <label>Provinsi</label>
          <select name="propinsi" id="propinsi" class="w-full border rounded px-3 py-2" required></select>
        </div>
        <div>
          <label>Kab/Kota</label>
          <select name="kota" id="kota" class="w-full border rounded px-3 py-2" required></select>
        </div>
        <div>
          <label>Kecamatan</label>
          <select name="kecamatan" id="kecamatan" class="w-full border rounded px-3 py-2" required></select>
        </div>
        <div>
          <label>Kelurahan</label>
          <select name="kelurahan" id="kelurahan" class="w-full border rounded px-3 py-2" required></select>
        </div>

        <div>
          <label>Agama</label>
          <select name="agama" id="agama" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih --</option>
            <option>Islam</option>
            <option>Kristen</option>
            <option>Katolik</option>
            <option>Hindu</option>
            <option>Budha</option>
            <option>Konghucu</option>
          </select>
        </div>
        <div>
          <label>Status</label>
          <select name="status" id="status" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih --</option>
            <option>Belum Kawin</option>
            <option>Kawin</option>
            <option>Cerai</option>
          </select>
        </div>
        <div>
          <label>Pekerjaan</label>
          <input type="text" name="pekerjaan" id="pekerjaan" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label>Foto</label>
          <input type="file" name="foto" id="foto" class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
<script>
    const modal = document.getElementById("myModal");
    const openModal = document.getElementById("openModal");
    const closeModal = document.getElementById("closeModal");

    openModal.onclick = function() {
        modal.classList.remove("hidden");
    }

    closeModal.onclick = function() {
        modal.classList.add("hidden");
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.add("hidden");
        }
    }
</script>

<script>
    function openEditTarifModal(kode_tarif, nama_tarif, tarif) {
        document.getElementById('edit_kode_tarif').value = kode_tarif;
        document.getElementById('edit_nama_tarif').value = nama_tarif;
        document.getElementById('edit_tarif').value = tarif;

        const modal = document.getElementById("editTarifModal");
        modal.classList.remove("hidden");
    }

    const closeeditTarifModal = document.getElementById("closeeditTarifModal");
    closeeditTarifModal.onclick = function() {
        const modal = document.getElementById("editTarifModal");
        modal.classList.add("hidden");
    }

    window.onclick = function(event) {
        const modal = document.getElementById("editTarifModal");
        if (event.target == modal) {
            modal.classList.add("hidden");
        }
    }
</script>

<script>
$(document).ready(function() {
  // Load Provinsi
  $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function(data) {
    $('#propinsi').append('<option value="">-- Pilih Provinsi --</option>');
    $.each(data, function(i, val) {
      $('#propinsi').append(`<option value="${val.name}" data-id="${val.id}">${val.name}</option>`);
    });
  });

  $('#propinsi').on('change', function() {
    const id = $(this).find(':selected').data('id');
    $('#kota').html('<option>Loading...</option>');
    $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`, function(data) {
      $('#kota').html('<option value="">-- Pilih Kota --</option>');
      $.each(data, function(i, val) {
        $('#kota').append(`<option value="${val.name}" data-id="${val.id}">${val.name}</option>`);
      });
    });
  });

  $('#kota').on('change', function() {
    const id = $(this).find(':selected').data('id');
    $('#kecamatan').html('<option>Loading...</option>');
    $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`, function(data) {
      $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
      $.each(data, function(i, val) {
        $('#kecamatan').append(`<option value="${val.name}" data-id="${val.id}">${val.name}</option>`);
      });
    });
  });

  $('#kecamatan').on('change', function() {
    const id = $(this).find(':selected').data('id');
    $('#kelurahan').html('<option>Loading...</option>');
    $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`, function(data) {
      $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');
      $.each(data, function(i, val) {
        $('#kelurahan').append(`<option value="${val.name}">${val.name}</option>`);
      });
    });
  });
});
</script>

<script>
function bukaModal() {
  $('#modalWarga').removeClass('hidden');
}
function tutupModal() {
  $('#modalWarga').addClass('hidden');
}
</script>

<?php
// Tutup koneksi
$pdo = null;
?>