<?php
include 'db.php'
// Proses simpan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['id_code'];
    $user = $_POST['user_name'];
    $nama = $_POST['name'];
    $pswd = $_POST['password'];
    $jadwal = $_POST['shift'];
    $role1 = $_POST['role'];

    $sql = "INSERT INTO master_kk (nama, alamat) VALUES ('$nama', '$alamat')";
    INSERT INTO `users`(`id_code`, `user_name`, `name`, `password`, `shift`, `role`) VALUES ('$code','$user','$nama','$pswd','$jadwal','$role1')

    if ($pdo->query($sql) === TRUE) {
        echo "Data berhasil ditambahkan";
    } else {
        echo "Error: " . $sql . "<br>" . $pdo->error;
    }
}

// Kembali ke halaman utama
header("Location: index.php");
exit();
?>
