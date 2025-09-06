<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Gaji Pegawai - {{ config('app.name', 'Laravel') }}</title>

    {{-- Aset CSS Modern --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    {{-- Gaya CSS Kustom --}}
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

        body {
            background-color: var(--light-bg);
            font-family: var(--font-family);
            color: var(--text-color);
        }

        main {
            position: relative;
            margin-top: var(--header-height);
            padding: 1.5rem;
            transition: .5s;
        }

        .body-pd main {
            padding-left: calc(var(--nav-width) + 1rem);
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .table td,
        .table th {
            padding: 1rem;
            vertical-align: middle;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 0.75rem;
        } .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body id="body-pd">
    @include('layouts.sidebar')

    <main>
        <div class="page-header mb-4">
            <h2 class="page-title fw-bold">Laporan Gaji Pegawai</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="filter-form" class="pb-4 mb-4 border-bottom">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label for="periode" class="form-label fw-bold">Periode Bulan</label>
                            <input type="month" class="form-control" id="periode" name="periode"
                                value="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="status" class="form-label fw-bold">Status Pekerjaan</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                @foreach ($jobStatuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="nama" class="form-label fw-bold">Cari Nama Pegawai</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                placeholder="Ketik nama...">
                        </div>
                        <div class="col-lg-3 col-md-6 text-end">
                            <button type="button" id="download-excel" class="btn btn-success w-100">
                                <i class="bi bi-download me-2"></i>Unduh Laporan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="position-relative">
                    <div id="loading" class="loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
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
            let allUsersData = JSON.parse('{!! $usersJson !!}');
            const tableContainer = document.getElementById('laporan-table-container');
            const paginationContainer = document.getElementById('pagination-container');
            const loadingEl = document.getElementById('loading');

            const state = {
                filters: {
                    periode: document.getElementById('periode').value,
                    status: document.getElementById('status').value,
                    nama: document.getElementById('nama').value,
                },
                pagination: {
                    currentPage: 1,
                    perPage: 15,
                },
                filteredData: []
            };

            const fetchAndRender = async () => {
                loadingEl.classList.remove('d-none');
                
                // Bangun URL dengan parameter query
                const params = new URLSearchParams({
                    periode: state.filters.periode
                }).toString();

                try {
                    // Ambil data baru dari server setiap kali periode berubah
                    const response = await fetch(`{{ route('admin.laporan.gaji') }}?${params}`);
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data dari server');
                    }
                    // Server akan merender ulang view dengan data JSON yang baru
                    const html = await response.text();
                    const newDoc = new DOMParser().parseFromString(html, 'text/html');
                    const newJsonScript = newDoc.querySelector('script:not([src])').textContent;
                    
                    // Ekstrak JSON baru dari script tag
                    const jsonString = newJsonScript.match(/JSON\.parse\('(.+?)'\);/s)[1].replace(/\\'/g, "'").replace(/\\\\/g, "\\");
                    allUsersData = JSON.parse(jsonString);

                    applyFilters(); // Terapkan filter nama & status di sisi client
                    render();
                } catch (error) {
                    console.error('Error:', error);
                    tableContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>`;
                } finally {
                    loadingEl.classList.add('d-none');
                }
            };
            
            const applyFilters = () => {
                const { status, nama } = state.filters;
                const lowerCaseNama = nama.toLowerCase();

                state.filteredData = allUsersData.filter(user => {
                    const nameMatch = !nama || user.nama.toLowerCase().includes(lowerCaseNama);
                    const statusMatch = !status || (user.profil && user.profil.detail_pekerjaan && user.profil.detail_pekerjaan.toLowerCase() === status.toLowerCase());
                    return nameMatch && statusMatch;
                });
            };

            const render = () => {
                const {
                    currentPage,
                    perPage
                } = state.pagination;
                const totalItems = state.filteredData.length;
                const totalPages = Math.ceil(totalItems / perPage);
                const startIndex = (currentPage - 1) * perPage;
                const endIndex = startIndex + perPage;
                const paginatedData = state.filteredData.slice(startIndex, endIndex);

                renderTable(paginatedData);
                renderPagination(totalPages);
            }

            const renderTable = (data) => {
                let tableHTML = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="text-start">Nama Pegawai</th>
                                <th>Jabatan</th>
                                <th>Total Jam</th>
                                <th>Jam Lembur</th>
                                <th>Tunjangan</th>
                                <th class="fw-bold">Total Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                if (data.length === 0) {
                    tableHTML += `<tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-search fs-2 d-block mb-2"></i> Tidak ada data yang cocok.</td></tr>`;
                } else {
                    data.forEach((user, index) => {
                        const no = state.pagination.perPage * (state.pagination.currentPage - 1) + index + 1;
                        const gajiData = user.gaji[0]; // Data hasil kalkulasi dari controller
                        const slip = gajiData.slip_gaji; // Data slip gaji jika ada

                        tableHTML += `
                            <tr>
                                <td>${no}</td>
                                <td class="text-start">${user.nama || 'N/A'}</td>
                                <td>${user.profil?.detail_pekerjaan || 'N/A'}</td>
                                <td>${gajiData.total_jam || 0} jam</td>
                                <td>${gajiData.total_jam_lembur || 0} jam</td>
                                <td>Rp ${Number(slip?.tunjangan || 0).toLocaleString('id-ID')}</td>
                                <td class="fw-bold">Rp ${Number(slip?.total_gaji || 0).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                }
                tableHTML += `</tbody></table></div>`;
                tableContainer.innerHTML = tableHTML;
            };

            const renderPagination = (totalPages) => {
                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }
                let paginationHTML = '<ul class="pagination mb-0">';
                for (let i = 1; i <= totalPages; i++) {
                    const activeClass = i === state.pagination.currentPage ? 'active' : '';
                    paginationHTML += `<li class="page-item ${activeClass}"><a class="page-link" data-page="${i}" href="#">${i}</a></li>`;
                }
                paginationHTML += '</ul>';
                paginationContainer.innerHTML = paginationHTML;
            };

            const exportToExcel = () => {
                // Gunakan data yang sudah difilter
                const dataToExport = state.filteredData.map(user => {
                        const gajiData = user.gaji[0];
                        const slip = gajiData.slip_gaji;
                        return {
                            "Nama Pegawai": user.nama,
                            "Jabatan": user.profil?.detail_pekerjaan || 'N/A',
                            "Periode Gaji": state.filters.periode,
                            "Total Jam Kerja (jam)": gajiData.total_jam || 0,
                            "Total Jam Lembur (jam)": gajiData.total_jam_lembur || 0,
                            "Tunjangan": slip?.tunjangan || 0,
                            "Total Gaji Diterima": slip?.total_gaji || 0,
                        };
                    });

                if (dataToExport.length === 0) {
                    alert('Tidak ada data gaji untuk diekspor pada filter ini.');
                    return;
                }

                const worksheet = XLSX.utils.json_to_sheet(dataToExport);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan Gaji");
                XLSX.writeFile(workbook, `laporan_gaji_${state.filters.periode}.xlsx`);
            };

            // --- Event Listeners ---
            document.getElementById('periode').addEventListener('change', (e) => {
                state.filters.periode = e.target.value;
                state.pagination.currentPage = 1;
                // Panggil ulang dari server karena periode berubah
                window.location.href = `{{ route('admin.laporan.gaji') }}?periode=${state.filters.periode}`;
            });
            
            ['status', 'nama'].forEach(id => {
                 document.getElementById(id).addEventListener('input', (e) => {
                    state.filters[id] = e.target.value;
                    state.pagination.currentPage = 1;
                    applyFilters(); // Filter client-side
                    render();
                });
            });

            paginationContainer.addEventListener('click', (e) => {
                if (e.target.matches('.page-link')) {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page, 10);
                    if (page !== state.pagination.currentPage) {
                        state.pagination.currentPage = page;
                        render();
                    }
                }
            });

            document.getElementById('download-excel').addEventListener('click', exportToExcel);

            // --- Initial Render ---
            applyFilters();
            render();
        });
    </script>
</body>

</html>