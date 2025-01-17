<?php
session_start(); // Mulai sesi

// Hancurkan semua sesi yang ada
session_unset();  // Menghapus semua data sesi
session_destroy(); // Menghancurkan sesi

// Redirect ke halaman login setelah logout
header("Location: home.php");
exit;
?>
