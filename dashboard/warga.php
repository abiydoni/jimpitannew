<?php
session_start();
include 'header.php'; // Pastikan header.php memuat JQuery dan Select2
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}
if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
include 'api/db.php'; // Koneksi database
?>

<div class="table-data">
    <div class="order overflow-x-auto">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700 transition duration-300">
                <i class='bx bx-plus-medical mr-2'></i>Tambah Warga
            </button>
        </div>
        <table id="dataWargaTable" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
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
                </tbody>
        </table>
    </div>
</div>

<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 px-2">
    <div class="bg-white p-4 rounded-lg w-full max-w-md relative overflow-y-auto max-h-[90vh] text-sm shadow-xl">
        <h2 class="text-lg font-semibold mb-3" id="modalTitle">Form Warga</h2>
        <form id="formWarga" class="space-y-3">
            <input type="hidden" name="id_warga" id="id_warga">
            <input type="hidden" name="aksi" value="save">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="kode" id="kode" placeholder="Kode: RT0700001" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" required readonly>
                <input type="text" name="nama" id="nama" placeholder="Nama" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" required>
                <input type="text" name="nik" id="nik" placeholder="NIK" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" required pattern="\d{16}" title="NIK harus 16 digit angka">
                <select name="hubungan" id="hubungan" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Saudara Lain">Saudara Lain</option>
                </select>
                <input type="text" name="nikk" id="nikk" placeholder="NIK KK" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                <select name="jenkel" id="jenkel" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <input type="text" name="tpt_lahir" id="tpt_lahir" placeholder="Tempat Lahir" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                <input type="date" name="tgl_lahir" id="tgl_lahir" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" value="<?= date('Y-m-d') ?>">
                <textarea name="alamat" id="alamat" placeholder="Alamat: Jl..." class="border p-1.5 rounded col-span-1 sm:col-span-2 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                <input type="number" name="rt" id="rt" placeholder="RT" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" value="0" min="0">
                <input type="number" name="rw" id="rw" placeholder="RW" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" value="0" min="0">
                <input type="hidden" name="negara" id="negara" value="Indonesia">
                <select id="propinsi" name="propinsi" class="selectWilayah border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Provinsi</option>
                </select>
                <select id="kota" name="kota" class="selectWilayah border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kota/Kabupaten</option>
                </select>
                <select id="kecamatan" name="kecamatan" class="selectWilayah border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kecamatan</option>
                </select>
                <select id="kelurahan" name="kelurahan" class="selectWilayah border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kelurahan/Desa</option>
                </select>
                <select name="agama" id="agama" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Budha">Budha</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <select name="status" id="status" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="K">Kawin</option>
                    <option value="TK">Tidak Kawin</option>
                    <option value="J">Janda</option>
                    <option value="D">Duda</option>
                    <option value="P">Pelajar</option>
                    <option value="L">Lainnya</option>
                </select>
                <select name="pekerjaan" id="pekerjaan" class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="PNS">PNS</option>
                    <option value="Swasta">Karyawan Swasta</option>
                    <option value="Wirausaha">Wirausaha</option>
                    <option value="Pelajar">Pelajar</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <input type="tel" name="hp" id="hp" placeholder="Nomor HP: 08..." class="border p-1.5 rounded text-sm focus:ring-blue-500 focus:border-blue-500" pattern="[0-9]{10,15}" title="Nomor HP harus berupa angka, 10-15 digit">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-3 py-1.5 border rounded text-sm hover:bg-gray-100 transition duration-300">Batal</button>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition duration-300">
                    <span id="saveButtonText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; // Pastikan footer.php memuat DataTables JS dan CSS ?>

<script>
    let dataTable; // Variabel global untuk DataTables

    $(document).ready(function() {
        // Inisialisasi DataTables
        dataTable = $('#dataWargaTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "api/warga_action.php",
                "type": "POST",
                "data": { aksi: 'read_datatable' } // Aksi baru untuk DataTables server-side
            },
            "columns": [
                { "data": "no", "orderable": false, "searchable": false },
                { "data": "nama" },
                { "data": "nik" },
                { "data": "jenkel" },
                { "data": "ttl" },
                { "data": "alamat" },
                { "data": "pekerjaan" },
                { "data": "hp" },
                { "data": "aksi", "orderable": false, "searchable": false }
            ],
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });

        // Inisialisasi Select2
        $('.selectWilayah').select2({
            width: '100%',
            dropdownParent: $('#modal'), // Penting untuk modal
            placeholder: 'Pilih opsi',
            allowClear: true
        });

        // Load provinsi
        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function(data) {
            $.each(data, function(i, provinsi) {
                $('#propinsi').append($('<option>', {
                    value: provinsi.id,
                    text: provinsi.name
                }));
            });
        });

        // Load kota/kabupaten saat provinsi dipilih
        $('#propinsi').on('change', function() {
            var provinsiId = $(this).val();
            $('#kota').val(null).trigger('change'); // Reset Select2
            $('#kecamatan').val(null).trigger('change'); // Reset Select2
            $('#kelurahan').val(null).trigger('change'); // Reset Select2

            $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>');
            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');

            if (provinsiId) {
                $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/" + provinsiId + ".json", function(data) {
                    $.each(data, function(i, kota) {
                        $('#kota').append($('<option>', {
                            value: kota.id,
                            text: kota.name
                        }));
                    });
                }).fail(function() {
                    console.error("Error loading kota/kabupaten.");
                });
            }
        });

        // Load kecamatan saat kota dipilih
        $('#kota').on('change', function() {
            var kotaId = $(this).val();
            $('#kecamatan').val(null).trigger('change'); // Reset Select2
            $('#kelurahan').val(null).trigger('change'); // Reset Select2

            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');

            if (kotaId) {
                $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/" + kotaId + ".json", function(data) {
                    $.each(data, function(i, kec) {
                        $('#kecamatan').append($('<option>', {
                            value: kec.id,
                            text: kec.name
                        }));
                    });
                }).fail(function() {
                    console.error("Error loading kecamatan.");
                });
            }
        });

        // Load kelurahan saat kecamatan dipilih
        $('#kecamatan').on('change', function() {
            var kecamatanId = $(this).val();
            $('#kelurahan').val(null).trigger('change'); // Reset Select2

            $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');

            if (kecamatanId) {
                $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/villages/" + kecamatanId + ".json", function(data) {
                    $.each(data, function(i, kel) {
                        $('#kelurahan').append($('<option>', {
                            value: kel.name, // Penting: API EMSIFA mengembalikan nama untuk desa/kelurahan
                            text: kel.name
                        }));
                    });
                }).fail(function() {
                    console.error("Error loading kelurahan.");
                });
            }
        });

        // Event submit form
        $('#formWarga').on('submit', function(e) {
            e.preventDefault();
            $('#saveButtonText').text('Menyimpan...'); // Indikator loading
            $('button[type="submit"]').prop('disabled', true); // Nonaktifkan tombol

            $.post('api/warga_action.php', $(this).serialize(), function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    dataTable.ajax.reload(); // Refresh DataTables
                    closeModal();
                    $('#formWarga')[0].reset();
                    $('#id_warga').val('');
                    // Reset Select2 wilayah
                    $('.selectWilayah').val(null).trigger('change');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }, 'json') // Pastikan respons diinterpretasikan sebagai JSON
            .fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data: ' + textStatus, 'error');
                console.error("AJAX Error: ", textStatus, errorThrown, jqXHR.responseText);
            })
            .always(function() {
                $('#saveButtonText').text('Simpan'); // Kembalikan teks tombol
                $('button[type="submit"]').prop('disabled', false); // Aktifkan kembali tombol
            });
        });
    });

    function editData(id) {
        $('#modalTitle').text('Edit Data Warga');
        $.post('api/warga_action.php', { aksi: 'get', id: id }, function(response) {
            if (response.status === 'success') {
                const obj = response.data;

                // Isi field biasa
                for (let key in obj) {
                    $('#' + key).val(obj[key]);
                }
                
                // Isi dropdown wilayah secara berurutan dan tungggu hingga terisi
                // Reset Select2 terlebih dahulu
                $('#propinsi, #kota, #kecamatan, #kelurahan').val(null).trigger('change');

                // Load dan set Provinsi
                $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function(provinsiData) {
                    $('#propinsi').html('<option value="">Pilih Provinsi</option>');
                    $.each(provinsiData, function(i, p) {
                        $('#propinsi').append(`<option value="${p.id}">${p.name}</option>`);
                    });
                    if (obj.propinsi) {
                        $('#propinsi').val(obj.propinsi).trigger('change');
                    }
                }).done(function() {
                    // Setelah provinsi terisi, load dan set Kota/Kabupaten
                    if (obj.propinsi && obj.kota) {
                        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/" + obj.propinsi + ".json", function(kotaData) {
                            $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>');
                            $.each(kotaData, function(i, k) {
                                $('#kota').append(`<option value="${k.id}">${k.name}</option>`);
                            });
                            $('#kota').val(obj.kota).trigger('change');
                        }).done(function() {
                            // Setelah kota terisi, load dan set Kecamatan
                            if (obj.kota && obj.kecamatan) {
                                $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/" + obj.kota + ".json", function(kecData) {
                                    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
                                    $.each(kecData, function(i, kc) {
                                        $('#kecamatan').append(`<option value="${kc.id}">${kc.name}</option>`);
                                    });
                                    $('#kecamatan').val(obj.kecamatan).trigger('change');
                                }).done(function() {
                                    // Setelah kecamatan terisi, load dan set Kelurahan/Desa
                                    if (obj.kecamatan && obj.kelurahan) {
                                        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/villages/" + obj.kecamatan + ".json", function(kelData) {
                                            $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>');
                                            $.each(kelData, function(i, kel) {
                                                $('#kelurahan').append(`<option value="${kel.name}">${kel.name}</option>`);
                                            });
                                            $('#kelurahan').val(obj.kelurahan).trigger('change');
                                        }).fail(function() { console.error("Error loading kelurahan on edit."); });
                                    }
                                }).fail(function() { console.error("Error loading kecamatan on edit."); });
                            }
                        }).fail(function() { console.error("Error loading kota on edit."); });
                    }
                }).fail(function() { console.error("Error loading provinsi on edit."); });

                openModal();
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data: ' + textStatus, 'error');
            console.error("AJAX Error: ", textStatus, errorThrown, jqXHR.responseText);
        });
    }

    function hapusData(id) {
        Swal.fire({
            title: 'Yakin?',
            text: "Data ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('api/warga_action.php', { aksi: 'delete', id: id }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Dihapus!', response.message, 'success');
                        dataTable.ajax.reload(); // Refresh DataTables
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }, 'json')
                .fail(function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data: ' + textStatus, 'error');
                    console.error("AJAX Error: ", textStatus, errorThrown, jqXHR.responseText);
                });
            }
        });
    }

    function openModal() {
        $('#modal').removeClass('hidden').addClass('flex');
        $('#modalTitle').text('Tambah Data Warga');

        // Reset form
        $('#formWarga')[0].reset();
        $('#id_warga').val('');
        $('#tgl_lahir').val('<?= date('Y-m-d') ?>'); // Set default tanggal
        $('#rt').val('0'); // Set default RT
        $('#rw').val('0'); // Set default RW

        // Reset Select2 wilayah
        $('.selectWilayah').val(null).trigger('change');

        // Dapatkan kode warga baru
        $.post('api/warga_action.php', { aksi: 'kode' }, function(response) {
            if (response.status === 'success') {
                $('#kode').val(response.kode);
            } else {
                Swal.fire('Error!', response.message, 'error');
                $('#kode').val('ERROR'); // Indikasi error
            }
        }, 'json').fail(function() {
            Swal.fire('Error!', 'Gagal mengambil kode warga.', 'error');
            $('#kode').val('ERROR');
        });
    }

    function closeModal() {
        $('#modal').addClass('hidden').removeClass('flex');
        // Pastikan Select2 juga di-reset saat modal ditutup
        $('.selectWilayah').val(null).trigger('change');
    }
</script>