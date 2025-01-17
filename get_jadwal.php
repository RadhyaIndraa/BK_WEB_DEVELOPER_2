<?php 
include 'config.php';

// Ambil id_poli dari permintaan
$id_poli = intval($_GET['id_poli']);

// Query untuk mendapatkan jadwal dokter berdasarkan poli
$query_jadwal = "
    SELECT jadwal_periksa.id, dokter.nama AS nama_dokter, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai 
    FROM jadwal_periksa 
    JOIN dokter ON jadwal_periksa.id_dokter = dokter.id 
    WHERE jadwal_periksa.status = 'aktif' AND dokter.id_poli = ?
";
$stmt = $conn->prepare($query_jadwal);
$stmt->bind_param("i", $id_poli);
$stmt->execute();
$result_jadwal = $stmt->get_result();

// Buat opsi untuk select
while ($jadwal = $result_jadwal->fetch_assoc()) {
    echo "<option value=\"{$jadwal['id']}\">{$jadwal['nama_dokter']} - {$jadwal['hari']} ({$jadwal['jam_mulai']} - {$jadwal['jam_selesai']})</option>";
}
?>
