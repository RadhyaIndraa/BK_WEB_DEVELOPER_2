<?php 
include 'config.php';
session_start();

if (!isset($_SESSION['id_dokter'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$id_dokter = $_SESSION['id_dokter'];
$id_daftar_poli = (int)$_GET['id'];

// Periksa apakah pasien sudah diperiksa
$query_check_status = "SELECT status FROM daftar_poli WHERE id = ?";
$stmt = $conn->prepare($query_check_status);
$stmt->bind_param("i", $id_daftar_poli);
$stmt->execute();
$result = $stmt->get_result();
$status_data = $result->fetch_assoc();

if ($status_data['status'] === 'Sudah diperiksa') {
    die("Pasien ini sudah diperiksa.");
}

// Ambil data pasien berdasarkan ID daftar poli
$query = "SELECT dp.keluhan, p.nama FROM daftar_poli dp
          JOIN pasien p ON dp.id_pasien = p.id
          WHERE dp.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_daftar_poli);
$stmt->execute();
$pasien_result = $stmt->get_result();

if ($pasien_result->num_rows > 0) {
    $pasien = $pasien_result->fetch_assoc();
} else {
    die("Pasien tidak ditemukan.");
}

// Ambil daftar obat
$query_obat = "SELECT * FROM obat";
$obat_result = $conn->query($query_obat);

if (!$obat_result) {
    die("Terjadi kesalahan saat mengambil data obat: " . $conn->error);
}

// Proses form jika ada pengiriman data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_periksa = $conn->real_escape_string($_POST['tgl_periksa']);
    $catatan = $conn->real_escape_string($_POST['catatan']);
    $obat_ids = $_POST['obat'];
    $total_harga = 150000;

    // Hitung total biaya berdasarkan harga obat yang dipilih
    foreach ($obat_ids as $obat_id) {
        $obat_query = "SELECT harga FROM obat WHERE id = ?";
        $stmt_obat = $conn->prepare($obat_query);
        $stmt_obat->bind_param("i", $obat_id);
        $stmt_obat->execute();
        $harga_obat = $stmt_obat->get_result()->fetch_assoc()['harga'];
        $total_harga += $harga_obat;
    }

    // Simpan data ke tabel periksa
    $insert_query = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) 
                     VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("issi", $id_daftar_poli, $tgl_periksa, $catatan, $total_harga);

    if ($stmt->execute()) {
        $id_periksa = $stmt->insert_id;

        // Simpan detail obat yang diberikan ke pasien
        foreach ($obat_ids as $obat_id) {
            $insert_obat_query = "INSERT INTO detail_periksa (id_periksa, id_obat) 
                                  VALUES (?, ?)";
            $stmt_obat = $conn->prepare($insert_obat_query);
            $stmt_obat->bind_param("ii", $id_periksa, $obat_id);
            $stmt_obat->execute();
        }

        // Update status pasien menjadi "Sudah diperiksa"
        $stmt_status = $conn->prepare("UPDATE daftar_poli SET status = 'Sudah diperiksa' WHERE id = ?");
        $stmt_status->bind_param("i", $id_daftar_poli);
        $stmt_status->execute();

        if ($stmt_status->affected_rows > 0) {
            echo "<script>alert('Pemeriksaan selesai!'); window.location.href = 'dokter_memeriksa.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui status.');</script>";
        }
    } else {
        die("Terjadi kesalahan saat menyimpan data pemeriksaan: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    class="fas fa-user-secret me-2"></i>POLIKLINIK</div>
            <div class="list-group list-group-flush my-3">
                <a href="dokter_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-clinic-medical me-2"></i>Dashboard</a>
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

            <div class="container-fluid px-4">
                <h3 class="mb-4">Periksa Pasien</h3>
                <p>Nama Pasien: <input type="text" class="form-control" value="<?= htmlspecialchars($pasien['nama']) ?>" readonly></p>
                <p>Keluhan: <input type="text" class="form-control" value="<?= htmlspecialchars($pasien['keluhan']) ?>" readonly></p>

                <form method="post">
                    <div class="mb-3">
                        <label for="tgl_periksa" class="form-label">Tanggal Periksa:</label>
                        <input type="date" id="tgl_periksa" name="tgl_periksa" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan:</label>
                        <textarea id="catatan" name="catatan" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="id_obat" class="form-label">Obat:</label>
                        <select id="id_obat" name="obat[]" class="form-select" multiple="multiple" style="width: 100%;" required>
                            <?php while ($obat = $obat_result->fetch_assoc()) { ?>
                                <option value="<?= htmlspecialchars($obat['id']) ?>" data-harga="<?= htmlspecialchars($obat['harga']) ?>">
                                    <?= htmlspecialchars($obat['nama_obat']) ?> (Rp<?= number_format($obat['harga'], 0, ',', '.') ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="harga" class="form-label">Total Harga:</label>
                        <input type="text" id="harga" name="harga" class="form-control" value="150000" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#id_obat').select2({
                placeholder: "Pilih obat",
                allowClear: true
            });

            $('#id_obat').on('change', function() {
                let baseHarga = 150000;
                let totalHarga = baseHarga;

                $('#id_obat option:selected').each(function() {
                    totalHarga += parseInt($(this).data('harga'));
                });

                $('#harga').val(totalHarga);
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
