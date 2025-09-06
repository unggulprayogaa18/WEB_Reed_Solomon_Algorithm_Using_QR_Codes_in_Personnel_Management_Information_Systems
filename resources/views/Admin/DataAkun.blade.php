<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Akun - {{ config('app.name', 'Laravel') }}</title>

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
            --primary-color: #3e6455; /* Warna hijau utama */
            --primary-color-alt: #3e6455; /* Warna hijau lebih gelap untuk hover */
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #495057;
            --border-color: #dee2e6;
            --font-family: 'Inter', sans-serif;
        }

        /* === GLOBAL & LAYOUT === */
        .swal2-container { z-index: 9999 !important; }

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

        /* === STYLING SIDEBAR BARU === */
        .header {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .l-navbar {
            background-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .nav_logo, .nav_link {
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

        .page-header { margin-bottom: 1.5rem; }
        .page-title { color: #212529; font-weight: 700; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }
        .card-header, .card-footer {
            background-color: transparent; padding: 1rem 1.5rem; border-color: var(--border-color);
        }

        .table-controls { padding: 1rem 1.5rem; }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(62, 100, 85, 0.25);
        }

        .table thead th {
            background-color: #f8f9fa; font-weight: 600; text-transform: uppercase;
            font-size: 0.8rem; vertical-align: middle;
        }
        .table tbody tr:hover { background-color: rgba(62, 100, 85, 0.05); }
        .table td, .table th { padding: 1rem; vertical-align: middle; }
        
        .btn-primary {
            background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500;
        }
        .btn-primary:hover {
            background-color: var(--primary-color-alt); border-color: var(--primary-color-alt);
        }
        .btn-action {
            width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
        }

        .offcanvas-header { background-color: var(--primary-color); color: white; }
        .offcanvas-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        .pagination .page-item.active .page-link { background-color: var(--primary-color); border-color: var(--primary-color); }
        .pagination .page-link { color: var(--primary-color); }
        
        /* START: CSS Tambahan untuk Password Toggle */
        .input-group .form-control {
            border-right: 0;
        }
        .input-group .form-control:focus {
            box-shadow: none; /* Menghilangkan shadow saat input-group aktif */
        }
        .input-group-text.toggle-password-icon {
            border-left: 0;
            background-color: transparent;
            cursor: pointer;
        }
        /* END: CSS Tambahan untuk Password Toggle */
    </style>
</head>
<body id="body-pd">
    
    @include('layouts.sidebar')

    <main>
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Kelola Akun</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">halaman / Data Akun</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">Daftar Akun Pengguna</h5>
                <button type="button" class="btn btn-primary btn-open-overlay" data-action="tambah">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Akun
                </button>
            </div>
            
            <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <label for="showEntries" class="form-label me-2 mb-0">Tampil</label>
                        <select class="form-select form-select-sm" id="showEntries" style="width: auto;">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="filterJabatan" class="form-label me-2 mb-0">Jabatan</label>
                        <select class="form-select form-select-sm" id="filterJabatan" style="width: auto;">
                            <option value="" selected>Semua Jabatan</option>
                            <option value="admin">Admin</option>
                            <option value="pemimpin">Pemimpin</option>
                            <option value="pegawai">Pegawai</option>
                        </select>
                    </div>
                    </div>
                <div class="input-group" style="width: auto; max-width: 300px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th class="text-start">Nama</th>
                                <th class="text-start">Email</th>
                                <th>Jabatan</th>
                                <th>No. Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="akunTableBody">
                            @forelse ($auth as $index => $userItem)
                                <tr>
                                    <td class="text-center">{{ $index + $auth->firstItem() }}</td>
                                    <td class="text-start">{{ $userItem->nama }}</td>
                                    <td class="text-start">{{ $userItem->email }}</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-25 text-success-emphasis rounded-pill px-2">{{ ucfirst($userItem->jabatan) }}</span></td>
                                    <td class="text-center">{{ $userItem->no_telepon ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-action btn-open-overlay" title="Edit"
                                                data-action="edit"
                                                data-id="{{ $userItem->id_user }}" data-nama="{{ $userItem->nama }}"
                                                data-email="{{ $userItem->email }}" data-jabatan="{{ $userItem->jabatan }}"
                                                data-no_telepon="{{ $userItem->no_telepon }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('akun.destroy', $userItem->id_user) }}" method="POST" class="form-hapus d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                                        Tidak ada data akun untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $auth->firstItem() }}</strong> sampai
                        <strong>{{ $auth->lastItem() }}</strong> dari <strong>{{ $auth->total() }}</strong> entri
                    </div>
                    <div>{{ $auth->links() }}</div>
                </div>
            
        </div>
    </main>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="formOverlay" aria-labelledby="formOverlayLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="formOverlayLabel">Form Akun</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="akunForm" method="POST" action="{{ old('id_user') ? url('akun/' . old('id_user')) : route('akun.store') }}">
                @csrf
                <div id="method-spoofing">
                    @if(old('id_user'))
                        @method('PUT')
                    @endif
                </div>
                
                <input type="hidden" id="form_id_user" name="id_user" value="{{ old('id_user') }}">

                <div class="mb-3">
                    <label for="form_nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="form_nama" name="nama" value="{{ old('nama') }}" required>
                </div>
                <div class="mb-3">
                    <label for="form_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="form_email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label for="form_jabatan" class="form-label">Jabatan</label>
                    <select class="form-select" id="form_jabatan" name="jabatan" required>
                        <option value="" disabled {{ old('jabatan') ? '' : 'selected' }}>Pilih Jabatan</option>
                        <option value="admin" {{ old('jabatan') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pemimpin" {{ old('jabatan') == 'pemimpin' ? 'selected' : '' }}>Pemimpin</option>
                        <option value="pegawai" {{ old('jabatan') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="form_no_telepon" class="form-label">No. Telepon (Opsional)</label>
                    <input type="text" class="form-control" id="form_no_telepon" name="no_telepon" value="{{ old('no_telepon') }}">
                </div>
                <hr>
                <div id="password-section">
                    <p class="text-muted small" id="password-help-text">Isi untuk membuat password baru.</p>
                    
                    <div class="mb-3">
                        <label for="form_password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="form_password" name="password">
                            <span class="input-group-text toggle-password-icon">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="form_password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="form_password_confirmation" name="password_confirmation">
                             <span class="input-group-text toggle-password-icon">
                                 <i class="bi bi-eye-slash"></i>
                             </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100" id="formSubmitButton">Simpan</button>
                    <button type="button" class="btn btn-secondary w-100 mt-2" data-bs-dismiss="offcanvas">Tutup</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 🔵 START: SCRIPT PASSWORD TOGGLE
            document.querySelectorAll('.toggle-password-icon').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            });
            // 🔵 END: SCRIPT PASSWORD TOGGLE

            // 🟢 START: SCRIPT FILTER & PENCARIAN OTOMATIS
            const searchInput = document.getElementById('searchInput');
            const jabatanFilter = document.getElementById('filterJabatan');
            const tableBody = document.getElementById('akunTableBody');
            const tableRows = tableBody.getElementsByTagName('tr');

            function applyFilters() {
                const searchText = searchInput.value.toLowerCase();
                const selectedJabatan = jabatanFilter.value.toLowerCase();
                for (let i = 0; i < tableRows.length; i++) {
                    const row = tableRows[i];
                    const firstCell = row.getElementsByTagName('td')[0];
                    if (firstCell && firstCell.hasAttribute('colspan')) {
                        continue;
                    }
                    const rowText = (row.textContent || row.innerText).toLowerCase();
                    const jabatanCellText = row.cells[3].innerText.toLowerCase();
                    const searchMatch = rowText.includes(searchText);
                    const jabatanMatch = (selectedJabatan === "") || (jabatanCellText.includes(selectedJabatan));
                    if (searchMatch && jabatanMatch) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            }
            searchInput.addEventListener('keyup', applyFilters);
            jabatanFilter.addEventListener('change', applyFilters);
            // 🟢 END: SCRIPT FILTER & PENCARIAN

            // --- SCRIPT UNTUK FORM OFFCANVAS ---
            const formOverlayEl = document.getElementById('formOverlay');
            const formOverlay = new bootstrap.Offcanvas(formOverlayEl);

            const form = document.getElementById('akunForm');
            const title = document.getElementById('formOverlayLabel');
            const submitButton = document.getElementById('formSubmitButton');
            const methodSpoofingDiv = document.getElementById('method-spoofing');
            const passwordHelpText = document.getElementById('password-help-text');
            const passwordInput = document.getElementById('form_password');
            const passwordConfirmInput = document.getElementById('form_password_confirmation');
            const hiddenUserIdInput = document.getElementById('form_id_user');

            document.querySelectorAll('.btn-open-overlay').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    form.reset();
                    methodSpoofingDiv.innerHTML = '';
                    passwordInput.required = false;
                    passwordConfirmInput.required = false;
                    hiddenUserIdInput.value = ''; // Clear hidden ID input

                    if (action === 'tambah') {
                        title.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Akun Baru';
                        form.action = '{{ route("akun.store") }}';
                        submitButton.textContent = 'Simpan Akun';
                        passwordHelpText.textContent = 'Isi untuk membuat password baru.';
                        passwordInput.required = true;
                        passwordConfirmInput.required = true;
                    } else if (action === 'edit') {
                        const id = this.getAttribute('data-id');
                        title.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Akun';
                        form.action = `{{ url('akun') }}/${id}`;
                        submitButton.textContent = 'Simpan';
                        passwordHelpText.textContent = 'Kosongkan jika tidak ingin mengubah password.';
                        methodSpoofingDiv.innerHTML = '@method("PUT")';

                        // Populate form fields
                        document.getElementById('form_nama').value = this.getAttribute('data-nama');
                        document.getElementById('form_email').value = this.getAttribute('data-email');
                        document.getElementById('form_jabatan').value = this.getAttribute('data-jabatan');
                        document.getElementById('form_no_telepon').value = this.getAttribute('data-no_telepon');
                        hiddenUserIdInput.value = id; // Set the hidden ID for validation failure
                    }
                    formOverlay.show();
                });
            });

            document.querySelectorAll('.form-hapus').forEach(form => {
                form.addEventListener('submit', function(e) {
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
            document.addEventListener('DOMContentLoaded', function() {
                const formOverlay = new bootstrap.Offcanvas(document.getElementById('formOverlay'));
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Terjadi Kesalahan Validasi',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    // Use willClose to ensure the modal is gone before showing the offcanvas
                    willClose: () => {
                        // Restore form state based on old input
                        @if(old('id_user'))
                            // This was an EDIT attempt
                            document.getElementById('formOverlayLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Akun';
                            document.getElementById('formSubmitButton').textContent = 'Perbarui Akun';
                            document.getElementById('password-help-text').textContent = 'Kosongkan jika tidak ingin mengubah password.';
                            document.getElementById('form_password').required = false;
                            document.getElementById('form_password_confirmation').required = false;
                        @else
                            // This was an ADD attempt
                            document.getElementById('formOverlayLabel').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Akun Baru';
                            document.getElementById('formSubmitButton').textContent = 'Simpan Akun';
                            document.getElementById('password-help-text').textContent = 'Isi untuk membuat password baru.';
                            document.getElementById('form_password').required = true;
                            document.getElementById('form_password_confirmation').required = true;
                        @endif

                        formOverlay.show();
                    }
                });
            });
        </script>
    @endif
</body>
</html>