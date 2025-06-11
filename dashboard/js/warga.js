
$(document).ready(function () {
    // Load data awal
    loadData();

    // Fungsi untuk load data warga
    function loadData(query = '') {
        $.ajax({
            url: 'warga_action.php',
            method: 'POST',
            data: { action: 'fetch', query: query },
            success: function (data) {
                $('#dataContainer').html(data);
            }
        });
    }

    // Live search
    $('#search').on('input', function () {
        const query = $(this).val();
        loadData(query);
    });

    // Tampilkan modal tambah data
    $('#btnTambah').click(function () {
        $('#formWarga')[0].reset();
        $('#id').val('');
        $('#formModalTitle').text('Tambah Data Warga');
        $('#modalForm').removeClass('hidden');
    });

    // Tutup modal
    $('.btnCloseModal').click(function () {
        $('#modalForm').addClass('hidden');
    });

    // Submit form (tambah/edit)
    $('#formWarga').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: 'warga_action.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                alert(res);
                $('#modalForm').addClass('hidden');
                loadData();
            }
        });
    });

    // Edit data
    $(document).on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'warga_action.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'get', id: id },
            success: function (data) {
                $('#id').val(data.id);
                $('#nik').val(data.nik);
                $('#nokk').val(data.nokk);
                $('#nama').val(data.nama);
                $('#jenkel').val(data.jenkel);
                $('#tpt_lahir').val(data.tpt_lahir);
                $('#tgl_lahir').val(data.tgl_lahir);
                $('#alamat').val(data.alamat);
                $('#rt').val(data.rt);
                $('#rw').val(data.rw);
                $('#agama').val(data.agama);
                $('#status').val(data.status);
                $('#pekerjaan').val(data.pekerjaan);
                $('#hp').val(data.hp);
                $('#formModalTitle').text('Edit Data Warga');
                $('#modalForm').removeClass('hidden');
            }
        });
    });

    // Hapus data
    $(document).on('click', '.btnHapus', function () {
        if (confirm('Yakin ingin menghapus data ini?')) {
            const id = $(this).data('id');
            $.ajax({
                url: 'warga_action.php',
                method: 'POST',
                data: { action: 'delete', id: id },
                success: function (res) {
                    alert(res);
                    loadData();
                }
            });
        }
    });

    // Cetak biodata
    $(document).on('click', '.btnCetak', function () {
        const id = $(this).data('id');
        window.open('warga_cetak.php?id=' + id, '_blank');
    });

    // Import Excel
    $('#formImport').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'import');
        $.ajax({
            url: 'warga_action.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                alert(res);
                loadData();
            }
        });
    });

    // Export Excel
    $('#btnExport').click(function () {
        window.location.href = 'warga_export.php';
    });

    // Dropdown wilayah berantai
    function loadWilayah(url, target, placeholder) {
        $.getJSON(url, function (data) {
            let options = `<option value="">${placeholder}</option>`;
            $.each(data, function (i, item) {
                options += `<option value="${item.id}">${item.nama}</option>`;
            });
            $(target).html(options);
        });
    }

    // Load provinsi
    loadWilayah('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', '#provinsi', 'Pilih Provinsi');

    // Load kota saat provinsi dipilih
    $('#provinsi').change(function () {
        const provID = $(this).val();
        loadWilayah('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + provID + '.json', '#kota', 'Pilih Kota');
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
    });

    // Load kecamatan saat kota dipilih
    $('#kota').change(function () {
        const kotaID = $(this).val();
        loadWilayah('https://www.emsifa.com/api-wilayah-indonesia/api/districts/' + kotaID + '.json', '#kecamatan', 'Pilih Kecamatan');
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
    });

    // Load kelurahan saat kecamatan dipilih
    $('#kecamatan').change(function () {
        const kecID = $(this).val();
        loadWilayah('https://www.emsifa.com/api-wilayah-indonesia/api/villages/' + kecID + '.json', '#kelurahan', 'Pilih Kelurahan');
    });
});
