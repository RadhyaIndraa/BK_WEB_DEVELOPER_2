<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik Sehat</title>
    <link href='logo/icon.ico' rel='SHORTCUT ICON' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        header {
            background: #85c1e9;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            font-size: 2.5rem;
            margin: 0;
            margin-left: 20px;
            font-weight: bolder;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background-color: #85c1e9;
            color: white;
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            padding: 15px 0;
        }
        .icon-box {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: #fff;
            transition: all 0.3s ease-in-out;
            height: 200px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .icon-box h4 {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 10px;
        }
        .icon-box i {
            font-size: 2.5rem;
            color: #85c1e9;
            margin-bottom: 10px;
        }
        .icon-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .description {
            font-size: 0.9rem;
            color: #555;
            margin-top: 10px;
            text-align: center;
        }
        .btn-custom {
            background-color: #85c1e9;
            color: white;
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #5da48a;
            transform: scale(1.05);
        }
        .footer {
            background-color: #85c1e9;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>POLIKLINIK</h1>
    </header>

    <!-- isi -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clinic-medical"></i> POLIKLINIK
                    </div>
                    <div class="card-body">
                        <p>Selamat datang di Poliklinik, layanan kesehatan terpercaya dengan fasilitas modern dan tenaga medis profesional untuk melayani kebutuhan Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login pasien/dokter -->
        <div class="text-center mt-5">
 
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-stethoscope"></i> Pasien
                        </div>
                        <div class="card-body">
                            <a href="login.php" class="btn btn-custom w-100">Login Sebagai Pasien</a>
                            <p class="description">Jika Anda seorang pasien, silakan login untuk mengatur jadwal pemeriksaan, memilih layanan, dan melihat riwayat kesehatan Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user-md"></i> Dokter
                        </div>
                        <div class="card-body">
                            <a href="login_dokter.php" class="btn btn-custom w-100">Login Sebagai Dokter</a>
                            <p class="description">Login sebagai dokter, login untuk mengelola jadwal konsultasi, memberikan diagnosis, dan mengakses informasi pasien dengan mudah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Radhya Adiyatma Indarto.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
