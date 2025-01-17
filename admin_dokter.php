<?php
include 'config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = '';
$nama = '';
$alamat = '';
$no_hp = '';
$id_poli = '';
$is_edit = false;

// Menampilkan data dokter
$query = "SELECT dokter.id, dokter.nama, dokter.alamat, dokter.no_hp, poli.nama_poli AS poli 
          FROM dokter 
          JOIN poli ON dokter.id_poli = poli.id";
$result = $conn->query($query);
if (!$result) {
    die("Error pada query dokter: " . $conn->error);
}

// Mengambil data poli untuk dropdown
$poli_query = "SELECT * FROM poli";
$poli_result = $conn->query($poli_query);
if (!$poli_result) {
    die("Error pada query poli: " . $conn->error);
}

// Proses tambah atau edit dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    if (isset($_POST['simpan'])) {
        if ($id) {
            // Proses edit
            $update_query = $conn->prepare("UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, id_poli = ? WHERE id = ?");
            $update_query->bind_param("sssii", $nama, $alamat, $no_hp, $id_poli, $id);

            if ($update_query->execute()) {
                header("Location: admin_dokter.php"); 
                exit;
            } else {
                die("Error pada update query: " . $conn->error);
            }
        } else {
            // Proses tambah
            $insert_query = $conn->prepare("INSERT INTO dokter (nama, alamat, no_hp, id_poli) VALUES (?, ?, ?, ?)");
            $insert_query->bind_param("sssi", $nama, $alamat, $no_hp, $id_poli);

            if ($insert_query->execute()) {
                header("Location: admin_dokter.php");
                exit;
            } else {
                die("Error pada insert query: " . $conn->error);
            }
        }
    }
}

// Proses hapus dokter
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_query = $conn->prepare("DELETE FROM dokter WHERE id = ?");
    $delete_query->bind_param("i", $id);

    if ($delete_query->execute()) {
        header("Location: admin_dokter.php"); 
        exit;
    } else {
        die("Error pada delete query: " . $conn->error);
    }
}

// Proses edit dokter (untuk menampilkan data di form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $select_query = $conn->prepare("SELECT * FROM dokter WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $result_edit = $select_query->get_result();

    if ($row = $result_edit->fetch_assoc()) {
        $nama = $row['nama'];
        $alamat = $row['alamat'];
        $no_hp = $row['no_hp'];
        $id_poli = $row['id_poli'];
        $is_edit = true;
    }
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
    <title>Dokter - Poliklinik</title>
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
                        class="fas fa-clinic-medical me-2"></i>Dashboard</a>
                <a href="admin_dokter.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
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
                    <h2 class="fs-2 m-0">Dokter</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <!-- Form Section -->
                <div class="row my-5">
                    <div class="col">
                        <h3 class="fs-4 mb-3">Tambah / Edit Dokter</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>" />
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Dokter</label>
                                <input type="text" class="form-control" name="nama" id="nama" value="<?= $nama ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" id="alamat" value="<?= $alamat ?>" />
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" name="no_hp" id="no_hp" value="<?= $no_hp ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="id_poli" class="form-label">Poli</label>
                                <select class="form-select" name="id_poli" id="id_poli" required>
                                    <?php while ($poli = $poli_result->fetch_assoc()): ?>
                                        <option value="<?= $poli['id'] ?>" <?= $poli['id'] == $id_poli ? 'selected' : '' ?>>
                                            <?= $poli['nama_poli'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="simpan"><?= $is_edit ? 'Simpan Perubahan' : 'Tambah Dokter' ?></button>
                            <?php if ($is_edit): ?>
                                <a href="admin_dokter.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <hr>

                <!-- Table Section -->
                <div class="row">
                    <div class="col">
                        <h3>Daftar Dokter</h3>
                        <table class="table bg-white rounded shadow-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No HP</th>
                                    <th>Poli</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['nama'] ?></td>
                                        <td><?= $row['alamat'] ?></td>
                                        <td><?= $row['no_hp'] ?></td>
                                        <td><?= $row['poli'] ?></td>
                                        <td>
                                            <a href="admin_dokter.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                            <a href="admin_dokter.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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
