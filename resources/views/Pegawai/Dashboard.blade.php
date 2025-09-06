<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --primary-color: #3e6455;
            --primary-color-alt: #315044;
            --white-color: #F7F6F3;
            --light-bg: #f7f7f7;
            --card-bg: #ffffff;
            --text-color: #495057;
            --font-family: 'Nunito', sans-serif;
            --first-color-light: #AFA5D9;
            --z-fixed: 100;
        }

        *, ::before, ::after {
            box-sizing: border-box;
        }

        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0;
            font-family: var(--font-family);
            background-color: var(--light-bg);
            transition: .5s;
        }

        a { text-decoration: none; }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: var(--primary-color);
            z-index: var(--z-fixed);
            transition: .5s;
        }
        
        .header_toggle { color: var(--white-color); font-size: 1.5rem; cursor: pointer; }
        .sign_out { display: flex; align-items: center; color: var(--white-color); }
        .sign_out .nav_name { background: none; border: none; color: var(--white-color); padding: 0; margin-left: 0.5rem; cursor: pointer; }
        .sign_out .nav_icon { font-size: 1.25rem; }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -100%; 
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--primary-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed);
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .nav_logo, .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem;
        }

        .nav_logo {
            margin-bottom: 2rem;
        }

        .nav_logo-icon { font-size: 1.25rem; color: var(--white-color); }
        .nav_logo-name { color: var(--white-color); font-weight: 700; }
        .nav_link {
            position: relative;
            color: white;
            margin-bottom: 1.5rem;
            transition: .3s;
        }
        .nav_link:hover { color: white; }
        .nav_icon { font-size: 1.25rem; }
        
        .show {
            left: 0;
            width: calc(var(--nav-width) + 156px); 
        }
        
        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem);
        }

        .active { color: var(--white-color); }
        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color);
        }

        /* Tampilan Desktop */
        @media screen and (min-width: 768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem);
            }
            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
            }
            .l-navbar {
                left: 0;
                padding: 1rem 1rem 0 0;
            }
            .show {
                width: calc(var(--nav-width) + 156px);
            }
            .body-pd {
                padding-left: calc(var(--nav-width) + 188px);
            }
        }

        /* Kartu Presensi dan Konten Utama */
        main {
            position: relative;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - var(--header-height));
        }
        
        .presensi-card {
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            border-radius: 1rem;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            background-color: var(--card-bg);
            text-align: center;
        }
        
        .presensi-card .qr-icon { font-size: 4rem; color: var(--primary-color); }
        .presensi-card h2 { font-weight: 700; margin-top: 1rem; margin-bottom: 0.5rem; }
        .presensi-card .date-display { text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px; color: #6c757d; margin-bottom: 0; }
        .presensi-card .clock-display { font-size: 3.5rem; font-weight: 700; color: var(--primary-color); line-height: 1.2; }
        
        .btn-presensi-sekarang {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            width: 100%;
            max-width: 300px;
            margin-top: 1.5rem;
        }

        .btn-presensi-sekarang:hover { background-color: var(--primary-color-alt); color: white; }
        .bottom-links { margin-top: 2rem; }
        .bottom-links a { color: #6c757d; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .bottom-links a:hover { color: var(--primary-color); }
        .bottom-links .separator { color: #d1d1d1; margin: 0 0.75rem; }

    </style>
</head>

<body id="body-pd">
    {{-- Header --}}
    <header class="header" id="header">
        <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
        <div class="header_img">
            <form action="{{ route('logout') }}" method="POST" class="sign_out">
                @csrf
                <i class='bx bx-log-out nav_icon'></i>
                <button type="submit" class="nav_name">Keluar</button>
            </form>
        </div>
    </header>

    {{-- Sidebar --}}
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="{{ route('dashboard.pegawai') }}" class="nav_logo">
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon" style="width: 25px; height: auto;">
                    <span class="nav_logo-name">SIMPEG</span>
                </a>
                <div class="nav_list">
                    <a href="{{ route('dashboard.pegawai') }}" class="nav_link {{ request()->routeIs('dashboard.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Menu Utama</span>
                    </a>
                    <a href="{{ route('presensiqr.pegawai') }}" class="nav_link {{ request()->routeIs('presensiqr.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-qr nav_icon'></i>
                        <span class="nav_name">Presensi QR Code</span>
                    </a>
                    <a href="{{ route('presensi.history') }}" class="nav_link {{ request()->routeIs('presensi.history') ? 'active' : '' }}">
                        <i class='bx bx-history nav_icon'></i>
                        <span class="nav_name">Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('slipgaji.pegawai') }}" class="nav_link {{ request()->routeIs('slipgaji.*') ? 'active' : '' }}">
                        <i class='bx bx-receipt nav_icon'></i>
                        <span class="nav_name">Slip Gaji</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <main>
        <div class="card presensi-card">
            <i class='bx bx-qr qr-icon mb-3'></i>

            <h2>Selamat Datang, {{ $user->nama ?? 'Pegawai' }}!</h2>
            <p class="text-muted">Silakan lakukan presensi dengan memindai QR Code.</p>

         
            <a href="{{ route('presensiqr.pegawai') }}" class="btn btn-presensi-sekarang mx-auto">
                <i class="bi bi-camera-fill me-2"></i> Lakukan Presensi Sekarang
            </a>

        </div>
    </main>
    @if (session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}' });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({ icon: 'error', title: 'Gagal!', html: `{!! implode('<br>', $errors->all()) !!}` });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clockEl = document.getElementById('clock-display');
            const dateEl = document.getElementById('date-display');

            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':');
                const dateString = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }).toUpperCase();

                if (clockEl) clockEl.textContent = timeString;
                if (dateEl) dateEl.textContent = dateString;
            }
            updateTime();
            setInterval(updateTime, 1000);

            // Inisiasi Sidebar Toggle
            const showNavbar = (toggleId, navId, bodyId, headerId) =>{
                const toggle = document.getElementById(toggleId),
                nav = document.getElementById(navId),
                bodypd = document.getElementById(bodyId),
                headerpd = document.getElementById(headerId)

                if(toggle && nav && bodypd && headerpd){
                    toggle.addEventListener('click', ()=>{
                        nav.classList.toggle('show')
                        bodypd.classList.toggle('body-pd')
                        headerpd.classList.toggle('body-pd')
                    })
                }
            }
            showNavbar('header-toggle','nav-bar','body-pd','header')
        });
    </script>
</body>

</html>