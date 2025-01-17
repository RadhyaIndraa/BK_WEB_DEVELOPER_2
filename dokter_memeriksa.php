<?php 
include 'config.php';
session_start();

// Periksa apakah dokter sudah login dan ID dokter tersedia
if (!isset($_SESSION['id_dokter'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$id_dokter = $_SESSION['id_dokter'];

// Menggunakan prepared statement untuk mencegah SQL injection
$stmt = $conn->prepare("
    SELECT dp.id, p.nama AS nama_pasien, dp.keluhan, dp.no_antrian
    FROM daftar_poli dp
    JOIN pasien p ON dp.id_pasien = p.id
    WHERE dp.id_jadwal IN (
        SELECT id FROM jadwal_periksa WHERE id_dokter = ?
    ) AND dp.status = 'Belum diperiksa'
");
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Terjadi kesalahan saat mengambil data: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <title>POLIKLINIK</title>
    <link href='logo/icon.ico' rel='SHORTCUT ICON' />
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom"><i
                    class="fas fa-clinic-medical me-2"></i>POLIKLINIK</div>
            <div class="list-group list-group-flush my-3">
                <a href="dokter_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="dokter_jadwal_periksa.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-paperclip me-2"></i>Jadwal Periksa</a>
                <a href="dokter_memeriksa.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-paperclip me-2"></i>Memeriksa Pasien</a>
                <a href="dokter_riwayat_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-paperclip me-2"></i>Riwayat Pasien</a>
                <a href="dokter_profil.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-paperclip me-2"></i>Profil</a>
                <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i
                        class="fas fa-power-off me-2"></i>Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Jadwal Periksa</h2>
                </div>
        </nav>
            <div class="container-fluid">
                <h1 class="mt-4">Daftar Periksa Pasien</h1>
                <table class="table bg-white rounded shadow-sm table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($no++); ?></td>
                                <td><?= htmlspecialchars($row['nama_pasien']); ?></td>
                                <td><?= htmlspecialchars($row['keluhan']); ?></td>
                                <td>
                                    <a href="dokter_memeriksa_pasien.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Periksa</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>

</html>
