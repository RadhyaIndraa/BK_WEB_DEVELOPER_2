<?php
session_start();
include 'config.php';

// Periksa apakah pasien sudah login
if (!isset($_SESSION['id_pasien'])) {
    header("Location: login.php");
    exit;
}

$id_pasien = $_SESSION['id_pasien'];

// Inisialisasi variabel
$id = '';
$subject = '';
$pertanyaan = '';
$id_dokter = '';
$is_edit = false;

// Menampilkan daftar konsultasi pasien
$query = "SELECT k.*, d.nama AS nama_dokter 
          FROM konsultasi k
          JOIN dokter d ON k.id_dokter = d.id
          WHERE k.id_pasien = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result = $stmt->get_result();

// Proses tambah atau edit pertanyaan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $subject = $_POST['subject'];
    $pertanyaan = $_POST['pertanyaan'];
    $id_dokter = $_POST['id_dokter'];

    if (isset($_POST['simpan'])) {
        if ($id) {
            // Proses edit
            $update_query = $conn->prepare("UPDATE konsultasi SET subject = ?, pertanyaan = ?, id_dokter = ? WHERE id = ?");
            $update_query->bind_param("ssii", $subject, $pertanyaan, $id_dokter, $id);

            if ($update_query->execute()) {
                header("Location: pasien_konsultasi.php");
                exit;
            } else {
                die("Error pada update query: " . $conn->error);
            }
        } else {
            // Proses tambah
            $insert_query = $conn->prepare("INSERT INTO konsultasi (subject, pertanyaan, tgl_periksa, id_pasien, id_dokter) VALUES (?, ?, NOW(), ?, ?)");
            $insert_query->bind_param("ssii", $subject, $pertanyaan, $id_pasien, $id_dokter);

            if ($insert_query->execute()) {
                header("Location: pasien_konsultasi.php");
                exit;
            } else {
                die("Error pada insert query: " . $conn->error);
            }
        }
    }
}

// Proses hapus pertanyaan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_query = $conn->prepare("DELETE FROM konsultasi WHERE id = ? AND id_pasien = ?");
    $delete_query->bind_param("ii", $id, $id_pasien);

    if ($delete_query->execute()) {
        header("Location: pasien_konsultasi.php");
        exit;
    } else {
        die("Error pada delete query: " . $conn->error);
    }
}

// Proses edit pertanyaan (untuk menampilkan data di form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $select_query = $conn->prepare("SELECT * FROM konsultasi WHERE id = ? AND id_pasien = ?");
    $select_query->bind_param("ii", $id, $id_pasien);
    $select_query->execute();
    $result_edit = $select_query->get_result();

    if ($row = $result_edit->fetch_assoc()) {
        $subject = $row['subject'];
        $pertanyaan = $row['pertanyaan'];
        $id_dokter = $row['id_dokter'];
        $is_edit = true;
    }
}

// Menampilkan daftar dokter untuk dropdown
$dokter_query = "SELECT * FROM dokter";
$dokter_result = $conn->query($dokter_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Medis Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Konsultasi Medis</h3>

        <!-- Button Tambah Pertanyaan -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#formModal">Tambah Pertanyaan</button>

        <!-- Tabel Daftar Konsultasi -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Pertanyaan</th>
                    <th>Tanggapan</th>
                    <th>Dokter</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['subject'] ?></td>
                        <td><?= $row['pertanyaan'] ?></td>
                        <td><?= $row['jawaban'] ?: 'Belum ada tanggapan' ?></td>
                        <td><?= $row['nama_dokter'] ?></td>
                        <td>
                            <a href="pasien_konsultasi.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="pasien_konsultasi.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel"><?= $is_edit ? 'Edit' : 'Tambah' ?> Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" id="subject" value="<?= $subject ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="pertanyaan" class="form-label">Pertanyaan</label>
                            <textarea class="form-control" name="pertanyaan" id="pertanyaan" required><?= $pertanyaan ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="id_dokter" class="form-label">Pilih Dokter</label>
                            <select class="form-control" name="id_dokter" id="id_dokter" required>
                                <option value="">Pilih Dokter</option>
                                <?php while ($dokter = $dokter_result->fetch_assoc()): ?>
                                    <option value="<?= $dokter['id'] ?>" <?= $id_dokter == $dokter['id'] ? 'selected' : '' ?>>
                                        <?= $dokter['nama'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="simpan"><?= $is_edit ? 'Simpan Perubahan' : 'Tambah Pertanyaan' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>