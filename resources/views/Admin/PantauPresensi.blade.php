<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Galeri Foto Presensi - {{ config('app.name', 'Laravel') }}</title>

    {{-- Aset CSS Modern --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    {{-- Gaya CSS Kustom yang Konsisten + Gaya untuk Galeri --}}
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
            .header {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .l-navbar {
            background-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
   
        .nav_logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
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
        .body-pd main { padding-left: calc(var(--nav-width) + 1rem); }
        .header, .l-navbar { background-color: var(--primary-color); }
        .nav_logo, .nav_link { color: #fff; }
        .nav_link:hover { background-color: var(--primary-color-alt); color: #fff; }
        .nav_link.active { background-color: var(--primary-color-alt); }
        .page-header { margin-bottom: 1.5rem; }
        .page-title { color: #212529; font-weight: 700; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }
        .card-header { background-color: transparent; padding: 1rem 1.5rem; border-color: var(--border-color); }

        /* Gaya Khusus untuk Galeri Foto yang Cantik */
        .photo-card {
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .photo-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .photo-card .card-img-top {
            height: 220px;
            object-fit: cover;
        }
        .photo-card .img-container {
            position: relative;
        }
        .photo-card .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            opacity: 0;
            transition: background 0.3s ease, opacity 0.3s ease;
        }
        .photo-card:hover .img-overlay {
            background: rgba(0, 0, 0, 0.4);
            opacity: 1;
        }
        .photo-card .card-footer {
            background-color: #fff;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.8rem;
        }
    </style>
</head>

<body id="body-pd">
    @include('layouts.sidebar')

    <main>
        <div class="page-header">
            <h2 class="page-title">Kelola Galeri Foto Presensi</h2>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">halaman / Data Pantau Presensi</li>
                    </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-images me-2"></i>Bukti Kehadiran Pegawai</h5>
            </div>
            <div class="card-body">
                @if(empty($photos))
                    <div class="text-center py-5">
                        <i class="bi bi-camera-reels fs-1 text-muted"></i>
                        <h5 class="mt-3">Galeri Masih Kosong</h5>
                        <p class="text-muted">Belum ada foto bukti presensi yang tersimpan.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($photos as $photoPath)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card h-100 shadow-sm photo-card" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#photoModal" 
                                     data-bs-image="{{ asset('storage/' . $photoPath) }}"
                                     data-bs-title="{{ basename($photoPath) }}">

                                    <div class="img-container">
                                        <img src="{{ asset('storage/' . $photoPath) }}" class="card-img-top" alt="Foto Presensi">
                                        <div class="img-overlay">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <small class="text-muted">{{ basename($photoPath) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>

    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel"><i class="bi bi-image-alt me-2"></i>Detail Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="modalImage" src="" class="img-fluid rounded" alt="Detail Foto Presensi">
                </div>
                <div class="modal-footer">
                    <span id="modalImageTitle" class="text-muted small me-auto"></span>
                    <a id="downloadButton" href="#" class="btn btn-primary" download>
                        <i class="bi bi-download me-2"></i>Unduh Gambar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoModal = document.getElementById('photoModal');
            photoModal.addEventListener('show.bs.modal', function (event) {
                const triggerElement = event.relatedTarget;
                
                const imageUrl = triggerElement.getAttribute('data-bs-image');
                const imageTitle = triggerElement.getAttribute('data-bs-title');
                
                const modalImage = photoModal.querySelector('#modalImage');
                const modalImageTitleSpan = photoModal.querySelector('#modalImageTitle');
                const downloadBtn = photoModal.querySelector('#downloadButton');
                
                modalImage.src = imageUrl;
                modalImageTitleSpan.textContent = imageTitle;
                downloadBtn.href = imageUrl;
                downloadBtn.download = imageTitle; 
            });
        });
    </script>
</body>

</html>