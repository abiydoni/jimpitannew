<script>
$(document).ready(function () {
  // Simpan data warga (tambah/edit)
  $('#formWarga').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: 'warga_action.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        alert(response);
        $('#formWarga')[0].reset();
        $('#modalWarga').addClass('hidden');
        loadDataWarga();
      }
    });
  });

  // Ambil data untuk diedit
  $(document).on('click', '.btnEdit', function () {
    var id = $(this).data('id');
    $.ajax({
      url: 'warga_action.php',
      type: 'POST',
      data: { action: 'get', id: id },
      dataType: 'json',
      success: function (data) {
        $('#id').val(data.id);
        $('#nik').val(data.nik);
        $('#nkk').val(data.nkk);
        $('#nama').val(data.nama);
        $('#hubungan').val(data.hubungan);
        $('#jk').val(data.jk);
        $('#tmp_lahir').val(data.tmp_lahir);
        $('#tgl_lahir').val(data.tgl_lahir);
        $('#alamat').val(data.alamat);
        $('#rt').val(data.rt);
        $('#rw').val(data.rw);
        $('#agama').val(data.agama);
        $('#status').val(data.status);
        $('#pekerjaan').val(data.pekerjaan);
        $('#hp').val(data.hp);
        $('#provinsi').val(data.provinsi).trigger('change');
        setTimeout(function () {
          $('#kota').val(data.kota).trigger('change');
        }, 500);
        setTimeout(function () {
          $('#kecamatan').val(data.kecamatan).trigger('change');
        }, 1000);
        setTimeout(function () {
          $('#kelurahan').val(data.kelurahan);
        }, 1500);
        $('#modalWarga').removeClass('hidden');
      }
    });
  });

  // Hapus data warga
  $(document).on('click', '.btnHapus', function () {
    if (confirm("Yakin ingin menghapus data ini?")) {
      var id = $(this).data('id');
      $.ajax({
        url: 'warga_action.php',
        type: 'POST',
        data: { action: 'delete', id: id },
        success: function (response) {
          alert(response);
          loadDataWarga();
        }
      });
    }
  });
});
</script>
