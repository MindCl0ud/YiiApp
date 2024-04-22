<?php
$servername = "localhost";
$username = "Admin";
$password = "1234Lima!@#";
$dbname = "yiiapp";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>