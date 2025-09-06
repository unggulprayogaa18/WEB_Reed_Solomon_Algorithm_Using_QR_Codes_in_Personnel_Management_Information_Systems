<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Presensi - {{ config('app.name', 'Laravel') }}</title>

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
            --primary-color-alt: #3e6455;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #495057;
            --border-color: #dee2e6;
            --font-family: 'Inter', sans-serif;
        }

        .swal2-container {
            z-index: 9999 !important;
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

        .header {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .l-navbar {
            background-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .nav_logo,
        .nav_link {
            color: #fff;
        }

        .nav_logo-name {
            font-weight: 700;
        }

        .nav_link:hover {
            background-color: var(--primary-color-alt);
            color: #fff;
        }

        .nav_link.active {
            background-color: var(--primary-color-alt);
            color: #fff;
        }

        .nav_link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #fff;
        }

        .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            color: #212529;
            font-weight: 700;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }

        .card-header,
        .card-footer {
            background-color: transparent;
            padding: 1rem 1.5rem;
            border-color: var(--border-color);
        }

        .table-controls {
            padding: 1rem 1.5rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 122, 51, 0.25);
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 122, 51, 0.05);
        }

        .table td,
        .table th {
            padding: 1rem;
            vertical-align: middle;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-color-alt);
            border-color: var(--primary-color-alt);
        }

        .btn-action {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .offcanvas-header {
            background-color: var(--primary-color);
            color: white;
        }

        .offcanvas-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination .page-link {
            color: var(--primary-color);
        }
    </style>
</head>

<body id="body-pd">

    @include('layouts.sidebar')

    <main>
        <div class="page-header">
            <h2 class="page-title">Kelola Aktivitas</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">halaman / Data Aktivitas qr</li>
                </ol>
            </nav>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- SCRIPT PENCARIAN OTOMATIS ---
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('activitiesTableBody');
                const tableRows = tableBody.getElementsByTagName('tr');

                searchInput.addEventListener('keyup', function () {
                    const filter = searchInput.value.toLowerCase();
                    for (let row of tableRows) {
                        let rowText = row.textContent || row.innerText;
                        if (rowText.toLowerCase().includes(filter)) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                });

                // (Tempatkan script untuk form, sweetalert, dan download QR Anda di sini)
            });
        </script>
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">Daftar Aktivitas</h5>
                <div class="d-flex gap-2">
                    {{-- <form action="{{ route('activity.end_day') }}" method="POST" id="endDayForm" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class='bx bx-time-five me-2'></i>Akhiri Hari
                        </button>
                    </form> --}}
                    <button type="button" class="btn btn-primary btn-open-overlay" data-action="tambah">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Aktivitas
                    </button>
                </div>
            </div>

            <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <label for="showEntries" class="form-label me-2 mb-0">Tampil</label>
                    <select class="form-select form-select-sm" id="showEntries" style="width: auto;">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="card-body border-bottom">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Ketik untuk mencari aktivitas...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th class="text-start">Nama Aktivitas</th>
                                <th class="text-start">Deskripsi</th>
                                <th class="text-start">uuid</th>
                                <th class="qr-code-cell">QR Code</th>
                                <th>Waktu Dibuat</th>
                                <th class="action-cell">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="activitiesTableBody">
                            @forelse ($activities as $index => $record)
                                <tr>
                                    <td class="text-center">{{ $activities->firstItem() + $index }}</td>
                                    <td class="text-start">{{ $record->nama_aktivitas ?? 'N/A' }}</td>
                                    <td class="text-start">{{ $record->deskripsi ?? 'N/A' }}</td>
                                    <td class="text-start">{{ $record->uuid ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if ($record->qrcode_path)
                                            <img src="{{ asset($record->qrcode_path) }}" alt="QR Code"
                                                style="width: 50px; height: 50px;">
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $record->created_at->format('d/m/y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-action btn-open-overlay"
                                                title="Edit" data-action="edit" data-uuid="{{ $record->uuid }}"
                                                data-nama="{{ $record->nama_aktivitas }}"
                                                data-deskripsi="{{ $record->deskripsi }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-action"
                                                title="Unduh QR Code"
                                                onclick="downloadQrCodeAsRaster('{{ asset($record->qrcode_path) }}', '{{ 'qrcode-' . Str::slug($record->nama_aktivitas) . '-' . $record->uuid }}', 'png', 1000, 'white')">
                                                <i class="bi bi-download"></i>
                                            </button>
                                            <form action="{{ route('activity.destroy', $record->uuid) }}" method="POST"
                                                class="form-hapus d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                        Belum ada aktivitas yang dibuat hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($activities->hasPages())
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $activities->firstItem() }}</strong> sampai
                        <strong>{{ $activities->lastItem() }}</strong> dari <strong>{{ $activities->total() }}</strong>
                        entri
                    </div>
                    <div>
                        {{ $activities->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="formOverlay" aria-labelledby="formOverlayLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="formOverlayLabel">Form Aktivitas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="activityForm" method="POST">
                @csrf
                <div id="method-spoofing"></div>

                <div class="mb-3">
                    <label for="form_nama_aktivitas" class="form-label">Nama Aktivitas</label>
                    <input type="text" class="form-control" id="form_nama_aktivitas" name="nama_aktivitas" required>
                </div>
                <div class="mb-3">
                    <label for="form_deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="form_deskripsi" name="deskripsi" rows="3"></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100" id="formSubmitButton">Simpan</button>
                    <button type="button" class="btn btn-secondary w-100 mt-2"
                        data-bs-dismiss="offcanvas">Tutup</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <script>

        async function downloadQrCodeAsRaster(svgPath, fileNameBase, format, size, bgColor = 'transparent') {
            const loadingAlert = Swal.fire({
                title: 'Memproses QR Code...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const response = await fetch(svgPath);
                if (!response.ok) throw new Error(`Gagal memuat SVG: ${response.statusText}`);
                const svgText = await response.text();
                const img = new Image();
                img.src = 'data:image/svg+xml;base64,' + btoa(svgText);
                await new Promise((resolve, reject) => {
                    img.onload = resolve;
                    img.onerror = (err) => reject(new Error('Gagal memuat SVG ke dalam elemen gambar.'));
                });
                const canvas = document.createElement('canvas');
                canvas.width = size;
                canvas.height = size;
                const ctx = canvas.getContext('2d');
                const padding = size * 0.05;
                const qrSize = size - (2 * padding);
                if (bgColor === 'white') {
                    ctx.fillStyle = 'white';
                    ctx.fillRect(0, 0, size, size);
                }
                ctx.drawImage(img, padding, padding, qrSize, qrSize);
                const mimeType = `image/${format}`;
                const dataURL = canvas.toDataURL(mimeType);
                const a = document.createElement('a');
                a.href = dataURL;
                a.download = `${fileNameBase}.${format}`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                loadingAlert.close();
                Swal.fire('Berhasil!', `QR Code berhasil diunduh sebagai ${format.toUpperCase()}.`, 'success');
            } catch (error) {
                loadingAlert.close();
                console.error('Download error:', error);
                Swal.fire('Gagal', `Terjadi kesalahan saat mengunduh QR Code. <br><small>${error.message}</small>`, 'error');
            }
        }

        // Jalankan semua script interaktif setelah halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            const formOverlayEl = document.getElementById('formOverlay');
            const formOverlay = new bootstrap.Offcanvas(formOverlayEl);
            const form = document.getElementById('activityForm');
            const title = document.getElementById('formOverlayLabel');
            const submitButton = document.getElementById('formSubmitButton');
            const methodSpoofingDiv = document.getElementById('method-spoofing');

            document.querySelectorAll('.btn-open-overlay').forEach(button => {
                button.addEventListener('click', function () {
                    const action = this.getAttribute('data-action');
                    form.reset();
                    methodSpoofingDiv.innerHTML = '';

                    if (action === 'tambah') {
                        title.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Aktivitas Baru';
                        form.action = '{{ route("activity.store") }}';
                        submitButton.textContent = 'Simpan Aktivitas';
                    } else if (action === 'edit') {
                        const uuid = this.getAttribute('data-uuid');
                        title.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Aktivitas';

                        form.action = `{{ url('activities') }}/${uuid}`;

                        submitButton.textContent = 'Perbarui Aktivitas';
                        methodSpoofingDiv.innerHTML = '@method("PUT")';
                        document.getElementById('form_nama_aktivitas').value = this.getAttribute('data-nama');
                        document.getElementById('form_deskripsi').value = this.getAttribute('data-deskripsi');
                    }
                    formOverlay.show();
                });
            });

            document.querySelectorAll('.form-hapus').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            const endDayForm = document.getElementById('endDayForm');
            if (endDayForm) {
                endDayForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin mengakhiri presensi?',
                        text: "Sistem akan membuat data 'keluar' untuk semua yang belum checkout hari ini. Aksi ini tidak bisa dibatalkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Akhiri Presensi!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            endDayForm.submit();
                        }
                    });
                });
            }
        });
    </script>

    {{-- Script notifikasi dipisahkan dan hanya ada satu kali --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops... Terjadi Kesalahan',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
            // Buka kembali form jika ada error validasi
            const formOverlay = new bootstrap.Offcanvas(document.getElementById('formOverlay'));
            formOverlay.show();
        </script>
    @endif

</body>

</html>