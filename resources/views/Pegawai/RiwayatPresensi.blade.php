<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Kehadiran - {{ config('app.name', 'Laravel') }}</title>

    {{-- Aset & Font --}}
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
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #495057;
            --font-family: 'Nunito', sans-serif;
            --z-fixed: 100;
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

        .header { width: 100%; height: var(--header-height); position: fixed; top: 0; left: 0; display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; background-color: var(--primary-color); z-index: var(--z-fixed); transition: .5s; }
        .header_toggle { color: var(--white-color); font-size: 1.5rem; cursor: pointer; }
        .l-navbar { position: fixed; top: 0; left: -100%; width: calc(var(--nav-width) + 156px); height: 100vh; background-color: var(--primary-color); padding: .5rem 1rem 0 0; transition: .5s; z-index: var(--z-fixed); }
        .nav { height: 100%; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; }
        .nav_logo, .nav_link { display: grid; grid-template-columns: max-content max-content; align-items: center; column-gap: 1rem; padding: .5rem 0 .5rem 1.5rem; }
        .nav_logo { margin-bottom: 2rem; }
        .nav_logo-icon, .nav_icon { font-size: 1.25rem; color: var(--white-color); }
        .nav_logo-name { color: var(--white-color); font-weight: 700; }
        .nav_link { position: relative; color: #ffffff; margin-bottom: 1.5rem; transition: .3s; }
        .nav_link:hover { color: var(--white-color); }
        .show { left: 0; }
        .body-pd { padding-left: calc(var(--nav-width) + 1rem); }
        .active { color: var(--white-color); }
        .active::before { content: ''; position: absolute; left: 0; width: 2px; height: 32px; background-color: var(--white-color); }
        .sign_out { display: flex; align-items: center; color: var(--white-color); }
        .sign_out .nav_name { background: none; border: none; color: var(--white-color); padding: 0; margin-left: 0.5rem; cursor: pointer; }

        @media screen and (min-width: 768px) {
            body { margin: calc(var(--header-height) + 1rem) 0 0 0; padding-left: calc(var(--nav-width) + 2rem); }
            .header { height: calc(var(--header-height) + 1rem); padding: 0 2rem 0 calc(var(--nav-width) + 2rem); }
            .l-navbar { left: 0; padding: 1rem 1rem 0 0; width: var(--nav-width); }
            .l-navbar.show { width: calc(var(--nav-width) + 156px); }
            .body-pd { padding-left: calc(var(--nav-width) + 188px); }
        }

        main { padding-top: 1.5rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h3 { font-weight: 700; color: #343a40; }
        .page-header .text-muted { font-size: 1rem; }
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card-header { background-color: var(--card-bg); border-bottom: 1px solid #e9ecef; padding: 1.25rem 1.5rem; font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; }
        .card-header i { margin-right: 0.75rem; color: var(--primary-color); }
        .card-body { padding: 1.5rem; }
        .search-wrapper { position: relative; }
        .search-wrapper .form-control { border-radius: 50px; padding-left: 2.5rem; border-color: #dee2e6; }
        .search-wrapper .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25); }
        .search-wrapper .icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #adb5bd; }
        .table { border-collapse: separate; border-spacing: 0 0.5rem; }
        .table thead th { background-color: var(--light-bg); border: none; font-weight: 600; color: #6c757d; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .table tbody tr { background-color: var(--card-bg); transition: all 0.2s ease-in-out; }
        .table tbody tr:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); z-index: 10; }
        .table td, .table th { border: none; vertical-align: middle; padding: 1rem; }
        .table td:first-child, .table th:first-child { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
        .table td:last-child, .table th:last-child { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
        .badge { padding: 0.5em 0.9em; font-size: 0.8rem; font-weight: 600; }
        
        /* Style untuk Pagination */
        .pagination { justify-content: center; }
        .pagination .page-item .page-link { border-radius: 50px !important; margin: 0 0.25rem; border: none; color: var(--primary-color); }
        .pagination .page-item.active .page-link { background-color: var(--primary-color); color: white; }
        .pagination .page-item.disabled .page-link { color: #6c757d; }

    </style>
</head>

<body id="body-pd">
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

    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo">
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon" style="width: 25px; height: auto;">
                    <span class="nav_logo-name">SIMPEG</span>
                </a>
                <div class="nav_list">
                    <a href="{{ route('dashboard.pegawai') }}" class="nav_link {{ request()->routeIs('dashboard.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i><span class="nav_name">Menu Utama</span>
                    </a>
                    <a href="{{ route('presensiqr.pegawai') }}" class="nav_link {{ request()->routeIs('presensiqr.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-qr nav_icon'></i><span class="nav_name">Presensi QR</span>
                    </a>
                    <a href="{{ route('presensi.history') }}" class="nav_link {{ request()->routeIs('presensi.history') ? 'active' : '' }}">
                        <i class='bx bx-history nav_icon'></i><span class="nav_name">Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('slipgaji.pegawai') }}" class="nav_link {{ request()->routeIs('slipgaji.*') ? 'active' : '' }}">
                        <i class='bx bx-receipt nav_icon'></i><span class="nav_name">Slip Gaji</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <main>
        <div class="container-fluid">
            <div class="page-header">
                <h3>Riwayat Kehadiran Saya</h3>
                <p class="text-muted">Berikut adalah rekapitulasi data kehadiran Anda.</p>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 search-wrapper">
                        <i class="icon bi bi-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan aktivitas, tanggal, atau status...">
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                {{-- ### HEADER TABEL BARU ### --}}
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Aktivitas</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Waktu</th>
                                    <th scope="col" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="riwayatTableBody">
                                {{-- ### LOGIKA LOOPING BARU ### --}}
                                @forelse ($riwayatPresensi as $index => $presensi)
                                    <tr>
                                        {{-- Nomor urut yang sesuai dengan pagination --}}
                                        <td>{{ $riwayatPresensi->firstItem() + $index }}</td>
                                        
                                        {{-- Menampilkan nama aktivitas (aman jika aktivitas sudah dihapus) --}}
                                        <td>{{ $presensi->activity->nama_aktivitas ?? 'Aktivitas Dihapus' }}</td>

                                        {{-- Menampilkan tanggal dari created_at --}}
                                        <td>{{ $presensi->created_at->isoFormat('dddd, D MMM Y') }}</td>

                                        {{-- Menampilkan waktu dari created_at --}}
                                        <td>{{ $presensi->created_at->format('H:i:s') }}</td>
                                        
                                        {{-- Menampilkan status dengan badge --}}
                                        <td class="text-center">
                                            @if ($presensi->status == 'masuk')
                                                <span class="badge bg-success">Masuk</span>
                                            @elseif ($presensi->status == 'keluar')
                                                <span class="badge bg-danger">Keluar</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($presensi->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Pesan jika tidak ada data, colspan disesuaikan menjadi 5 --}}
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-cloud-drizzle fs-1 text-muted"></i>
                                            <h5 class="mt-3">Belum Ada Data</h5>
                                            <p class="text-muted">Tidak ada riwayat presensi yang tercatat untuk Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ### TAMBAHKAN LINK PAGINATION ### --}}
                    @if ($riwayatPresensi->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $riwayatPresensi->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </main>

    {{-- Script SweetAlert dan lainnya --}}
    @if (session('success'))
        <script> Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}' }); </script>
    @endif
    @if ($errors->any())
        <script> Swal.fire({ icon: 'error', title: 'Gagal!', html: `{!! implode('<br>', $errors->all()) !!}` }); </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script untuk sidebar (tidak diubah)
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });
                }
            };
            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            // Script untuk pencarian (tidak diubah, akan tetap berfungsi)
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                const tableBody = document.getElementById('riwayatTableBody');
                const tableRows = tableBody.getElementsByTagName('tr');

                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    for (let i = 0; i < tableRows.length; i++) {
                        let row = tableRows[i];
                        if (row.getElementsByTagName('td').length > 1) { 
                            let text = row.textContent || row.innerText;
                            if (text.toLowerCase().indexOf(filter) > -1) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>