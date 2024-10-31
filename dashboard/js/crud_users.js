let data = []; // Array untuk menyimpan data
let id = 1;
let editId = null; // Menyimpan ID data yang sedang diedit

// Ambil elemen modal dan input
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const nameInput = document.getElementById("nameInput");
const emailInput = document.getElementById("emailInput");
const addBtn = document.getElementById("addBtn");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

// Fungsi untuk menampilkan atau menyembunyikan modal
function toggleModal(show) {
    modal.classList.toggle("hidden", !show);
    if (!show) {
        nameInput.value = "";
        emailInput.value = "";
        editId = null;
    }
}

// Fungsi untuk render data ke tabel
function renderTable() {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = ""; // Bersihkan tabel

    data.forEach((item, index) => {
        const row = `
            <tr>
                <td class="py-2 px-4 border-b">${index + 1}</td>
                <td class="py-2 px-4 border-b">${item.id_code}</td>
                <td class="py-2 px-4 border-b">${item.name}</td>
                <td class="py-2 px-4 border-b">${item.sift}</td>
                <td class="py-2 px-4 border-b">
                    <button onclick="editData(${item.id})" class="bg-yellow-500 text-white px-2 py-1 rounded mr-2">Edit</button>
                    <button onclick="deleteData(${item.id})" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Fungsi untuk menambah data
addBtn.addEventListener("click", () => {
    modalTitle.textContent = "Tambah Data";
    toggleModal(true);
});

// Fungsi untuk menyimpan data (baru atau edit)
saveBtn.addEventListener("click", () => {
    const name = nameInput.value;
    const email = emailInput.value;

    if (name && email) {
        if (editId === null) {
            // Tambah data baru
            data.push({ id: id++, name, email });
        } else {
            // Simpan data hasil edit
            const item = data.find(d => d.id === editId);
            item.name = name;
            item.email = email;
        }

        toggleModal(false);
        renderTable();
    }
});

// Fungsi untuk mengedit data
function editData(itemId) {
    const item = data.find(d => d.id === itemId);
    if (item) {
        editId = item.id;
        modalTitle.textContent = "Edit Data";
        nameInput.value = item.name;
        emailInput.value = item.email;
        toggleModal(true);
    }
}

// Fungsi untuk menghapus data
function deleteData(itemId) {
    data = data.filter(d => d.id !== itemId);
    renderTable();
}

// Fungsi untuk menutup modal
cancelBtn.addEventListener("click", () => toggleModal(false));

// Render pertama kali
renderTable();
