<?php
session_start();
include 'config.php';

// Periksa apakah dokter sudah login
if (!isset($_SESSION['id_dokter'])) {
    header("Location: login.php");
    exit;
}

$id_dokter = $_SESSION['id_dokter'];

// Inisialisasi variabel
$id = '';
$jawaban = '';
$is_edit = false;

// Menampilkan daftar konsultasi untuk dokter
$query = "SELECT k.*, p.nama AS nama_pasien 
          FROM konsultasi k
          JOIN pasien p ON k.id_pasien = p.id
          WHERE k.id_dokter = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$result = $stmt->get_result();

// Proses tambah atau edit tanggapan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jawaban = $_POST['jawaban'];

    if (isset($_POST['simpan'])) {
        // Proses tanggapi / edit jawaban
        $update_query = $conn->prepare("UPDATE konsultasi SET jawaban = ? WHERE id = ? AND id_dokter = ?");
        $update_query->bind_param("sii", $jawaban, $id, $id_dokter);

        if ($update_query->execute()) {
            header("Location: dokter_konsultasi.php");
            exit;
        } else {
            die("Error pada update query: " . $conn->error);
        }
    }
}

// Proses hapus konsultasi
// if (isset($_GET['hapus'])) {
//     $id = $_GET['hapus'];
//     $delete_query = $conn->prepare("DELETE FROM konsultasi WHERE id = ? AND id_dokter = ?");
//     $delete_query->bind_param("ii", $id, $id_dokter);

//     if ($delete_query->execute()) {
//         header("Location: dokter_konsultasi.php");
//         exit;
//     } else {
//         die("Error pada delete query: " . $conn->error);
//     }
// }

// Proses edit tanggapan (untuk menampilkan data di form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $select_query = $conn->prepare("SELECT * FROM konsultasi WHERE id = ? AND id_dokter = ?");
    $select_query->bind_param("ii", $id, $id_dokter);
    $select_query->execute();
    $result_edit = $select_query->get_result();

    if ($row = $result_edit->fetch_assoc()) {
        $jawaban = $row['jawaban'];
        $is_edit = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Medis Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Konsultasi Medis</h3>

        <!-- Tabel Daftar Konsultasi -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Pertanyaan</th>
                    <th>Tanggapan</th>
                    <th>Pasien</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['subject'] ?></td>
                        <td><?= $row['pertanyaan'] ?></td>
                        <td><?= $row['jawaban'] ?: 'Belum ada tanggapan' ?></td>
                        <td><?= $row['nama_pasien'] ?></td>
                        <td>
                            <?php if (!$row['jawaban']): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal" 
                                    onclick="setFormData(<?= $row['id'] ?>, '<?= htmlspecialchars($row['pertanyaan'], ENT_QUOTES) ?>')">
                                    Tanggapi
                                </button>
                            <?php endif; ?>
                            <a href="dokter_konsultasi.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <!-- <a href="dokter_konsultasi.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a> -->
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
                        <h5 class="modal-title" id="formModalLabel">Tanggapi Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="formId" value="<?= $id ?>">
                        <div class="mb-3">
                            <label for="pertanyaan" class="form-label">Pertanyaan</label>
                            <textarea class="form-control" id="formPertanyaan" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="jawaban" class="form-label">Tanggapan</label>
                            <textarea class="form-control" name="jawaban" id="formJawaban" required><?= $jawaban ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="simpan">Simpan Tanggapan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setFormData(id, pertanyaan) {
            document.getElementById('formId').value = id;
            document.getElementById('formPertanyaan').value = pertanyaan;
            document.getElementById('formJawaban').value = '';
        }
    </script>
</body>

</html>