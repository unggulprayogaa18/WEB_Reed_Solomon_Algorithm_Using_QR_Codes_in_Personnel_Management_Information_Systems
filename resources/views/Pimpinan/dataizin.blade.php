<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Pegawai - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        :root { --header-height: 3rem; --nav-width: 68px; --primary-color: #3e6455; --primary-color-alt: #315044; --light-bg: #f8f9fa; --card-bg: #ffffff; --text-color: #495057; --border-color: #dee2e6; --font-family: 'Inter', sans-serif; }
        .header, .l-navbar { background-color: var(--primary-color); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav_logo-icon { width: 25px; height: 25px; border-radius: 50%; object-fit: cover; }
        body { background-color: var(--light-bg); font-family: var(--font-family); color: var(--text-color); }
        main { position: relative; margin-top: var(--header-height); padding: 1.5rem; transition: .5s; }
        .body-pd main { padding-left: calc(var(--nav-width) + 1rem); }
        .nav_logo, .nav_link { color: #fff; }
        .nav_link:hover, .nav_link.active { background-color: var(--primary-color-alt); color: #fff; }
        .page-header { margin-bottom: 1.5rem; }
        .page-title { color: #212529; font-weight: 700; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .card { border: 1px solid var(--border-color); border-radius: 0.75rem; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .card-header, .card-footer { background-color: transparent; padding: 1rem 1.5rem; border-color: var(--border-color); }
        .table thead th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; vertical-align: middle; }
        .table tbody tr:hover { background-color: rgba(62, 100, 85, 0.05); }
        .table td, .table th { padding: 1rem; vertical-align: middle; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25); }
        .pagination .page-item.active .page-link { background-color: var(--primary-color); border-color: var(--primary-color); }
        .pagination .page-link { color: var(--primary-color); cursor: pointer; }
        .loading-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.8); display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 0.75rem; }
        .btn-toggle-view.active { background-color: var(--primary-color); color: white; }
    </style>
</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img"></div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="{{ route('dashboard.pemimpin') }}" class="nav_logo">
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon">
                    <span class="nav_logo-name">SIMPEG Al-Kasyaf</span>
                </a>
                <div class="nav_list">
                    <a href="{{ route('dashboard.pemimpin') }}" class="nav_link {{ request()->routeIs('dashboard.pemimpin') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="{{ route('izin.buat') }}" class="nav_link {{ request()->routeIs('izin.*') ? 'active' : '' }}">
                        <i class='bx bxs-calendar-edit nav_icon'></i> <span class="nav_name">Data Izin/Sakit</span>
                    </a>
                    <a href="{{ route('laporan.gajibypemimpin') }}" class="nav_link {{ request()->routeIs('laporan.gajibypemimpin') ? 'active' : '' }}">
                        <i class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Laporan</span>
                    </a>
                </div>
            </div>
            <a href="#" class="nav_link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Keluar</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
        </nav>
    </div>

    <main>
        <div class="page-header d-flex justify-content-between align-items-center">
            <h2 id="page-title" class="page-title">Laporan Gaji Pegawai</h2>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-toggle-view active" data-view="gaji">Laporan Gaji</button>
                <button type="button" class="btn btn-outline-secondary btn-toggle-view" data-view="kehadiran">Laporan Kehadiran</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="filter-form" class="pb-4 mb-4 border-bottom">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label for="periode" class="form-label fw-bold">Periode Bulan</label>
                            <input type="month" class="form-control" id="periode" name="periode" value="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <label for="nama" class="form-label fw-bold">Cari Nama Pegawai</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Ketik nama untuk memfilter...">
                        </div>
                        <div class="col-lg-3 col-md-12 text-end">
                            <button class="btn btn-success w-100" type="button" id="download-excel">
                                <i class="bi bi-download me-2"></i>Unduh Laporan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="position-relative">
                    <div id="loading" class="loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span> </div>
                    </div>
                    <div id="laporan-table-container"></div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-center">
                <nav id="pagination-container"></nav>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const state = {
            currentView: 'gaji', // 'gaji' atau 'kehadiran'
            gajiData: JSON.parse('{!! $usersJson !!}'),
            kehadiranData: [],
            filteredData: [],
            filters: {
                periode: document.getElementById('periode').value,
                nama: document.getElementById('nama').value,
            },
            pagination: { currentPage: 1, perPage: 10 },
        };

        const pageTitle = document.getElementById('page-title');
        const tableContainer = document.getElementById('laporan-table-container');
        const paginationContainer = document.getElementById('pagination-container');
        const loadingEl = document.getElementById('loading');
        const viewToggles = document.querySelectorAll('.btn-toggle-view');

        const fetchKehadiranData = async () => {
            loadingEl.classList.remove('d-none');
            try {
                const response = await fetch(`{{ route('laporan.kehadiran.data') }}?periode=${state.filters.periode}`);
                if (!response.ok) throw new Error('Gagal memuat data kehadiran.');
                state.kehadiranData = await response.json();
            } catch (error) {
                console.error('Fetch Error:', error);
                tableContainer.innerHTML = `<div class="alert alert-danger text-center">${error.message}</div>`;
            } finally {
                loadingEl.classList.add('d-none');
            }
        };

        const applyFiltersAndRender = () => {
            const lowerCaseNama = state.filters.nama.toLowerCase();
            const sourceData = state.currentView === 'gaji' ? state.gajiData : state.kehadiranData;
            
            state.filteredData = sourceData.filter(user => user.nama.toLowerCase().includes(lowerCaseNama));
            
            state.pagination.currentPage = 1;
            render();
        };

        const render = () => {
            const { currentPage, perPage } = state.pagination;
            const totalItems = state.filteredData.length;
            const totalPages = Math.ceil(totalItems / perPage);
            const startIndex = (currentPage - 1) * perPage;
            const paginatedData = state.filteredData.slice(startIndex, startIndex + perPage);

            if (state.currentView === 'gaji') {
                renderTableGaji(paginatedData, startIndex);
            } else {
                renderTableKehadiran(paginatedData, startIndex);
            }
            renderPagination(totalPages);
        };
        
        const renderTableGaji = (data, startIndex) => {
            const { periode } = state.filters;
            let tableHTML = `<div class="table-responsive"><table class="table table-striped table-hover text-center mb-0"><thead><tr>
                <th>No</th><th class="text-start">Nama Pegawai</th><th>Jabatan</th>
                <th>Total Jam</th><th>Tunjangan</th><th class="fw-bold">Total Diterima</th><th>Tanggal Bayar</th>
                </tr></thead><tbody>`;

            if (data.length === 0) {
                tableHTML += `<tr><td colspan="7" class="text-center text-muted py-5">Tidak ada data ditemukan.</td></tr>`;
            } else {
                data.forEach((user, index) => {
                    const gajiPeriodeIni = user.gaji.find(g => g.slip_gaji && g.slip_gaji.periode === periode);
                    tableHTML += `<tr><td>${startIndex + index + 1}</td><td class="text-start">${user.nama}</td><td>${user.profil?.detail_pekerjaan || 'N/A'}</td>`;
                    if (gajiPeriodeIni) {
                        const slip = gajiPeriodeIni.slip_gaji;
                        tableHTML += `<td>${gajiPeriodeIni.total_jam || 0} jam</td>
                            <td>Rp ${Number(slip.tunjangan || 0).toLocaleString('id-ID')}</td>
                            <td class="fw-bold">Rp ${Number(slip.total_gaji || 0).toLocaleString('id-ID')}</td>
                            <td>${new Date(gajiPeriodeIni.tanggal_penggajian).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})}</td>`;
                    } else {
                        tableHTML += `<td colspan="4" class="text-center text-muted fst-italic">Belum ada data penggajian</td>`;
                    }
                    tableHTML += `</tr>`;
                });
            }
            tableHTML += `</tbody></table></div>`;
            tableContainer.innerHTML = tableHTML;
        };
        
        const renderTableKehadiran = (data, startIndex) => {
            let tableHTML = `<div class="table-responsive"><table class="table table-striped table-hover text-center mb-0"><thead><tr>
                <th>No</th><th class="text-start">Nama Pegawai</th><th>Hari Kerja</th><th>Hadir</th><th>Izin</th><th>Izin Dinas</th><th>Sakit</th><th>Alpha</th>
                </tr></thead><tbody>`;

            if (data.length === 0) {
                tableHTML += `<tr><td colspan="8" class="text-center text-muted py-5">Tidak ada data ditemukan.</td></tr>`;
            } else {
                data.forEach((item, index) => {
                    tableHTML += `<tr>
                        <td>${startIndex + index + 1}</td><td class="text-start">${item.nama}</td>
                        <td>${item.jumlah_hari_kerja}</td><td>${item.hadir}</td><td>${item.izin}</td>
                        <td>${item.izin_dinas}</td><td>${item.sakit}</td><td>${item.alpha}</td>
                        </tr>`;
                });
            }
            tableHTML += `</tbody></table></div>`;
            tableContainer.innerHTML = tableHTML;
        };

        const renderPagination = (totalPages) => {
            if (totalPages <= 1) { paginationContainer.innerHTML = ''; return; }
            let paginationHTML = '<ul class="pagination mb-0">';
            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === state.pagination.currentPage ? 'active' : '';
                paginationHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
            paginationHTML += '</ul>';
            paginationContainer.innerHTML = paginationHTML;
        };

        const exportToExcel = () => {
            if (state.filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor.'); return;
            }
            let dataToExport, worksheet, filename;

            if (state.currentView === 'gaji') {
                dataToExport = state.filteredData.map((user, index) => {
                    const gajiPeriodeIni = user.gaji.find(g => g.slip_gaji && g.slip_gaji.periode === state.filters.periode);
                    return {
                        "No": index + 1, "Nama Pegawai": user.nama, "Jabatan": user.profil?.detail_pekerjaan || 'N/A',
                        "Total Jam": gajiPeriodeIni ? gajiPeriodeIni.total_jam : 0,
                        "Tunjangan": gajiPeriodeIni ? gajiPeriodeIni.slip_gaji.tunjangan : 0,
                        "Total Diterima": gajiPeriodeIni ? gajiPeriodeIni.slip_gaji.total_gaji : 0,
                        "Tanggal Bayar": gajiPeriodeIni ? new Date(gajiPeriodeIni.tanggal_penggajian).toLocaleDateString('id-ID') : 'N/A'
                    };
                });
                worksheet = XLSX.utils.json_to_sheet(dataToExport);
                filename = `laporan_gaji_${state.filters.periode}.xlsx`;
            } else {
                dataToExport = state.filteredData.map((item, index) => ({
                    "No": index + 1, "Nama Pegawai": item.nama, "Jabatan": item.jabatan,
                    "Jml Hari Kerja": item.jumlah_hari_kerja, "Hadir": item.hadir, "Izin": item.izin,
                    "Izin Dinas": item.izin_dinas, "Sakit": item.sakit, "Alpha": item.alpha,
                }));
                worksheet = XLSX.utils.json_to_sheet(dataToExport);
                filename = `laporan_kehadiran_${state.filters.periode}.xlsx`;
            }
            
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan");
            XLSX.writeFile(workbook, filename);
        };

        const switchView = async (view) => {
            if (state.currentView === view) return;
            state.currentView = view;
            
            pageTitle.textContent = view === 'gaji' ? 'Laporan Gaji Pegawai' : 'Laporan Kehadiran Pegawai';
            viewToggles.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            if (view === 'kehadiran' && state.kehadiranData.length === 0) {
                await fetchKehadiranData();
            }
            applyFiltersAndRender();
        };

        // Event Listeners
        viewToggles.forEach(btn => btn.addEventListener('click', () => switchView(btn.dataset.view)));

        document.getElementById('periode').addEventListener('change', async (e) => {
            state.filters.periode = e.target.value;
            // Jika di view kehadiran, fetch ulang data. Jika di gaji, langsung filter
            if(state.currentView === 'kehadiran') {
                await fetchKehadiranData();
            }
            applyFiltersAndRender();
        });
        document.getElementById('nama').addEventListener('input', (e) => {
            state.filters.nama = e.target.value;
            applyFiltersAndRender();
        });
        document.getElementById('download-excel').addEventListener('click', exportToExcel);
        paginationContainer.addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.matches('.page-link')) {
                const page = parseInt(e.target.dataset.page, 10);
                if (page !== state.pagination.currentPage) {
                    state.pagination.currentPage = page;
                    render();
                }
            }
        });

        // Initial Render
        applyFiltersAndRender();
    });
    </script>
</body>
</html>