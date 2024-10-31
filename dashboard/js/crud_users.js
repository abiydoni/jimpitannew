let data = []; // Array untuk menyimpan data
let id = 1;

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
                <td class="py-2 px-4 border-b">${item.shift}</td>
                
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
document.getElementById("addBtn").addEventListener("click", () => {
    const name = prompt("Masukkan Nama:");
    const email = prompt("Masukkan Email:");
    if (name && email) {
        data.push({ id: id++, name, email });
        renderTable();
    }
});

// Fungsi untuk mengedit data
function editData(itemId) {
    const item = data.find(d => d.id === itemId);
    if (item) {
        const newName = prompt("Edit Nama:", item.name);
        const newEmail = prompt("Edit Email:", item.email);
        if (newName && newEmail) {
            item.name = newName;
            item.email = newEmail;
            renderTable();
        }
    }
}

// Fungsi untuk menghapus data
function deleteData(itemId) {
    data = data.filter(d => d.id !== itemId);
    renderTable();
}

// Render pertama kali
renderTable();
