<?php
include 'config.php';

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement untuk admin
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin'] = true;

        // Redirect ke halaman dashboard admin
        header('Location: admin_dashboard.php');
        exit();
    } else {
        // Prepared statement untuk pasien
        $stmt = $conn->prepare("SELECT * FROM pasien WHERE nama = ? AND no_hp = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $pasien = $result->fetch_assoc();
            $_SESSION['id_pasien'] = $pasien['id'];
            $_SESSION['nama'] = $pasien['nama'];

            // Redirect ke halaman dashboard pasien
            header('Location: pasien_dashboard.php');
            exit();
        } else {
            $error = "Login gagal! Nama atau Nomor HP salah.";
        }
        $stmt->close();
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
                                    <form action="" method="POST">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <span class="h1 fw-bold mb-0">LOGIN PASIEN</span>
                                        </div>
                                        <?php if (!empty($error)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?= htmlspecialchars($error) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-outline mb-4">
                                            <input type="text" id="username" name="username" class="form-control form-control-lg" required />
                                            <label class="form-label" for="username">Username</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                                            <label class="form-label" for="password">Password</label>
                                        </div>

                                        <div class="pt-1 mb-4">
                                            <button type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
                                        </div>
                                        <p class="mb-5 pb-lg-2" style="color: #393f81;">Belum memiliki akun?
                                            <a href="register.php" style="color: #393f81;">Registrasi di sini</a>
                                        </p>
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
