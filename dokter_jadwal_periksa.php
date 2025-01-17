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

$edit_jadwal = null;

// Proses Tambah dan Edit Jadwal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_dokter = $_SESSION['id_dokter'];

    if (isset($_POST['add_schedule'])) {
        // Tambah Jadwal Baru
        $hari = $_POST['hari'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];
        $status = $_POST['status'];

        // Cek apakah dokter lain memiliki jadwal aktif
        $cek_jadwal_aktif = "SELECT id FROM jadwal_periksa WHERE id_dokter = ? AND status = 'aktif' AND id != ?"; 
        $stmt_cek = $conn->prepare($cek_jadwal_aktif);
        $stmt_cek->bind_param("ii", $id_dokter, $_POST['id_jadwal']);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0 && $status === 'aktif') {
            echo "Hanya satu jadwal yang bisa memiliki status 'aktif'.";
        } elseif (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
            echo "Jam mulai harus lebih awal daripada jam selesai.";
        } else {
            // Jika status bukan 'aktif', pastikan jadwal lain dengan status aktif dihapus atau diperbarui ke tidak aktif
            if ($status !== 'aktif') {
                $update_status_lain = "UPDATE jadwal_periksa SET status = 'tidak aktif' WHERE id_dokter = ? AND id != ?";
                $stmt_update = $conn->prepare($update_status_lain);
                $stmt_update->bind_param("ii", $id_dokter, $_POST['id_jadwal']);
                $stmt_update->execute();
            }
            
            $sql = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $id_dokter, $hari, $jam_mulai, $jam_selesai, $status);

            if ($stmt->execute()) {
                echo "Jadwal berhasil ditambahkan.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    } elseif (isset($_POST['edit_schedule'])) {
        // Edit Jadwal
        $id_jadwal = $_POST['id_jadwal'];
        $hari = $_POST['hari'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];
        $status = $_POST['status'];

        // Cek apakah dokter lain memiliki jadwal aktif
        $cek_jadwal_aktif = "SELECT id FROM jadwal_periksa WHERE id_dokter = ? AND status = 'aktif' AND id != ?";
        $stmt_cek = $conn->prepare($cek_jadwal_aktif);
        $stmt_cek->bind_param("ii", $id_dokter, $id_jadwal);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0 && $status === 'aktif') {
            echo "Hanya satu jadwal yang bisa memiliki status 'aktif'.";
        } elseif (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
            echo "Jam mulai harus lebih awal daripada jam selesai.";
        } else {
            // Jika status bukan 'aktif', pastikan jadwal lain dengan status aktif dihapus atau diperbarui ke tidak aktif
            if ($status !== 'aktif') {
                $update_status_lain = "UPDATE jadwal_periksa SET status = 'tidak aktif' WHERE id_dokter = ? AND id != ?";
                $stmt_update = $conn->prepare($update_status_lain);
                $stmt_update->bind_param("ii", $id_dokter, $id_jadwal);
                $stmt_update->execute();
            }
            
            $sql = "UPDATE jadwal_periksa SET hari = ?, jam_mulai = ?, jam_selesai = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $hari, $jam_mulai, $jam_selesai, $status, $id_jadwal);

            if ($stmt->execute()) {
                echo "Jadwal berhasil diubah.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
    header("Location: dokter_jadwal_periksa.php");
    exit;
}

// Proses Hapus Jadwal
if (isset($_GET['delete_schedule'])) {
    $id_jadwal = $_GET['delete_schedule'];

    // Pastikan untuk memperbarui atau menghapus data yang mengacu pada id_jadwal di tabel daftar_poli
    $update_daftar_poli = "UPDATE daftar_poli SET id_jadwal = NULL WHERE id_jadwal = ?";
    $stmt_update_daftar_poli = $conn->prepare($update_daftar_poli);
    $stmt_update_daftar_poli->bind_param("i", $id_jadwal);
    $stmt_update_daftar_poli->execute();

    // Hapus jadwal dari tabel jadwal_periksa
    $sql = "DELETE FROM jadwal_periksa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_jadwal);

    if ($stmt->execute()) {
        // Jika berhasil, simpan pesan ke session
        $_SESSION['message'] = 'Jadwal berhasil dihapus';
    } else {
        $_SESSION['message'] = 'Terjadi kesalahan saat menghapus jadwal';
    }

    $stmt->close();
    header("Location: dokter_jadwal_periksa.php");
    exit;
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
                <a href="dokter_jadwal_periksa.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-paperclip me-2"></i>Jadwal Periksa</a>
                <a href="dokter_memeriksa.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
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
                <h1 class="mt-4">Pengaturan Jadwal Periksa Dokter</h1>
                <form method="POST" action="">
                    <input type="hidden" name="id_jadwal" value="<?php echo $edit_jadwal['id'] ?? ''; ?>">

                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari:</label>
                        <select name="hari" id="hari" class="form-control" required>
                            <option value="Senin" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Senin') ? 'selected' : ''; ?>>Senin</option>
                            <option value="Selasa" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Selasa') ? 'selected' : ''; ?>>Selasa</option>
                            <option value="Rabu" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Rabu') ? 'selected' : ''; ?>>Rabu</option>
                            <option value="Kamis" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Kamis') ? 'selected' : ''; ?>>Kamis</option>
                            <option value="Jumat" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Jumat') ? 'selected' : ''; ?>>Jumat</option>
                            <option value="Sabtu" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Sabtu') ? 'selected' : ''; ?>>Sabtu</option>
                            <option value="Minggu" <?php echo (isset($edit_jadwal) && $edit_jadwal['hari'] == 'Minggu') ? 'selected' : ''; ?>>Minggu</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai:</label>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="<?php echo $edit_jadwal['jam_mulai'] ?? ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai:</label>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="<?php echo $edit_jadwal['jam_selesai'] ?? ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="aktif" <?php echo (isset($edit_jadwal) && $edit_jadwal['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="tidak aktif" <?php echo (isset($edit_jadwal) && $edit_jadwal['status'] == 'tidak aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" name="<?php echo isset($edit_jadwal) ? 'edit_schedule' : 'add_schedule'; ?>">
                        <?php echo isset($edit_jadwal) ? 'Simpan Perubahan' : 'Tambah Jadwal'; ?>
                    </button>

                    <?php if (isset($edit_jadwal)): ?>
                        <a href="dokter_jadwal_periksa.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </form>

                <h2 class="mt-5">Daftar Jadwal</h2>
                <table class="table bg-white rounded shadow-sm table-hover">
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $id_dokter = $_SESSION['id_dokter'];
                        $sql_jadwal = "SELECT jadwal_periksa.id, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai, jadwal_periksa.status, dokter.nama 
                                    FROM jadwal_periksa 
                                    JOIN dokter ON jadwal_periksa.id_dokter = dokter.id 
                                    WHERE jadwal_periksa.id_dokter = ?";
                        $stmt_jadwal = $conn->prepare($sql_jadwal);
                        $stmt_jadwal->bind_param("i", $id_dokter);
                        $stmt_jadwal->execute();
                        $result_jadwal = $stmt_jadwal->get_result();

                        while ($row = $result_jadwal->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $row['hari'] . "</td>";
                            echo "<td>" . $row['jam_mulai'] . "</td>";
                            echo "<td>" . $row['jam_selesai'] . "</td>";
                            echo "<td>" . ucfirst($row['status']) . "</td>";
                            echo "<td>
                                    <a href='?edit_schedule=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                                    <a href='?delete_schedule=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus jadwal ini?\");'>Hapus</a>
                                </td>";
                            echo "</tr>";
                        }
                        ?>
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

        <!-- Notifikasi Popup -->
        // Memeriksa apakah ada pesan di session dan menampilkan alert
        <?php if (isset($_SESSION['message'])): ?>
            alert("<?php echo $_SESSION['message']; ?>");
            <?php unset($_SESSION['message']); ?>  <!-- Hapus pesan setelah ditampilkan -->
        <?php endif; ?>
    </script>
    
</body>

</html>
