<?php
session_start();
if(isset($_SESSION['is_login']) && $_SESSION['is_login'] == 'Y'){
    header("location:dashboard");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Door AES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background-color: #f4f7f6;            
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
            background-color: #ffffff;
            transition: transform 0.3s ease;
        }        
        .card-body-custom {
            padding: 1.5rem;
        }
        @media (min-width: 576px) {
            .card-body-custom {
                padding: 2.5rem;
            }
        }
        .lock-icon {
            font-size: 3.5rem;
            color: #2b67f6;
        }
        .btn-masuk {
            background-color: #2b67f6;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-masuk:hover {
            background-color: #1a52d5;
        }
        .footer-text {
            margin-top: 25px;
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="card login-card shadow-sm">
        <div class="card-body card-body-custom text-center">
            <div class="mb-3">
                <i class="bi bi-lock-fill lock-icon"></i>
            </div>

            <h2 class="fw-bold mb-1 h3">Smart System AES</h2>
            <p class="text-muted mb-4 small">Silakan login untuk melanjutkan ke dashboard.</p>

            <form id="loginForm" class="text-start">
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" placeholder="admin@kampus.ac.id" style="font-size: 0.95rem;" required oninput="this.value = this.value.replace(/\s/g, '')">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" placeholder="........" style="font-size: 0.95rem;" required oninput="this.value = this.value.replace(/\s/g, '')">
                </div>

                <div class="d-grid">
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-masuk">
                        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="btnText">Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-text">
        Tugas Akhir - Implementasi Enkripsi AES
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.innerText = ' Mengecek...';

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            fetch('api/login/cek_login', {
                method: 'POST',
                body: formData // Body menggunakan FormData, header otomatis menyesuaikan
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                resetButton();
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Kredensial valid.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "dashboard";
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Email atau password salah.', 'error');
                }
            })
            .catch(error => {
                console.error("Fetch error: ", error); // Berguna untuk debugging di console browser
                setTimeout(() => {
                    resetButton();
                    Swal.fire('Info', 'Koneksi ke server gagal atau response bukan JSON.', 'warning');
                }, 1000);
            });

            function resetButton() {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerText = 'Masuk';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>