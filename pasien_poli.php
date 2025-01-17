<?php 
include 'config.php';
session_start();
$id_pasien = $_SESSION['id_pasien']; 

$query_pasien = "SELECT no_rm FROM pasien WHERE id = ?";
$stmt = $conn->prepare($query_pasien);
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result_pasien = $stmt->get_result();
$pasien = $result_pasien->fetch_assoc();
$no_rm = $pasien['no_rm'];

$query_poli = "SELECT * FROM poli";
$result_poli = $conn->query($query_poli);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'daftar') {
    $id_poli = $_POST['id_poli'];
    $id_jadwal = $_POST['id_jadwal'];
    $keluhan = $_POST['keluhan'];

    $query_antrian = "SELECT MAX(no_antrian) AS last_antrian FROM daftar_poli WHERE id_jadwal = ?";
    $stmt = $conn->prepare($query_antrian);
    $stmt->bind_param("i", $id_jadwal);
    $stmt->execute();
    $result_antrian = $stmt->get_result();
    $data_antrian = $result_antrian->fetch_assoc();
    $no_antrian = $data_antrian['last_antrian'] + 1;

    $query_daftar = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status) VALUES (?, ?, ?, ?, 'Belum diperiksa')";
    $stmt = $conn->prepare($query_daftar);
    $stmt->bind_param("iisi", $id_pasien, $id_jadwal, $keluhan, $no_antrian);

    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil! Nomor antrian Anda: $no_antrian'); window.location.href = 'pasien_poli.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal! Silakan coba lagi.');</script>";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $id_daftar = $_POST['id_daftar'];

    $query_hapus = "DELETE FROM daftar_poli WHERE id = ? AND id_pasien = ?";
    $stmt = $conn->prepare($query_hapus);
    $stmt->bind_param("ii", $id_daftar, $id_pasien);

    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil dihapus!'); window.location.href = 'pasien_poli.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pendaftaran! Silakan coba lagi.');</script>";
    }
}

$query_riwayat = "SELECT daftar_poli.id, daftar_poli.no_antrian, poli.nama_poli, dokter.nama AS nama_dokter, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai, daftar_poli.status 
FROM daftar_poli 
JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id 
JOIN dokter ON jadwal_periksa.id_dokter = dokter.id 
JOIN poli ON dokter.id_poli = poli.id 
WHERE daftar_poli.id_pasien = ?";
$stmt = $conn->prepare($query_riwayat);
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result_riwayat = $stmt->get_result();
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
    <title>Pendaftaran Poli</title>
    <link href='logo/icon.ico' rel='SHORTCUT ICON' />
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
                <i class="fas fa-clinic-medical me-2"></i>POLIKLINIK
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="pasien_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="pasien_poli.php" class="list-group-item list-group-item-action bg-transparent second-text active">
                    <i class="fas fa-paperclip me-2"></i>Poli
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold">
                    <i class="fas fa-power-off me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Jadwal Periksa</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <h1>Pendaftaran Poli</h1>

                <form action="" method="post">
                    <input type="hidden" name="action" value="daftar">
                    <div class="mb-3">
                        <label for="no_rm" class="form-label">No Rekam Medis:</label>
                        <input type="text" class="form-control" name="no_rm" id="no_rm" value="<?= $no_rm ?>" readonly />
                    </div>
                    <div class="mb-3">
                        <label for="id_poli" class="form-label">Pilih Poli:</label>
                        <select name="id_poli" id="id_poli" class="form-select" onchange="loadJadwalDokter(this.value)" required>
                            <option value="">-- Pilih Poli --</option>
                            <?php while ($poli = $result_poli->fetch_assoc()): ?>
                                <option value="<?php echo $poli['id']; ?>"><?php echo $poli['nama_poli']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_jadwal" class="form-label">Pilih Jadwal Dokter:</label>
                        <select name="id_jadwal" id="id_jadwal" class="form-select" required>
                            <option value="">-- Pilih Jadwal --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keluhan" class="form-label">Keluhan:</label>
                        <textarea name="keluhan" id="keluhan" rows="4" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Daftar</button>
                </form>

                <h2 class="mt-5">Riwayat Daftar Poli</h2>
                <table class="table bg-white rounded shadow-sm table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Antrian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($riwayat = $result_riwayat->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $riwayat['nama_poli']; ?></td>
                                <td><?php echo $riwayat['nama_dokter']; ?></td>
                                <td><?php echo $riwayat['hari']; ?></td>
                                <td><?php echo $riwayat['jam_mulai']; ?></td>
                                <td><?php echo $riwayat['jam_selesai']; ?></td>
                                <td><?php echo $riwayat['no_antrian']; ?></td>
                                <td><?php echo $riwayat['status']; ?></td>
                                <td>
                                    <?php if ($riwayat['status'] == 'Sudah diperiksa'): ?>
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalRiwayat<?php echo $riwayat['id']; ?>">Riwayat</button>
                                    <?php else: ?>
                                        <span class="text-muted"></span>
                                    <?php endif; ?>
                                    <form action="" method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="hapus">
                                        <input type="hidden" name="id_daftar" value="<?php echo $riwayat['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?');">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Riwayat -->
                            <div class="modal fade" id="modalRiwayat<?php echo $riwayat['id']; ?>" tabindex="-1" aria-labelledby="modalLabelRiwayat<?php echo $riwayat['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabelRiwayat<?php echo $riwayat['id']; ?>">Riwayat Periksa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            $query_detail = "SELECT periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa, 
                                                             GROUP_CONCAT(obat.nama_obat SEPARATOR ', ') AS daftar_obat
                                                             FROM periksa 
                                                             LEFT JOIN detail_periksa ON periksa.id = detail_periksa.id_periksa
                                                             LEFT JOIN obat ON detail_periksa.id_obat = obat.id
                                                             WHERE periksa.id_daftar_poli = ?";
                                            $stmt_detail = $conn->prepare($query_detail);
                                            $stmt_detail->bind_param("i", $riwayat['id']);
                                            $stmt_detail->execute();
                                            $result_detail = $stmt_detail->get_result();
                                            $detail = $result_detail->fetch_assoc();
                                            ?>
                                            <p><strong>Poli:</strong> <?php echo $riwayat['nama_poli']; ?></p>
                                            <p><strong>Dokter:</strong> <?php echo $riwayat['nama_dokter']; ?></p>
                                            <p><strong>Hari:</strong> <?php echo $riwayat['hari']; ?></p>
                                            <p><strong>Jam:</strong> <?php echo $riwayat['jam_mulai'] . ' - ' . $riwayat['jam_selesai']; ?></p>
                                            <hr>
                                            <p><strong>Tanggal Periksa:</strong> <?php echo $detail['tgl_periksa']; ?></p>
                                            <p><strong>Catatan:</strong> <?php echo $detail['catatan']; ?></p>
                                            <p><strong>Daftar Obat:</strong> <?php echo $detail['daftar_obat']; ?></p>
                                            <p><strong>Biaya Periksa:</strong> Rp <?php echo number_format($detail['biaya_periksa'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
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

        function loadJadwalDokter(idPoli) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_jadwal.php?id_poli=' + idPoli, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    document.getElementById('id_jadwal').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
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
