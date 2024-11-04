function editUser(id, idCode, userName, name, shift, role) {
    document.getElementById('userId').value = id;
    document.getElementById('idCode').value = idCode;
    document.getElementById('userName').value = userName;
    document.getElementById('name').value = name;
    document.getElementById('password').value = ""; // Kosongkan password saat edit
    document.getElementById('shift').value = shift;
    document.getElementById('role').value = role;
    document.getElementById('formTitle').innerText = "Edit Pengguna";
}

function cancelEdit() {
    document.getElementById('userId').value = "";
    document.getElementById('idCode').value = "";
    document.getElementById('userName').value = "";
    document.getElementById('name').value = "";
    document.getElementById('password').value = "";
    document.getElementById('shift').value = "";
    document.getElementById('role').value = "";
    document.getElementById('formTitle').innerText = "Tambah Pengguna";
}
