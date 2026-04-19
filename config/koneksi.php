<?php
$host = "localhost";
$user = "root";       // Username default MariaDB
$pass = "";           // Kosongkan jika tidak pakai password, atau isi jika MariaDB Anda memiliki password
$db   = "db_kesehatan";

// Tanda komentar sudah dihapus sehingga koneksi aktif
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
