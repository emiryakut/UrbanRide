<?php
$conn = mysqli_connect("localhost", "root", "", "urbandrive_db");
if (!$conn) { die("Bağlantı koptu: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8");
?>