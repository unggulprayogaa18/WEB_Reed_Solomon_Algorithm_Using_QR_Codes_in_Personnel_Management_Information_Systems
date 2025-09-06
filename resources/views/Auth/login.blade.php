<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kepegawaian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body,
        html {
            height: 100%;
        }

        .container-login {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            background-color: #fff;
        }

        /* START: Style tambahan untuk ikon agar tidak mengubah tinggi input */
        .input-group-text {
            border-left: 0;
            background-color: transparent;
        }
        .form-control:focus {
            box-shadow: none;
        }
        /* END: Style tambahan */

    </style>
</head>

<body>
    <div class="container-login">
        <div class="login-card text-center">

            <img src="{{ asset('img/logoak.png') }}" alt="Logo"
                style="width: 100px; height: auto; margin-bottom: 20px;">

            <h4 class="mb-1">Sistem Manajemen Kepegawaian</h4>
            <p class="mb-4 text-muted">Pesantren Yatim Al-Kasyaf</p>

            @if ($errors->any())
                <div class="alert alert-danger text-start">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email Pengguna</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required
                        autofocus>
                </div>

                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" class="form-control border-end-0" id="password" name="password" required>
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn w-100 mt-3"
                    style="background-color: #007A33; color: white;">Login</button>

            </form>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const icon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function (e) {
            // Toggle tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle ikon mata
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
    </body>

</html>