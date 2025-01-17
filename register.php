<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    // Cek apakah pasien sudah terdaftar berdasarkan nomor KTP
    $stmt = $conn->prepare("SELECT * FROM pasien WHERE no_ktp = ?");
    $stmt->bind_param("i", $no_ktp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('Pasien dengan nomor KTP ini sudah terdaftar!');
            window.location.href = 'register.php';
        </script>";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Jika pasien belum terdaftar, buat No RM
    $bulanTahun = date('Ym'); // Tahun dan bulan saat ini (format: YYYYMM)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_pasien FROM pasien WHERE no_rm LIKE ?");
    $noRmPrefix = $bulanTahun . '-%';
    $stmt->bind_param("s", $noRmPrefix);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $urut = $row['total_pasien'] + 1; // Urutan pasien keberapa di bulan ini
    $no_rm = $bulanTahun . '-' . $urut;

    // Masukkan data pasien ke database
    $stmt = $conn->prepare("INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registrasi berhasil! Nomor Rekam Medis Anda: " . $no_rm . "');
            window.location.href = 'register.php';
        </script>";
    } else {
        echo "<script>
            alert('Terjadi kesalahan: " . $stmt->error . "');
            window.location.href = 'register.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pasien Baru</title>
    <link href='logo/icon.ico' rel='SHORTCUT ICON' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section class="vh-100" style="background-color: #baf3d7;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <a href="home.php">
                                <img src="https://politeknikpu.ac.id/wp-content/uploads/2024/05/DSC02421-1-scaled.webp" 
                                     alt="login form" class="img-fluid" 
                                     style="border-radius: 1rem 0 0 1rem; width: 100%; height: 100%; object-fit: cover;" />
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <h2 class="fw-bold mb-4">REGISTRASI PASIEN</h2>

                                <form action="" method="POST">
                                    <div class="form-outline mb-4">
                                        <input type="text" id="nama" name="nama" class="form-control form-control-lg" required aria-label="Nama" />
                                        <label class="form-label" for="nama">Nama</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <textarea name="alamat" id="alamat" class="form-control form-control-lg" required aria-label="Alamat"></textarea>
                                        <label class="form-label" for="alamat">Alamat</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="text" id="no_ktp" name="no_ktp" class="form-control form-control-lg" required aria-label="Nomor KTP" />
                                        <label class="form-label" for="no_ktp">Nomor KTP</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="tel" id="no_hp" name="no_hp" class="form-control form-control-lg" required aria-label="Nomor HP" />
                                        <label class="form-label" for="no_hp">Nomor HP</label>
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button type="submit" class="btn btn-dark btn-lg btn-block">Daftar</button>
                                    </div>
                                </form>

                                <p class="mb-5 pb-lg-2" style="color: #393f81;">Sudah memiliki akun?
                                    <a href="login.php" style="color: #393f81;">Login di sini</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
