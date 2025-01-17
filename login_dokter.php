<?php
include 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);

    // Query untuk memeriksa nama dan no_hp
    $sql = "SELECT * FROM dokter WHERE nama = '$nama' AND no_hp = '$no_hp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $dokter = $result->fetch_assoc();
        $_SESSION['id_dokter'] = $dokter['id'];
        $_SESSION['nama'] = $dokter['nama'];

        // Redirect ke halaman dashboard
        header('Location: dokter_dashboard.php');
        exit();
    } else {
        $error = "Login gagal! Nama atau Nomor HP salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href='logo/icon.ico' rel='SHORTCUT ICON' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <section class="vh-100" style="background-color: #85c1e9;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 1rem;">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-5 d-none d-md-block">
                                <a href="home.php">
                                    <img src="logo/logogambar.png" 
                                        alt="login form" class="img-fluid" 
                                        style="border-radius: 1rem 0 0 1rem; width: 100%; height: 100%; object-fit: cover;" />
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body p-4 p-lg-5 text-black">

                                    <form method="POST" action="">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <span class="h1 fw-bold mb-0">LOGIN DOKTER</span>
                                        </div>

                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?= $error; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="nama" name="nama" class="form-control form-control-lg" required />
                                            <label class="form-label" for="nama">Nama</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="no_hp" name="no_hp" class="form-control form-control-lg" required />
                                            <label class="form-label" for="no_hp">Nomor HP</label>
                                        </div>

                                        <div class="pt-1 mb-4">
                                            <button type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
                                        </div>
                                    </form>

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
