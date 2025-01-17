<?php
include 'config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil jumlah dokter
$result_dokter = $conn->query("SELECT COUNT(*) as jumlah_dokter FROM dokter");
$row_dokter = $result_dokter->fetch_assoc();
$jumlah_dokter = $row_dokter['jumlah_dokter'];

// Ambil jumlah pasien
$result_pasien = $conn->query("SELECT COUNT(*) as jumlah_pasien FROM pasien");
$row_pasien = $result_pasien->fetch_assoc();
$jumlah_pasien = $row_pasien['jumlah_pasien'];

// Ambil jumlah poli
$result_poli = $conn->query("SELECT COUNT(*) as jumlah_poli FROM poli");
$row_poli = $result_poli->fetch_assoc();
$jumlah_poli = $row_poli['jumlah_poli'];

// Ambil jumlah obat
$result_obat = $conn->query("SELECT COUNT(*) as jumlah_obat FROM obat");
$row_obat = $result_obat->fetch_assoc();
$jumlah_obat = $row_obat['jumlah_obat'];

// Ambil daftar pasien terbaru
$result_pasien_terbaru = $conn->query("SELECT * FROM pasien ORDER BY id DESC LIMIT 10");
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
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="admin_dokter.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-user-md me-2"></i>Dokter</a>
                <a href="admin_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-users me-2"></i>Pasien</a>
                <a href="admin_poli.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-hospital me-2"></i>Poli</a>
                <a href="admin_obat.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-pills me-2"></i>Obat</a>
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
                    <h2 class="fs-2 m-0">Dashboard</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row g-3 my-2">
                    <!-- Dokter -->
                    <div class="col-md-3">
                    <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                        <div>
                            <h3 class="fs-2"><?php echo $jumlah_dokter; ?></h3>
                            <p class="fs-5">Dokter</p>
                        </div>
                        <!-- Ikon Dokter -->
                        <i class="fas fa-user-md fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                        <div>
                            <h3 class="fs-2"><?php echo $jumlah_pasien; ?></h3>
                            <p class="fs-5">Pasien</p>
                        </div>
                        <!-- Ikon Pasien -->
                        <i class="fas fa-users fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                        <div>
                            <h3 class="fs-2"><?php echo $jumlah_poli; ?></h3>
                            <p class="fs-5">Poli</p>
                        </div>
                        <!-- Ikon Poli -->
                        <i class="fas fa-clinic-medical fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                        <div>
                            <h3 class="fs-2"><?php echo $jumlah_obat; ?></h3>
                            <p class="fs-5">Obat</p>
                        </div>
                        <!-- Ikon Obat -->
                        <i class="fas fa-pills fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>


                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Pasien Terbaru</h3>
                    <div class="col">
                        <table class="table bg-white rounded shadow-sm  table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Nama Pasien</th>
                                    <th scope="col">No. KTP</th>
                                    <th scope="col">No. HP</th>
                                    <th scope="col">No. RM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_pasien_terbaru->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo $row['no_ktp']; ?></td>
                                        <td><?php echo $row['no_hp']; ?></td>
                                        <td><?php echo $row['no_rm']; ?></td>
                                        
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
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

<?php
// Tutup koneksi
$conn->close();
?>
