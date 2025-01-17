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

// Ambil data pasien
$query = "SELECT p.id, p.nama AS nama_pasien, p.no_ktp, p.no_hp, p.no_rm 
          FROM pasien p";
$result = $conn->query($query);
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
                <a href="dokter_memeriksa.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-paperclip me-2"></i>Memeriksa Pasien</a>
                <a href="dokter_riwayat_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
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
                    <h2 class="fs-2 m-0">Riwayat Pasien</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Daftar Riwayat Pasien</h1>
                <table class="table bg-white rounded shadow-sm table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>No. KTP</th>
                            <th>No. Telpon</th>
                            <th>No. RM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nama_pasien']; ?></td>
                                    <td><?= $row['no_ktp']; ?></td>
                                    <td><?= $row['no_hp']; ?></td>
                                    <td><?= $row['no_rm']; ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $row['id']; ?>">Detail Riwayat Periksa</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pasien</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Riwayat Periksa -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Riwayat Periksa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Periksa</th>
                                <th>Nama Pasien</th>
                                <th>Nama Dokter</th>
                                <th>Keluhan</th>
                                <th>Catatan</th>
                                <th>Obat</th>
                                <th>Biaya Periksa</th>
                            </tr>
                        </thead>
                        <tbody id="modalContent">
                            <!-- Konten akan dimuat secara dinamis dengan AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const detailModal = document.getElementById('detailModal');

            detailModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const patientId = button.getAttribute('data-id');

                fetch(`get_riwayat.php?id=${patientId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('modalContent').innerHTML = data;
                    });
            });
        });
    </script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>

</html>