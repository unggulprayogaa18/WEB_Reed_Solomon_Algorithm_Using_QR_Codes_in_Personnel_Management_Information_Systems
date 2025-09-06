<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Pegawai - {{ config('app.name', 'Laravel') }}</title>

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

        .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
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

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25);
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

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #315044;
            border-color: #315044;
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

        .input-group .btn:focus {
            box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25);
        }
    </style>
</head>

<body id="body-pd">

    @include('layouts.sidebar')

    <main>
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Kelola Pegawai</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">halaman / Data Pegawai</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">Daftar Pegawai</h5>
                <button type="button" class="btn btn-primary btn-open-overlay" data-action="tambah">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pegawai
                </button>
            </div>
            <div class="card-body border-bottom">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Ketik untuk mencari aktivitas...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th class="text-start">Nama Lengkap</th>
                                <th>Posisi</th>
                                <th>Tgl. Lahir</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="activitiesTableBody">
                            @forelse ($pegawai as $index => $data)
                                <tr>
                                    <td class="text-center">{{ $index + $pegawai->firstItem() }}</td>
                                    <td class="text-start">{{ $data->nama }}</td>
                                    <td class="text-center">{{ $data->profil?->detail_pekerjaan ?? '-' }}</td>
                                    <td class="text-center">{{ $data->profil?->tanggal_lahir?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">{{ $data->no_telepon ?? '-' }}</td>
                                    <td class="text-center">{{ $data->profil?->alamat ?? '-' }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-action btn-open-overlay"
                                                title="Edit" data-action="edit" data-id="{{ $data->id_user }}"
                                                data-nama="{{ $data->nama }}" data-email="{{ $data->email }}"
                                                data-jabatan="{{ $data->jabatan }}"
                                                data-tanggal_lahir="{{ $data->profil?->tanggal_lahir?->format('Y-m-d') }}"
                                                data-no_telepon="{{ $data->no_telepon }}"
                                                data-alamat="{{ $data->profil?->alamat }}"
                                                data-detail_pekerjaan="{{ $data->profil?->detail_pekerjaan }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('pegawai.destroy', $data->id_user) }}" method="POST"
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
                                        <i class="bi bi-people-fill fs-2 d-block mb-2"></i>
                                        Tidak ada data pegawai untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($pegawai->hasPages())
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $pegawai->firstItem() }}</strong> sampai
                        <strong>{{ $pegawai->lastItem() }}</strong> dari <strong>{{ $pegawai->total() }}</strong> entri
                    </div>
                    <div>{{ $pegawai->links() }}</div>
                </div>
            @endif
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- SCRIPT PENCARIAN OTOMATIS ---
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('activitiesTableBody');
            const tableRows = tableBody.getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function () {
                const filter = searchInput.value.toLowerCase();
                for (let i = 0; i < tableRows.length; i++) {
                    let row = tableRows[i];
                    let rowText = row.textContent || row.innerText;
                    if (rowText.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });

            // --- SCRIPT UNTUK FORM OFFCANVAS ---
            const formOverlay = new bootstrap.Offcanvas(document.getElementById('formOverlay'));
            const form = document.getElementById('akunForm');
            // ... (sisa script form Anda yang panjang bisa ditempatkan di sini atau di include terpisah)
        });
    </script>
    {{-- Form Overlay (Offcanvas) --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="formOverlay" aria-labelledby="formOverlayLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="formOverlayLabel">Form Pegawai</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="pegawaiForm" method="POST">
                @csrf
                <div id="method-spoofing"></div>
                <div class="mb-3">
                    <label for="form_nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="form_nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="form_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="form_email" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" value="pegawai" readonly>
                </div>
                <div class="mb-3">
                    <label for="form_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="form_tanggal_lahir" name="tanggal_lahir" required>
                </div>
                <div class="mb-3">
                    <label for="form_no_telepon" class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" id="form_no_telepon" name="no_telepon" required>
                </div>
                <div class="mb-3">
                    <label for="form_alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="form_alamat" name="alamat" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label for="form_detail_pekerjaan" class="form-label">Detail Pekerjaan</label>
                    <select class="form-select" id="form_detail_pekerjaan" name="detail_pekerjaan" required>
                        <option value="" disabled selected>Pilih jenis pekerjaan</option>
                        <option value="Pengajar">Pengajar</option>
                        <option value="Staf">Staf</option>
                        <option value="Tukang Cuci">Tukang Cuci</option>
                        <option value="Tukang Masak">Tukang Masak</option>
                        <option value="Magang">Magang</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div class="mb-3 d-none" id="pekerjaan_lainnya_wrapper">
                    <label for="form_pekerjaan_lainnya" class="form-label">Sebutkan Pekerjaan Lainnya</label>
                    <input type="text" class="form-control" id="form_pekerjaan_lainnya" name="pekerjaan_lainnya">
                </div>
                <hr>
                <div id="password-section" class="d-none">
                    <p class="text-muted small" id="password-help-text">Isi untuk membuat password baru.</p>
                    <div class="mb-3">
                        <label for="form_password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="form_password" name="password"
                                autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                                <i class="bi bi-eye-slash-fill"></i>
                            </button>
                        </div>
                    </div>
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
        let formOverlay;

        document.addEventListener('DOMContentLoaded', function () {
            formOverlay = new bootstrap.Offcanvas(document.getElementById('formOverlay'));
            const form = document.getElementById('pegawaiForm');
            const title = document.getElementById('formOverlayLabel');
            const submitButton = document.getElementById('formSubmitButton');
            const methodSpoofingDiv = document.getElementById('method-spoofing');
            const passwordInput = document.getElementById('form_password');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const detailPekerjaanSelect = document.getElementById('form_detail_pekerjaan');
            const pekerjaanLainnyaWrapper = document.getElementById('pekerjaan_lainnya_wrapper');
            const pekerjaanLainnyaInput = document.getElementById('form_pekerjaan_lainnya');

            // --- Logika untuk toggle password visibility ---
            togglePasswordBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye-slash-fill');
                icon.classList.toggle('bi-eye-fill');
            });

            // --- Logika untuk combobox "Lainnya" ---
            detailPekerjaanSelect.addEventListener('change', function () {
                if (this.value === 'Lainnya') {
                    pekerjaanLainnyaWrapper.classList.remove('d-none');
                    pekerjaanLainnyaInput.required = true;
                } else {
                    pekerjaanLainnyaWrapper.classList.add('d-none');
                    pekerjaanLainnyaInput.required = false;
                    pekerjaanLainnyaInput.value = '';
                }
            });

            // --- Logika untuk membuka form (Tambah/Edit) ---
            document.querySelectorAll('.btn-open-overlay').forEach(button => {
                button.addEventListener('click', function () {
                    const action = this.getAttribute('data-action');
                    form.reset();
                    methodSpoofingDiv.innerHTML = '';
                    passwordInput.required = false;

                    // Reset field "Lainnya"
                    pekerjaanLainnyaWrapper.classList.add('d-none');
                    pekerjaanLainnyaInput.required = false;
                    pekerjaanLainnyaInput.value = '';

                    // Reset password visibility
                    passwordInput.setAttribute('type', 'password');
                    const passIcon = togglePasswordBtn.querySelector('i');
                    if (passIcon.classList.contains('bi-eye-fill')) {
                        passIcon.classList.remove('bi-eye-fill');
                        passIcon.classList.add('bi-eye-slash-fill');
                    }

                    if (action === 'tambah') {
                        title.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Pegawai Baru';
                        form.action = '{{ route("pegawai.store") }}';
                        submitButton.textContent = 'Simpan Pegawai';
                        document.getElementById('password-help-text').textContent = 'Password default akan dibuat.';
                        // passwordInput.required = true; // Baris ini sudah dihapus/dikomentari
                        passwordInput.value = 'password123';
                    } else if (action === 'edit') {
                        const id = this.getAttribute('data-id');
                        title.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Data Pegawai';
                        form.action = `{{ url('pegawai') }}/${id}`;
                        submitButton.textContent = 'Perbarui Data';
                        document.getElementById('password-help-text').textContent = 'Kosongkan jika tidak ingin mengubah password.';
                        methodSpoofingDiv.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                        // Isi semua data dari atribut data-*
                        document.getElementById('form_nama').value = this.getAttribute('data-nama');
                        document.getElementById('form_email').value = this.getAttribute('data-email');
                        document.getElementById('form_tanggal_lahir').value = this.getAttribute('data-tanggal_lahir');
                        document.getElementById('form_no_telepon').value = this.getAttribute('data-no_telepon');
                        document.getElementById('form_alamat').value = this.getAttribute('data-alamat');

                        // Logika khusus untuk combobox saat edit
                        const detailPekerjaan = this.getAttribute('data-detail_pekerjaan');
                        const isOptionExist = [...detailPekerjaanSelect.options].some(o => o.value === detailPekerjaan);

                        if (isOptionExist) {
                            detailPekerjaanSelect.value = detailPekerjaan;
                        } else if (detailPekerjaan) {
                            detailPekerjaanSelect.value = 'Lainnya';
                            pekerjaanLainnyaWrapper.classList.remove('d-none');
                            pekerjaanLainnyaInput.value = detailPekerjaan;
                            pekerjaanLainnyaInput.required = true;
                        }
                    }
                    formOverlay.show();
                });
            });

            // --- Logika untuk SweetAlert hapus ---
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
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error', title: 'Oops... Terjadi Kesalahan Validasi', html: `{!! implode('<br>', $errors->all()) !!}`
            });
            if (formOverlay) {
                formOverlay.show();
            }
        </script>
    @endif
</body>

</html>