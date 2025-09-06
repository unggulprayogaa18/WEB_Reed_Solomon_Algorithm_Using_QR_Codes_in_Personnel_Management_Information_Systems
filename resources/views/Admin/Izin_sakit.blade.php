<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulir Izin & Sakit - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Library PDF versi stabil --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    {{-- Library untuk Export Excel --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --primary-color: #3e6455;
            --primary-color-alt: #345447;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #495057;
            --border-color: #dee2e6;
            --font-family: 'Inter', sans-serif;
        }
        .nav_logo-icon { width: 25px; height: 25px; border-radius: 50%; object-fit: cover; }
        .swal2-container { z-index: 1056 !important; }
        body { background-color: var(--light-bg); font-family: var(--font-family); color: var(--text-color); }
        main { position: relative; margin-top: var(--header-height); padding: 1.5rem; transition: .5s; }
        .body-pd main { padding-left: calc(var(--nav-width) + 1rem); }
        .header { background-color: var(--primary-color); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
        .l-navbar { background-color: var(--primary-color); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .nav_logo, .nav_link { color: #fff; }
        .nav_logo-name { font-weight: 700; }
        .nav_link:hover { background-color: var(--primary-color-alt); color: #fff; }
        .nav_link.active { background-color: var(--primary-color-alt); color: #fff; }
        .nav_link.active::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background-color: #fff; }
        .page-header { margin-bottom: 1.5rem; }
        .page-title { color: #212529; font-weight: 700; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .card { border: 1px solid var(--border-color); border-radius: 0.75rem; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; padding: 1rem 1.5rem; border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500; }
        .btn-primary:hover { background-color: var(--primary-color-alt); border-color: var(--primary-color-alt); }
        .table thead th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }
        .table td, .table th { padding: 1rem; vertical-align: middle; }
        .btn-action { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; }
        .offcanvas-header { background-color: var(--primary-color); color: white; }
        .offcanvas-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .btn-toggle-presensi { font-size: 1.5rem; color: var(--primary-color); cursor: pointer; }
        .btn-view-toggle.active { background-color: var(--primary-color); color: white; border-color: var(--primary-color); }
        .collapse-row .collapse-content { padding: 0 !important; border-top: none; }
        .collapse-row.show { background-color: #f8f9fa; }
        .employee-row { cursor: pointer; }
    </style>
</head>

<body id="body-pd">

    @include('layouts.sidebar')

    <main>
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Kelola Data Izin / Sakit</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active" aria-current="page">Halaman / Data Izin & Sakit</li></ol></nav>
                    </ol>
                </nav>
            </div>
            <div class="btn-group" role="group" aria-label="View Toggles">
                <button type="button" id="btn-show-form" class="btn btn-outline-secondary btn-view-toggle">
                    <i class="bi bi-pencil-square me-2"></i>Form Input
                </button>
                <button type="button" id="btn-show-rekap" class="btn btn-outline-secondary btn-view-toggle">
                    <i class="bi bi-search me-2"></i>Rekap Data
                </button>
            </div>
        </div>

        {{-- ===== WRAPPER FOR THE FORM VIEW ===== --}}
        <div id="form-wrapper">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Formulir Data</h5>
                </div>
                <form action="{{ route('izin.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_karyawan" class="form-label">Nama Pegawai</label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="nama_karyawan" name="user_id" required>
                                        <option selected disabled value="">Pilih Pegawai...</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id_user }}" data-pekerjaan="{{ $employee->profil?->detail_pekerjaan ?? 'Belum diatur' }}" {{ old('user_id') == $employee->id_user ? 'selected' : '' }}>
                                            {{ $employee->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="detail_pekerjaan" class="form-label">Detail Pekerjaan / Jabatan</label>
                                    <input type="text" class="form-control" id="detail_pekerjaan" name="detail_pekerjaan" placeholder="Akan terisi otomatis..." value="{{ old('detail_pekerjaan') }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="aktivitas" class="form-label">Aktivitas/Tugas yang Ditinggalkan</label>
                                    <select class="form-select @error('aktivitas') is-invalid @enderror" id="aktivitas" name="aktivitas" required>
                                        <option selected disabled value="">Pilih Aktivitas...</option>
                                        @foreach($activities as $activity)
                                        <option value="{{ $activity->id_activity }}" {{ old('aktivitas') == $activity->id_activity ? 'selected' : '' }}>
                                            {{ $activity->nama_aktivitas }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('aktivitas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_izin" class="form-label">Tanggal Tidak Masuk</label>
                                    <input type="date" class="form-control @error('tanggal_izin') is-invalid @enderror" id="tanggal_izin" name="tanggal_izin" value="{{ old('tanggal_izin', date('Y-m-d')) }}" required>
                                    @error('tanggal_izin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Buat Presensi Otomatis?</label>
                                    <i class="bi bi-toggle-on btn-toggle-presensi" id="toggle-presensi"></i>
                                </div>
                                <input type="hidden" name="buat_presensi" id="buat_presensi_input" value="1">
                                <div id="jam-presensi-wrapper">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="jam_masuk" class="form-label">Dari Jam</label>
                                            <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}" required>
                                            @error('jam_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="jam_keluar" class="form-label">Sampai Jam</label>
                                            <input type="time" class="form-control @error('jam_keluar') is-invalid @enderror" id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar') }}" required>
                                            @error('jam_keluar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <select class="form-select @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" required>
                                        <option value="" selected disabled>Pilih Keterangan...</option>
                                        <option value="Izin" {{ old('keterangan') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="Sakit" {{ old('keterangan') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="Alpha" {{ old('keterangan') == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                                        <option value="Izin Dinas" {{ old('keterangan') == 'Izin Dinas' ? 'selected' : '' }}>Izin Dinas</option>
                                    </select>
                                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <input type="hidden" name="jenis_pembayaran" value="harian">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send me-2"></i>Simpan </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== WRAPPER FOR THE RECAP VIEW ===== --}}
        <div id="rekap-wrapper" style="display: none;">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 fw-bold "><i class="bi bi-filter me-2"></i> Rekap Data Kehadiran</h5>
                </div>
                <form action="{{ route('izin.create') }}" method="GET">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4" hidden>
                                <label for="search_user_id" class="form-label">Nama Pegawai</label>
                                <select name="user_id" id="search_user_id" class="form-select">
                                    <option value="">Semua Pegawai</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id_user }}" {{ request('user_id') == $employee->id_user ? 'selected' : '' }}>
                                        {{ $employee->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search_bulan" class="form-label">Bulan</label>
                                <select name="bulan" id="search_bulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    @php
                                        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                    @endphp
                                    @foreach ($bulanIndonesia as $angka => $nama)
                                    <option value="{{ $angka }}" {{ request('bulan') == $angka ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search_tahun" class="form-label">Tahun</label>
                                <input type="number" name="tahun" id="search_tahun" class="form-control" placeholder="Contoh: {{ date('Y') }}" value="{{ request('tahun', date('Y')) }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>{{ $title }}</h5>
                    <div class="btn-group">
                        <button id="exportExcelBtn" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel-fill me-2"></i>Excel</button>
                        <button id="exportPdfBtn" class="btn btn-sm btn-danger"><i class="bi bi-file-earmark-pdf-fill me-2"></i>PDF</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="rekapTable">
                           <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Pegawai</th>
                                    <th class="text-center">Total Tidak Hadir</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapData as $userId => $details)
                                <tr class="employee-row" data-bs-toggle="collapse" data-bs-target="#detail-{{ $userId }}" aria-expanded="false" aria-controls="detail-{{ $userId }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $details->first()->user->nama ?? 'Nama Tidak Ditemukan' }}</div>
                                        <small class="text-muted">{{ $details->first()->user->profil->detail_pekerjaan ?? 'Jabatan Belum Diatur' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark fs-6">{{ $details->count() }} hari</span>
                                    </td>
                                    <td class="text-center">
                                        <i class="bi bi-chevron-down"></i>
                                    </td>
                                </tr>
                                <tr class="collapse-row">
                                    <td colspan="4" class="p-0">
                                        <div class="collapse" id="detail-{{ $userId }}">
                                            <div class="p-3">
                                                <h6 class="mb-3">Rincian Absensi: <strong>{{ $details->first()->user->nama }}</strong></h6>
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Aktivitas Ditinggalkan</th>
                                                            <th>Keterangan</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($details as $item)
                                                        <tr>
                                                            <td>{{ $item->tanggal_izin->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                                                            <td>{{ $item->aktivitas }}</td>
                                                            <td>{{ $item->keterangan }}</td>
                                                            <td class="text-center">
                                                                <span class="badge bg-{{ $item->status === 'disetujui' ? 'success' : 'danger' }}">{{ ucfirst($item->status) }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                        @if($is_search)
                                            Tidak ada data yang cocok dengan kriteria pencarian.
                                        @else
                                            Belum ada riwayat pengajuan.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Autofill for create form ---
        const karyawanSelect = document.getElementById('nama_karyawan');
        if (karyawanSelect) {
            const pekerjaanInput = document.getElementById('detail_pekerjaan');
            karyawanSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                pekerjaanInput.value = selectedOption.getAttribute('data-pekerjaan') || '';
            });
            if (karyawanSelect.value) { karyawanSelect.dispatchEvent(new Event('change')); }
        }

        // --- Reusable function to handle the presensi toggle ---
        function setupPresensiToggle(toggleId, wrapperId, hiddenInputId, jamMasukId, jamKeluarId) {
            const toggleButton = document.getElementById(toggleId);
            if (!toggleButton) return;
            const wrapper = document.getElementById(wrapperId);
            const hiddenInput = document.getElementById(hiddenInputId);
            const jamMasuk = document.getElementById(jamMasukId);
            const jamKeluar = document.getElementById(jamKeluarId);
            const updateState = (isActive) => {
                if (isActive) {
                    hiddenInput.value = '1';
                    toggleButton.classList.replace('bi-toggle-off', 'bi-toggle-on');
                    wrapper.style.display = 'block';
                    jamMasuk.required = true; jamKeluar.required = true;
                } else {
                    hiddenInput.value = '0';
                    toggleButton.classList.replace('bi-toggle-on', 'bi-toggle-off');
                    wrapper.style.display = 'none';
                    jamMasuk.required = false; jamKeluar.required = false;
                }
            };
            updateState(hiddenInput.value === '1');
            toggleButton.addEventListener('click', function () {
                updateState(hiddenInput.value !== '1');
            });
        }
        setupPresensiToggle('toggle-presensi', 'jam-presensi-wrapper', 'buat_presensi_input', 'jam_masuk', 'jam_keluar');

        // ==========================================================
        // ===== START: UPDATED EXPORT LOGIC (PDF & EXCEL) =====
        // ==========================================================

        // --- Function to gather detailed data for export ---
        function getDetailedExportData() {
            const data = [];
            const employeeRows = document.querySelectorAll('#rekapTable > tbody > tr.employee-row');
            let counter = 1;

            employeeRows.forEach(row => {
                const nama = row.querySelector('td:nth-child(2) .fw-bold').textContent.trim();
                const jabatan = row.querySelector('td:nth-child(2) .text-muted').textContent.trim();
                
                // Find the corresponding detail rows inside the next sibling element
                const detailContainer = row.nextElementSibling;
                if (detailContainer) {
                    const detailRows = detailContainer.querySelectorAll('.collapse table tbody tr');
                    detailRows.forEach(detailRow => {
                        const cells = detailRow.querySelectorAll('td');
                        const tanggal = cells[0].textContent.trim();
                        const aktivitas = cells[1].textContent.trim();
                        const keterangan = cells[2].textContent.trim();

                        data.push([
                            counter++,
                            nama,
                            jabatan,
                            tanggal,
                            aktivitas,
                            keterangan,
                        ]);
                    });
                }
            });
            return data;
        }

        // --- PDF EXPORT LOGIC ---
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape' });
                
                const title = document.querySelector('#rekap-wrapper .card-title').textContent.trim();
                const tableData = getDetailedExportData();
                const tableHeaders = ['No', 'Nama Pegawai', 'Jabatan', 'Tanggal', 'Aktivitas Ditinggalkan', 'Keterangan'];

                doc.text(title, 14, 15);
                
                doc.autoTable({
                    head: [tableHeaders],
                    body: tableData,
                    startY: 20,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [62, 100, 85] // Warna hijau primary-color
                    },
                    styles: {
                        fontSize: 8
                    },
                    columnStyles: {
                        0: { cellWidth: 10 }, // No
                        1: { cellWidth: 40 }, // Nama
                        2: { cellWidth: 30 }, // Jabatan
                        3: { cellWidth: 30 }, // Tanggal
                        4: { cellWidth: 'auto' },// Aktivitas
                        5: { cellWidth: 25 }, // Keterangan
                        6: { cellWidth: 20 }  // Status
                    }
                });

                doc.save(`rekap-lengkap-izin-${new Date().toISOString().slice(0,10)}.pdf`);
            });
        }
        
        // --- EXCEL EXPORT LOGIC ---
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                const tableHeaders = ['No', 'Nama Pegawai', 'Jabatan', 'Tanggal', 'Aktivitas Ditinggalkan', 'Keterangan'];
                const tableData = getDetailedExportData();
                
                // Combine headers and data
                const excelData = [tableHeaders, ...tableData];

                const worksheet = XLSX.utils.aoa_to_sheet(excelData);

                // Set column widths
                worksheet['!cols'] = [
                    { wch: 5 },   // No
                    { wch: 30 },  // Nama Pegawai
                    { wch: 25 },  // Jabatan
                    { wch: 20 },  // Tanggal
                    { wch: 40 },  // Aktivitas
                    { wch: 15 },  // Keterangan
                    { wch: 15 }   // Status
                ];

                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Rekap Izin Lengkap");

                XLSX.writeFile(workbook, `rekap-lengkap-izin-${new Date().toISOString().slice(0,10)}.xlsx`);
            });
        }
        // ========================================================
        // ===== END: UPDATED EXPORT LOGIC (PDF & EXCEL) =====
        // ========================================================


        // ===== VIEW TOGGLING LOGIC =====
        const formWrapper = document.getElementById('form-wrapper');
        const rekapWrapper = document.getElementById('rekap-wrapper');
        const btnShowForm = document.getElementById('btn-show-form');
        const btnShowRekap = document.getElementById('btn-show-rekap');
        const hasErrors = {{ $errors->any() ? 'true' : 'false' }};

        function showFormView() {
            formWrapper.style.display = 'block';
            rekapWrapper.style.display = 'none';
            btnShowForm.classList.add('active');
            btnShowRekap.classList.remove('active');
        }

        function showRekapView() {
            formWrapper.style.display = 'none';
            rekapWrapper.style.display = 'block';
            btnShowRekap.classList.add('active');
            btnShowForm.classList.remove('active');
        }
        
        if (hasErrors) {
            showFormView();
        } else {
            // Check for search query params to decide initial view
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('bulan') || urlParams.has('tahun')) {
                showRekapView();
            } else {
                // Default view based on what you prefer. Let's make Rekap default.
                showRekapView();
            }
        }

        btnShowForm.addEventListener('click', showFormView);
        btnShowRekap.addEventListener('click', showRekapView);
    });
    </script>

    @if (session('success'))
        <script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false });</script>
    @endif
    @if ($errors->any() || session('error'))
        <script>
            Swal.fire({ 
                icon: 'error', 
                title: 'Oops... Terjadi Kesalahan', 
                html: '@if(session("error")){{ session("error") }}<br>@endif' + '{!! implode("<br>", $errors->all()) !!}' 
            });
        </script>
    @endif
</body>
</html>