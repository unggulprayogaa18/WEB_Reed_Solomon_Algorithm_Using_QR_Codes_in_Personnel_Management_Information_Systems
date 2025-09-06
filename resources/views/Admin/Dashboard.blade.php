<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --primary-color: #3e6455;
            --primary-color-alt: #315044;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #495057;
            --border-color: #dee2e6;
            --font-family: 'Inter', sans-serif;
        }

        .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
        }

        body {
            background-color: var(--light-bg);
            font-family: var(--font-family);
            color: var(--text-color);
        }

        main {
            position: relative;
            margin-top: var(--header-height);
            padding: 1.5rem;
            transition: padding-left .5s;
        }

        .body-pd main {
            padding-left: calc(var(--nav-width) + 1.5rem);
        }

        /* Gaya Kartu Statistik yang Diperbarui */
        .stat-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .stat-card-icon {
            font-size: 2.5rem;
            padding: 1rem;
            border-radius: 50%;
            margin-right: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card-info .stat-label {
            font-weight: 500;
            color: #6c757d;
        }

        .stat-card-info .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
        }

        /* Warna spesifik untuk ikon */
        .icon-primary {
            background-color: rgba(62, 100, 85, 0.1);
            color: var(--primary-color);
        }

        .icon-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .icon-info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .icon-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
    </style>
</head>

<body id="body-pd">
    @include('layouts.sidebar')

    <main>
        <div class="container-fluid">
            <div class="mb-4">
                <h2 class="fw-bold">Dashboard Admin</h2>
                <p class="text-muted">Selamat datang kembali, {{ $user->nama ?? 'Admin' }}!</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-card-icon icon-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-label">Total Pegawai</div>
                            <div class="stat-number">{{ $pegawai }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-card-icon icon-success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-label">Total Pimpinan </div>
                            <div class="stat-number">{{ $pemimpin }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-card-icon icon-info">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-label">Total Admin</div>
                            <div class="stat-number">{{ $totalAdmin }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-card-icon icon-warning">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-label">Total Aktivitas </div>
                            <div class="stat-number">{{ $totalAktivitas }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5">



        </div>
    </main>

    @if (session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}' });
        </script>
    @endif
    @if ($errors->any() || session('error'))
        <script>
            Swal.fire({ title: 'Gagal!', html: `{!! implode('<br>', $errors->all()) !!}{{ session('error') }}`, icon: 'error' });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>