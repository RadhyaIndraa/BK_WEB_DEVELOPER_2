<?php
include 'config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = '';
$nama_obat = '';
$kemasan = '';
$harga = '';
$is_edit = false;

// Menampilkan data obat
$query = "SELECT * FROM obat";
$result = $conn->query($query);
if (!$result) {
    die("Error pada query obat: " . $conn->error);
}

// Proses tambah atau edit obat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    if (isset($_POST['simpan'])) {
        if ($id) {
            // Proses edit
            $update_query = $conn->prepare("UPDATE obat SET nama_obat = ?, kemasan = ?, harga = ? WHERE id = ?");
            $update_query->bind_param("ssii", $nama_obat, $kemasan, $harga, $id);

            if ($update_query->execute()) {
                header("Location: admin_obat.php");
                exit;
            } else {
                die("Error pada update query: " . $conn->error);
            }
        } else {
            // Proses tambah
            $insert_query = $conn->prepare("INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)");
            $insert_query->bind_param("ssi", $nama_obat, $kemasan, $harga);

            if ($insert_query->execute()) {
                header("Location: admin_obat.php");
                exit;
            } else {
                die("Error pada insert query: " . $conn->error);
            }
        }
    }
}

// Proses hapus obat
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_query = $conn->prepare("DELETE FROM obat WHERE id = ?");
    $delete_query->bind_param("i", $id);

    if ($delete_query->execute()) {
        header("Location: admin_obat.php");
        exit;
    } else {
        die("Error pada delete query: " . $conn->error);
    }
}

// Proses edit obat (untuk menampilkan data di form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $select_query = $conn->prepare("SELECT * FROM obat WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $result_edit = $select_query->get_result();

    if ($row = $result_edit->fetch_assoc()) {
        $nama_obat = $row['nama_obat'];
        $kemasan = $row['kemasan'];
        $harga = $row['harga'];
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
    <title>Obat - Poliklinik</title>
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
                <a href="admin_pasien.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-users me-2"></i>Pasien</a>
                <a href="admin_poli.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-hospital me-2"></i>Poli</a>
                <a href="admin_obat.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
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
                    <h2 class="fs-2 m-0">Obat</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <!-- Form Section -->
                <div class="row my-5">
                    <div class="col">
                        <h3 class="fs-4 mb-3">Tambah / Edit Obat</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>" />
                            <div class="mb-3">
                                <label for="nama_obat" class="form-label">Nama Obat</label>
                                <input type="text" class="form-control" name="nama_obat" id="nama_obat" value="<?= $nama_obat ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="kemasan" class="form-label">Kemasan</label>
                                <input type="text" class="form-control" name="kemasan" id="kemasan" value="<?= $kemasan ?>" />
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" name="harga" id="harga" value="<?= $harga ?>" required />
                            </div>
                            <button type="submit" class="btn btn-primary" name="simpan"><?= $is_edit ? 'Simpan Perubahan' : 'Tambah Obat' ?></button>
                            <?php if ($is_edit): ?>
                                <a href="admin_obat.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <hr>

                <!-- Table Section -->
                <div class="row">
                    <div class="col">
                        <h3>Daftar Obat</h3>
                        <table class="table bg-white rounded shadow-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Kemasan</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['nama_obat'] ?></td>
                                        <td><?= $row['kemasan'] ?></td>
                                        <td><?= $row['harga'] ?></td>
                                        <td>
                                            <a href="admin_obat.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                            <a href="admin_obat.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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
