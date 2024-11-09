<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_code = $_POST['id_code'];
    $user_name = $_POST['user_name'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // enkripsi password
    $shift = $_POST['shift'];
    $role = $_POST['role'];

    // SQL untuk memasukkan data
    $sql = "INSERT INTO users (id_code, user_name, name, password, shift, role) VALUES (:id_code, :user_name, :name, :password, :shift, :role)";

    $stmt = $pdo->prepare($sql);

    // Eksekusi dan bind data
    $stmt->bindParam(':id_code', $id_code);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':shift', $shift);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        header("Location: ../jadwal.php"); // Mengarahkan ke jadwal.php setelah berhasil
        exit(); // Menghentikan eksekusi script setelah pengalihan
    } else {
        echo "Gagal menyimpan data.";
    }
}
?>
