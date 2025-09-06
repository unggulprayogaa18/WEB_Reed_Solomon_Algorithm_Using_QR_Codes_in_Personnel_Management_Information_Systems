{{-- layouts/sidebar.blade.php --}}

<header class="header bg-ijo-custom" id="header">
    <div class="header_toggle">
        <i class='bx bx-menu' id="header-toggle"></i>
    </div>
    <div class="header_img">
        {{-- Jika Anda ingin menampilkan gambar profil, letakkan di sini --}}
        {{-- <img src="path/to/image.jpg" alt=""> --}}
    </div>
</header>

<div class="l-navbar" id="nav-bar">
    <nav class="nav">
        <div>
            <a href="{{ route('dashboard.admin') }}" class="nav_logo">
                <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon" width="100px">
                <span class="nav_logo-name">SIMPEG Al-Kasyaf</span>
            </a>
            <div class="nav_list">
                <a href="{{ route('dashboard.admin') }}"
                    class="nav_link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                    <i class='bx bx-grid-alt nav_icon'></i>
                    <span class="nav_name">Dashboard</span>
                </a>
                <a href="{{ route('pegawai.index') }}"
                    class="nav_link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                    <i class='bx bx-user nav_icon'></i>
                    <span class="nav_name">Data Pegawai</span>
                </a>
                <a href="{{ route('akun.index') }}" class="nav_link {{ request()->routeIs('akun.*') ? 'active' : '' }}">
                    <i class='bx bx-id-card nav_icon'></i>
                    <span class="nav_name">Data Akun</span>
                </a>
                <a href="{{ route('data.presensi') }}"
                    class="nav_link {{ request()->routeIs('datapresensi.*') ? 'active' : '' }}">
                    <i class='bx bx-calendar-check nav_icon'></i>
                    <span class="nav_name">Data Aktivitas</span>
                </a>
                <a href="{{ route('penggajian.index') }}"
                    class="nav_link {{ request()->routeIs('penggajian.index') ? 'active' : '' }}">
                    <i class='bx bx-wallet nav_icon'></i>
                    <span class="nav_name">Penggajian</span>
                </a>
                <a href="{{ route('laporan.gaji') }}"
                    class="nav_link {{ request()->routeIs('laporan.gaji') ? 'active' : '' }}">
                    <i class='bx bx-bar-chart-alt-2 nav_icon'></i>
                    <span class="nav_name">Laporan</span>
                </a>
                <a href="{{ route('admin.foto.presensi') }}"
                    class="nav_link {{ request()->routeIs('admin.foto.presensi') ? 'active' : '' }}">
                    <i class='bx bx-photo-album nav_icon'></i>
                    <span class="nav_name">Foto Presensi</span>
                </a>
                  <a href="{{ route('izin.create') }}"
                    class="nav_link {{ request()->routeIs('izin.*') ? 'active' : '' }}">
                    <i class='bx bx-plus-medical nav_icon'></i>
                    <span class="nav_name">Izin & Sakit</span>
                </a>
            </div>
        </div>

        <a href="#" class="nav_link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class='bx bx-log-out nav_icon'></i>
            <span class="nav_name">Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
</div>