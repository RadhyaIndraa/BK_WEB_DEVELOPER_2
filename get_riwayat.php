<?php 
include 'config.php';
session_start();

if (!isset($_SESSION['id_dokter'])) {
    header("Location: login.php"); 
    exit;
}

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID pasien dari parameter GET
$id_pasien = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data riwayat periksa pasien
$query = "SELECT 
            periksa.id AS no,
            periksa.tgl_periksa,
            pasien.nama AS nama_pasien,
            dokter.nama AS nama_dokter,
            daftar_poli.keluhan,
            periksa.catatan,
            GROUP_CONCAT(obat.nama_obat SEPARATOR ', ') AS obat,
            periksa.biaya_periksa
          FROM periksa
          JOIN daftar_poli ON periksa.id_daftar_poli = daftar_poli.id
          JOIN pasien ON daftar_poli.id_pasien = pasien.id
          JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id
          JOIN dokter ON jadwal_periksa.id_dokter = dokter.id
          LEFT JOIN detail_periksa ON periksa.id = detail_periksa.id_periksa
          LEFT JOIN obat ON detail_periksa.id_obat = obat.id
          WHERE pasien.id = ?
          GROUP BY periksa.id";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_pasien);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['tgl_periksa']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_pasien']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_dokter']) . "</td>";
        echo "<td>" . htmlspecialchars($row['keluhan']) . "</td>";
        echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";
        echo "<td>" . htmlspecialchars($row['obat']) . "</td>";
        echo "<td>Rp " . number_format($row['biaya_periksa'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>Tidak ada data riwayat periksa</td></tr>";
}

$stmt->close();
$conn->close();
?>
