<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    @stack('styles')
    <style>
        .swal2-popup .btn-batal-custom {
            background-color: #007A33 !important;
            color: white !important;
            border: none !important;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            margin-left: 10px;
        }

        .swal2-popup .btn-batal-custom:hover {
            background-color: #006127 !important;
        }
    </style>
</head>


<body id="body-pd">
    <header class="header bg-ijo-custom" id="header">
        <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img">
            <form action="{{ route('logout') }}" method="POST" class="sign_out">
                @csrf
                {{-- <button type="submit" class="btn btn-danger w-100">Keluar</button> --}}
                <i class='bx bx-log-out nav_icon'></i>
                <button type="submit" class="nav_name" style="all: unset;">Keluar</span>
            </form>
            {{-- <img src="https://media.istockphoto.com/id/1337144146/vector/default-avatar-profile-icon-vector.jpg?s=612x612&w=0&k=20&c=BIbFwuv7FxTWvh5S3vB6bkT0Qv8Vn8N5Ffseq84ClGI="
                alt=""> --}}
        </div>

    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo">
                    {{-- <i class='bx bx-layer nav_logo-icon'></i> --}}
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon"
                        style="width: 25px; height: auto;">
                    <span class="nav_logo-name">SIMPEG Al-Kasyaf</span>
                </a>
                <div class="nav_list"> <a href="{{ route('dashboard') }}"
                        class="nav_link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="{{ route('pegawai.index') }}"
                        class="nav_link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                        <i class='bx bx-user nav_icon'></i>
                        <span class="nav_name">Data Pegawai</span>
                    </a> <a href="{{ route('user.index') }}"
                        class="nav_link {{ request()->routeIs('user.*') ? 'active' : '' }}"> <i
                            class='bx bx-id-card nav_icon'></i>
                        <span class="nav_name">Data Akun</span>
                    </a> <a href="{{ route('activity.index') }}" class="nav_link"> <i
                            class='bx bx-calendar-check nav_icon'></i> <span class="nav_name">Data Aktivitas</span> </a>
                    </a> <a href="{{ route('presensi.index') }}" class="nav_link"> <i
                            class='bx bx-calendar-check nav_icon'></i> <span class="nav_name">Data Presensi</span> </a>
                    <a href="{{ route('penggajian.index') }}" class="nav_link"> <i class='bx bx-wallet nav_icon'></i>
                        <span class="nav_name">Penggajian</span> </a> <a href="#" class="nav_link"> <i
                            class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Laporan</span> </a>
                    </a> <a href="{{ route('presensi.create') }}" class="nav_link"> <i
                            class='bx bx-wallet nav_icon'></i> <span class="nav_name">Absen</span> </a>
                </div>
            </div>

        </nav>
    </div>
    <main class="height-100 bg-white pt-4">
        <div class="pb-5">
            @yield('content')
        </div>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: true,
                    timer: 3000
                });
            </script>
        @endif

        @if ($errors->any() || session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Gagal!',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    @stack('scripts')

</body>

</html>
