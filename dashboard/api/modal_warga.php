<!-- Modal Tambah/Edit Warga -->
<div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-xl font-semibold" id="modalTitle">Tambah Warga</h2>
      <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
    </div>

    <form id="formWarga" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
      <input type="hidden" name="id" id="id">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">NIK</label>
          <input type="text" name="nik" id="nik" maxlength="16" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">No KK</label>
          <input type="text" name="nokk" id="nokk" maxlength="16" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">Nama Lengkap</label>
          <input type="text" name="nama" id="nama" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">Jenis Kelamin</label>
          <select name="jenkel" id="jenkel" required class="w-full input">
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Tempat Lahir</label>
          <input type="text" name="tpt_lahir" id="tpt_lahir" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">Tanggal Lahir</label>
          <input type="date" name="tgl_lahir" id="tgl_lahir" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">Agama</label>
          <select name="agama" id="agama" required class="w-full input">
            <option value="">Pilih Agama</option>
            <option>Islam</option>
            <option>Kristen</option>
            <option>Katolik</option>
            <option>Hindu</option>
            <option>Buddha</option>
            <option>Konghucu</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Status Perkawinan</label>
          <select name="status" id="status" required class="w-full input">
            <option value="">Pilih Status</option>
            <option>Belum Kawin</option>
            <option>Kawin</option>
            <option>Cerai Hidup</option>
            <option>Cerai Mati</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Pekerjaan</label>
          <input type="text" name="pekerjaan" id="pekerjaan" class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">No HP</label>
          <input type="text" name="hp" id="hp" class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">Hubungan</label>
          <select name="hubungan" id="hubungan" required class="w-full input">
            <option value="">Pilih</option>
            <option>Suami</option>
            <option>Istri</option>
            <option>Anak</option>
            <option>Keluarga Lain</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Upload Foto</label>
          <input type="file" name="foto" id="foto" accept="image/*" class="w-full input">
          <img id="previewFoto" class="mt-2 max-h-32" />
        </div>
        <div>
          <label class="block text-sm font-medium">Alamat (Jalan/Gang)</label>
          <input type="text" name="alamat" id="alamat" required class="w-full input">
        </div>
        <div>
          <label class="block text-sm font-medium">RT/RW</label>
          <div class="flex gap-2">
            <input type="text" name="rt" id="rt" maxlength="3" placeholder="RT" class="input w-1/2">
            <input type="text" name="rw" id="rw" maxlength="3" placeholder="RW" class="input w-1/2">
          </div>
        </div>
      </div>

      <!-- Dropdown Wilayah (Provinsi - Kota - Kecamatan - Kelurahan) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Provinsi</label>
          <select name="provinsi" id="provinsi" required class="w-full input"></select>
        </div>
        <div>
          <label class="block text-sm font-medium">Kabupaten/Kota</label>
          <select name="kota" id="kota" required class="w-full input"></select>
        </div>
        <div>
          <label class="block text-sm font-medium">Kecamatan</label>
          <select name="kecamatan" id="kecamatan" required class="w-full input"></select>
        </div>
        <div>
          <label class="block text-sm font-medium">Kelurahan</label>
          <select name="kelurahan" id="kelurahan" required class="w-full input"></select>
        </div>
      </div>

      <div class="flex justify-end pt-4 border-t">
        <button type="button" onclick="closeModal()" class="btn-secondary mr-2">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Tailwind helper -->
<style>
  .input {
    @apply border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring focus:ring-blue-300;
  }
  .btn-primary {
    @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700;
  }
  .btn-secondary {
    @apply bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400;
  }
</style>
