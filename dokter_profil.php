<?php 
include 'config.php';
session_start();

if (!isset($_SESSION['id_dokter'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$id_dokter = $_SESSION['id_dokter'];


// Ambil data dokter berdasarkan ID
$sql = "SELECT * FROM dokter WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_dokter);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Data dokter tidak ditemukan.");
}

$dokter = $result->fetch_assoc();

// Ambil daftar poli untuk dropdown
$sql_poli = "SELECT * FROM poli";
$result_poli = $conn->query($sql_poli);

// Proses pembaruan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    // Validasi input (bisa ditambah sesuai kebutuhan)
    if (empty($nama) || empty($no_hp) || empty($id_poli)) {
        $error = "Semua field yang bertanda * harus diisi.";
    } else {
        // Update data dokter
        $sql_update = "UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, id_poli = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('ssiii', $nama, $alamat, $no_hp, $id_poli, $id_dokter);

        if ($stmt_update->execute()) {
            $success = "Profil berhasil diperbarui.";
            // Refresh data dokter setelah update
            $stmt->execute();
            $dokter = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
    }
}

$conn->close();
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
                <a href="dokter_riwayat_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-paperclip me-2"></i>Riwayat Pasien</a>
                <a href="dokter_profil.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
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
                    <h2 class="fs-2 m-0">Profil</h2>
                </div>
            </nav>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Profil Dokter</h1>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"> <?= $error; ?> </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"> <?= $success; ?> </div>
                <?php endif; ?>

                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama dan Gelar</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($dokter['nama']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control"><?= htmlspecialchars($dokter['alamat']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="number" id="no_hp" name="no_hp" class="form-control" value="<?= htmlspecialchars($dokter['no_hp']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_poli" class="form-label">Poli</label>
                        <select id="id_poli" name="id_poli" class="form-select" disabled>
                            <option value="">-- Pilih Poli --</option>
                            <?php while ($poli = $result_poli->fetch_assoc()): ?>
                                <option value="<?= $poli['id']; ?>" <?= $dokter['id_poli'] == $poli['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($poli['nama_poli']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="hidden" name="id_poli" value="<?= $dokter['id_poli']; ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
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
