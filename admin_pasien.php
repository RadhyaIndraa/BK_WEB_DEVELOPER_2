<?php
include 'config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = '';
$nama = '';
$alamat = '';
$no_ktp = '';
$no_hp = '';
$no_rm = '';
$is_edit = false;

// Menampilkan data pasien
$query = "SELECT * FROM pasien";
$result = $conn->query($query);
if (!$result) {
    die("Error pada query pasien: " . $conn->error);
}

// Proses tambah atau edit pasien
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $no_rm = $_POST['no_rm'];

    if (isset($_POST['simpan'])) {
        if ($id) {
            // Proses edit
            $update_query = $conn->prepare("UPDATE pasien SET nama = ?, alamat = ?, no_ktp = ?, no_hp = ?, no_rm = ? WHERE id = ?");
            $update_query->bind_param("sssssi", $nama, $alamat, $no_ktp, $no_hp, $no_rm, $id);

            if ($update_query->execute()) {
                header("Location: admin_pasien.php");
                exit;
            } else {
                die("Error pada update query: " . $conn->error);
            }
        } else {
            // Proses tambah
            $insert_query = $conn->prepare("INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)");
            $insert_query->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);

            if ($insert_query->execute()) {
                header("Location: admin_pasien.php"); 
                exit;
            } else {
                die("Error pada insert query: " . $conn->error);
            }
        }
    }
}

// Proses hapus pasien
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_query = $conn->prepare("DELETE FROM pasien WHERE id = ?");
    $delete_query->bind_param("i", $id);

    if ($delete_query->execute()) {
        header("Location: admin_pasien.php");
        exit;
    } else {
        die("Error pada delete query: " . $conn->error);
    }
}

// Proses edit pasien (untuk menampilkan data di form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $select_query = $conn->prepare("SELECT * FROM pasien WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $result_edit = $select_query->get_result();

    if ($row = $result_edit->fetch_assoc()) {
        $nama = $row['nama'];
        $alamat = $row['alamat'];
        $no_ktp = $row['no_ktp'];
        $no_hp = $row['no_hp'];
        $no_rm = $row['no_rm'];
        $is_edit = true;
    }
}
// Generate No Rekam Medis otomatis jika form tambah
if (!$is_edit) {
    $year_month = date('Ym'); // Tahun dan bulan, contoh: 202412
    $last_rm_query = "SELECT no_rm FROM pasien WHERE no_rm LIKE '$year_month-%' ORDER BY id DESC LIMIT 1";
    $last_rm_result = $conn->query($last_rm_query);

    if ($last_rm_result && $last_rm_result->num_rows > 0) {
        $last_rm_row = $last_rm_result->fetch_assoc();
        $last_rm_number = intval(explode('-', $last_rm_row['no_rm'])[1]); // Ambil angka urut terakhir
    } else {
        $last_rm_number = 0; // Jika belum ada pasien pada bulan ini, mulai dari 0
    }

    $new_rm_number = $last_rm_number + 1; // Increment angka urut
    $no_rm = $year_month . '-' . $new_rm_number; // Contoh: 202412-2
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
    <title>Pasien - Poliklinik</title>
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
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="admin_dokter.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-user-md me-2"></i>Dokter</a>
                <a href="admin_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
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
                    <h2 class="fs-2 m-0">Pasien</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <!-- Form Section -->
                <div class="row my-5">
                    <div class="col">
                        <h3 class="fs-4 mb-3">Tambah / Edit Pasien</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>" />
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control" name="nama" id="nama" value="<?= $nama ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" id="alamat"><?= $alamat ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="no_ktp" class="form-label">No KTP</label>
                                <input type="number" class="form-control" name="no_ktp" id="no_ktp" value="<?= $no_ktp ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="number" class="form-control" name="no_hp" id="no_hp" value="<?= $no_hp ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="no_rm" class="form-label">No Rekam Medis</label>
                                <input type="text" class="form-control" name="no_rm" id="no_rm" value="<?= $no_rm ?>" readonly />
                            </div>
                            <button type="submit" class="btn btn-primary" name="simpan"><?= $is_edit ? 'Simpan Perubahan' : 'Tambah Pasien' ?></button>
                            <?php if ($is_edit): ?>
                                <a href="admin_pasien.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <hr>

                <!-- Table Section -->
                <div class="row">
                    <div class="col">
                        <h3>Daftar Pasien</h3>
                        <table class="table bg-white rounded shadow-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No KTP</th>
                                    <th>No HP</th>
                                    <th>No RM</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['nama'] ?></td>
                                        <td><?= $row['alamat'] ?></td>
                                        <td><?= $row['no_ktp'] ?></td>
                                        <td><?= $row['no_hp'] ?></td>
                                        <td><?= $row['no_rm'] ?></td>
                                        <td>
                                            <a href="admin_pasien.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                            <a href="admin_pasien.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
