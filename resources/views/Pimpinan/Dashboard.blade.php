<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --primary-color: #3e6455;
            --primary-color-alt: rgb(49, 80, 68);
            --white-color: #FFFFFF;
            --light-bg: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-color-light: #6c757d;
            --border-color: #e9ecef;
            --font-family: 'Inter', sans-serif;
            --z-fixed: 100;

            --color-blue: #0d6efd;
            --color-green: #198754;
            --color-orange: #fd7e14;
        }

        *, ::before, ::after { box-sizing: border-box; }
        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--font-family);
            background-color: var(--light-bg);
            transition: .5s;
        }
        a { text-decoration: none; }

        /* --- Sidebar & Header (Tidak Diubah) --- */
        .header{width:100%;height:var(--header-height);position:fixed;top:0;left:0;display:flex;align-items:center;justify-content:space-between;padding:0 1rem;background-color:var(--primary-color);z-index:var(--z-fixed);transition:.5s}.header_toggle{color:var(--white-color);font-size:1.5rem;cursor:pointer}.l-navbar{position:fixed;top:0;left:-100%;width:calc(var(--nav-width) + 156px);height:100vh;background-color:var(--primary-color);padding:.5rem 1rem 0 0;transition:.5s;z-index:var(--z-fixed)}.nav{height:100%;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden}.nav_logo,.nav_link{display:grid;grid-template-columns:max-content max-content;align-items:center;column-gap:1rem;padding:.5rem 0 .5rem 1.5rem}.nav_logo{margin-bottom:2rem}.nav_logo-icon,.nav_icon{font-size:1.25rem;color:var(--white-color)}.nav_logo-name{color:var(--white-color);font-weight:700}.nav_link{position:relative;color:#E0E0E0;margin-bottom:1.5rem;transition:.3s}.nav_link:hover{color:var(--white-color)}.show{left:0}.body-pd{padding-left:calc(var(--nav-width) + 1rem)}.active{color:var(--white-color)}.active::before{content:'';position:absolute;left:0;width:2px;height:32px;background-color:var(--white-color)}.sign_out{display:flex;align-items:center;color:var(--white-color)}.sign_out .nav_name{background:none;border:none;color:var(--white-color);padding:0;margin-left:.5rem;cursor:pointer}
        @media screen and (min-width:768px){body{margin:calc(var(--header-height) + 1rem) 0 0 0;padding-left:calc(var(--nav-width) + 2rem)}.header{height:calc(var(--header-height) + 1rem);padding:0 2rem 0 calc(var(--nav-width) + 2rem)}.l-navbar{left:0;padding:1rem 1rem 0 0;width:var(--nav-width)}.l-navbar.show{width:calc(var(--nav-width) + 156px)}.body-pd{padding-left:calc(var(--nav-width) + 188px)}}

        /* === Tampilan Konten Dashboard === */
        main { padding-top: 1.5rem; padding-bottom: 3rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h3 { font-weight: 700; color: #343a40; }
        .page-header .text-muted { font-size: 1rem; }

        .stat-card {
            color: var(--white-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .stat-card .stat-icon {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 6rem;
            opacity: 0.2;
            transform: rotate(-15deg);
        }
        .stat-card h5 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .count {
            font-size: 2.75rem;
            font-weight: 700;
        }
        .bg-card-green { background: linear-gradient(45deg, #212529, #212529); }
        .bg-card-blue { background: linear-gradient(45deg, #212529, #212529); }
        .bg-card-orange { background: linear-gradient(45deg, #212529, #212529); }

        .content-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .content-card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text-color);
        }
    </style>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
        <div class="header_img">
            <form action="{{ route('logout') }}" method="POST" class="sign_out">
                @csrf
                <i class='bx bx-log-out nav_icon'></i><button type="submit" class="nav_name">Keluar</button>
            </form>
        </div>
    </header>

    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo">
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon" style="width: 25px; height: auto;">
                    <span class="nav_logo-name">SIMPEG</span>
                </a>
                <div class="nav_list">
                    {{-- Pastikan route ini benar --}}
                    <a href="{{ route('dashboard.pemimpin') }}" class="nav_link {{ request()->routeIs('dashboard.pemimpin') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    
                    {{-- START: Route Baru Ditambahkan di Sini --}}
                
                    {{-- END: Route Baru Ditambahkan di Sini --}}
                    
                    <a href="{{ route('laporan.gajibypemimpin') }}" class="nav_link {{ request()->routeIs('laporan.gajibypemimpin') ? 'active' : '' }}">
                        <i class='bx bx-bar-chart-alt-2 nav_icon'></i>
                        <span class="nav_name">Laporan</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <main>
        <div class="container-fluid">
            <div class="page-header">
                <h3 class="fw-bold">Selamat datang, {{ $user->nama ?? 'Pimpinan' }}</h3>
                <p class="text-muted">Ini adalah ringkasan data dari sistem kepegawaian.</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-card bg-card-green">
                        <h5>Total Pegawai</h5>
                        <p class="count">{{ $totalPegawai ?? 0 }}</p>
                        <i class="stat-icon bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-card bg-card-blue">
                        <h5>Total Admin</h5>
                        <p class="count">{{ $totalAdmin ?? 0 }}</p>
                        <i class="stat-icon bi bi-person-workspace"></i>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="stat-card bg-card-orange">
                        <h5>Total Aktivitas</h5>
                        <p class="count">{{ $totalAktivitas ?? 0 }}</p>
                        <i class="stat-icon bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>

            
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showNavbar=(t,n,e,o)=>{const l=document.getElementById(t),d=document.getElementById(n),c=document.getElementById(e),a=document.getElementById(o);l&&d&&c&&a&&l.addEventListener("click",()=>{d.classList.toggle("show"),c.classList.toggle("body-pd"),a.classList.toggle("body-pd")})};showNavbar("header-toggle","nav-bar","body-pd","header");
        });
    </script>
</body>
</html>