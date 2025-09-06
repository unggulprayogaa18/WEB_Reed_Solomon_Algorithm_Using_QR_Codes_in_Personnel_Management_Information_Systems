<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Penggajian - {{ config('app.name', 'Laravel') }}</title>

    {{-- Aset CSS Modern --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
        }
        .swal2-container { z-index: 1060 !important; }
        body { background-color: var(--light-bg); font-family: var(--font-family); }
        main { position: relative; margin-top: var(--header-height); padding: 1.5rem; transition: .5s; }
        .body-pd main { padding-left: calc(var(--nav-width) + 1rem); }
        .header { background-color: var(--primary-color); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .l-navbar { background-color: var(--primary-color); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .nav_logo, .nav_link { color: #fff; }
        .nav_link:hover { background-color: var(--primary-color-alt); }
        .nav_link.active { background-color: var(--primary-color-alt); }
        .nav_link.active::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background-color: #fff; }
        .page-title { font-weight: 700; }
        .card { border: 1px solid var(--border-color); border-radius: 0.75rem; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: #fcfdff; }
        .table thead th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; vertical-align: middle; }
        .table td, .table th { padding: 1rem; vertical-align: middle; }
        .table tbody tr:hover { background-color: rgba(62, 100, 85, 0.05); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-color-alt); border-color: var(--primary-color-alt); }
        .popover { max-width: 320px; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.1); z-index: 1056 !important; }
        
        .salary-status.calculated {
            background-color: #e9f5e9;
            color: #28a745;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        
        /* Gaya untuk baris detail yang bisa disembunyikan */
        tr.detail-row > td { padding: 0 !important; }
        .detail-content-wrapper { padding: 1.5rem; background-color: #f8f9fa; }

        /* Gaya Baru untuk Kotak Detail Presensi yang bisa di-scroll */
        .payroll-details-box {
            max-height: 180px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
        .payroll-detail-entry {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f3f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        .payroll-detail-entry:last-child {
            border-bottom: none;
        }
        .payroll-entry-activity {
            flex-grow: 1;
        }
        .payroll-entry-time {
            display: flex;
            gap: 1rem;
            color: #6c757d;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .payroll-entry-time span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
    </style>
</head>

<body id="body-pd">
    @include('layouts.sidebar')

    <main>
        <div class="page-header mb-4">
            <h2 class="page-title fw-bold">Manajemen Penggajian</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active" aria-current="page">Halaman / Data Penggajian</li></ol></nav>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="card-title mb-0 fw-bold">Filter Periode</h5></div>
            <div class="card-body">
                <form action="{{ route('penggajian.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="month" class="form-label">Bulan</label>
                            <select name="month" id="month" class="form-select">
                                @php
                                    $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                @endphp
                                @foreach ($bulanIndonesia as $angka => $nama)
                                    <option value="{{ $angka }}" {{ $selectedMonth == $angka ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="year" class="form-label">Tahun</label>
                            <select name="year" id="year" class="form-select">
                                @for ($i = \Carbon\Carbon::now()->year; $i >= \Carbon\Carbon::now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>Tampilkan Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0 fw-bold">Rekapitulasi Gaji Periode {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->locale('id')->isoFormat('MMMM YYYY') }}</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start">Nama Pegawai</th>
                                <th>Total Kehadiran</th>
                                <th>Total Jam Normal</th>
                                <th>Total Jam Lembur</th>
                                <th>Total Jam Kerja</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapPresensi as $userId => $data)
                            <tr>
                                <td class="text-start">
                                    <div class="fw-bold">{{ $data['nama_user'] }}</div>
                                    <small class="text-muted">{{ $data['detail_pekerjaan'] }}</small>
                                </td>
                                <td class="text-center"><strong>{{ $data['total_hari_kerja_terhitung'] }}</strong> Hari</td>
                                <td class="text-center"><strong>{{ $data['total_jam_normal_terhitung'] }}</strong> Jam</td>
                                <td class="text-center"><strong class="text-danger">{{ $data['total_jam_lembur_terhitung'] }}</strong> Jam</td>
                                <td class="text-center"><strong>{{ $data['total_jam_kerja_terhitung'] }}</strong> Jam</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetail{{ $userId }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                        @if ($data['total_hari_kerja_terhitung'] > 0 || $data['total_jam_kerja_terhitung'] > 0)
                                            @if ($data['existing_salary'])
                                                <div class="salary-status calculated">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    <strong>Rp {{ number_format($data['calculated_total_gaji'], 2, ',', '.') }}</strong>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-warning btn-sm salary-popover-btn" data-bs-toggle="popover" data-bs-placement="left" data-user-id="{{ $userId }}">
                                                    <i class="bi bi-calculator me-1"></i> Hitung Gaji
                                                </button>
                                            @endif
                                        @else
                                            <span class="badge text-bg-light text-muted fw-normal p-2">Tidak Ada Kehadiran</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="detail-row">
                                <td colspan="6">
                                    <div class="collapse" id="collapseDetail{{ $userId }}">
                                        <div class="detail-content-wrapper">
                                            <h6 class="mb-3"><strong>Detail Presensi:</strong> {{ $data['nama_user'] }}</h6>
                                            <div class="payroll-details-box">
                                                @forelse($data['daily_details'] as $detail)
                                                    @if($detail['jam_masuk']) {{-- Hanya tampilkan yang valid --}}
                                                    <div class="payroll-detail-entry">
                                                        <div class="payroll-entry-activity">
                                                            <strong>{{ \Carbon\Carbon::parse($detail['date'])->locale('id')->isoFormat('dddd, D MMM') }}</strong>
                                                            <div class="text-muted small">{{ $detail['nama_aktivitas'] }}</div>
                                                        </div>
                                                        <div class="payroll-entry-time">
                                                            <span><i class="bi bi-box-arrow-in-right"></i> {{ $detail['jam_masuk'] ?? '-' }}</span>
                                                            <span><i class="bi bi-box-arrow-right"></i> {{ $detail['jam_keluar'] ?? '-' }}</span>
                                                            <span><i class="bi bi-hourglass-split"></i> {{ $detail['durasi_jam'] > 0 ? $detail['durasi_jam'] . ' Jam' : '-' }}</span>
                                                            @if(isset($detail['jam_lembur_harian']) && $detail['jam_lembur_harian'] > 0)
                                                                <span class="text-danger" title="Jam Lembur"><i class="bi bi-clock-history"></i> {{ $detail['jam_lembur_harian'] . ' Jam' }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                @empty
                                                    <p class="text-muted text-center small p-3">Tidak ada detail presensi yang valid.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                                    Tidak ada data rekapitulasi untuk periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    {{-- TEMPLATE POPOVER --}}
    <div id="popover-content-template" class="d-none">
        <form action="{{ route('penggajian.calculate') }}" method="POST" class="salary-form p-2">
            @csrf
            <input type="hidden" name="user_id" class="form-input-user-id">
            <input type="hidden" name="total_monthly_hours" class="form-input-total-hours">
            <input type="hidden" name="total_days_attended" class="form-input-total-days">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Pilih Tipe Pembayaran</label>
                <select name="tipe_pembayaran" class="form-select form-select-sm payment-type-select">
                    <option value="bulanan">Gaji Bulanan</option>
                    <option value="harian">Gaji Harian</option>
                    <option value="per_jam">Gaji Per Jam</option>
                </select>
            </div>

            <div class="conditional-inputs mb-3"></div>

            <div class="mb-2">
                <label class="form-label small fw-bold">Tunjangan</label>
                <input type="number" name="tunjangan" class="form-control form-control-sm" value="0" required min="0" step="0.01">
            </div>
            
            <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Simpan & Hitung</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika notifikasi sukses dengan SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true
                });
            @endif

            const rekapData = @json($rekapPresensi);
            
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            popoverTriggerList.forEach(popoverTriggerEl => {
                const userId = popoverTriggerEl.getAttribute('data-user-id');
                if (!rekapData[userId]) return;

                const userData = rekapData[userId];
                
                const popoverContent = document.getElementById('popover-content-template').cloneNode(true);
                popoverContent.classList.remove('d-none');
                popoverContent.removeAttribute('id');

                popoverContent.querySelector('.form-input-user-id').value = userId;
                popoverContent.querySelector('.form-input-total-hours').value = userData.total_jam_kerja_terhitung;
                popoverContent.querySelector('.form-input-total-days').value = userData.total_hari_kerja_terhitung;
                
                const paymentSelect = popoverContent.querySelector('.payment-type-select');
                paymentSelect.value = userData.tipe_gaji;

                function generateTarifInput(type) {
                    let labelText = '';
                    let defaultTarif = (type === userData.tipe_gaji) ? userData.tarif_gaji_default : 0;

                    switch (type) {
                        case 'per_jam': labelText = 'Tarif Gaji Per Jam'; break;
                        case 'harian': labelText = 'Tarif Gaji Per Hari'; break;
                        default: labelText = 'Gaji Pokok Bulanan'; break;
                    }
                    const conditionalDiv = popoverContent.querySelector('.conditional-inputs');
                    conditionalDiv.innerHTML = `
                        <div class="mb-2">
                            <label class="form-label small fw-bold">${labelText}</label>
                            <input type="number" name="tarif_gaji" class="form-control form-control-sm" value="${defaultTarif}" required min="0" step="0.01">
                        </div>`;
                }

                generateTarifInput(paymentSelect.value);

                paymentSelect.addEventListener('change', function() {
                    generateTarifInput(this.value);
                });

                new bootstrap.Popover(popoverTriggerEl, {
                    html: true,
                    sanitize: false,
                    content: popoverContent,
                    title: `<span class="fw-bold">Hitung Gaji: ${userData.nama_user}</span>`
                });
            });

            document.addEventListener('click', function (e) {
                document.querySelectorAll('.salary-popover-btn').forEach(button => {
                    if (!button.contains(e.target) && !document.querySelector('.popover')?.contains(e.target)) {
                        const popoverInstance = bootstrap.Popover.getInstance(button);
                        if (popoverInstance) {
                            popoverInstance.hide();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>