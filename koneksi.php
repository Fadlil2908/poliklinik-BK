<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'poliklinik';

// Membuat koneksi ke database
$mysqli = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
?>
