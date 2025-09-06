<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi QR & Wajah - {{ config('app.name', 'Laravel') }}</title>

    {{-- Aset & Font --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --primary-color: #3e6455;
            --primary-color-alt: #315044;
            --secondary-color: #5a9a83;
            --white-color: #FFFFFF;
            --light-bg: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-color-light: #6c757d;
            --border-color: #e9ecef;
            --font-family: 'Inter', sans-serif;
            --z-fixed: 100;
        }

        .btn-primary2 {
            background-color: #315044;
            color: white;
        }

        *,
        ::before,
        ::after {
            box-sizing: border-box;
        }

        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--font-family);
            background-color: var(--light-bg);
            transition: .5s;
        }

        a {
            text-decoration: none;
        }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: var(--primary-color);
            z-index: var(--z-fixed);
            transition: .5s
        }

        .header_toggle {
            color: var(--white-color);
            font-size: 1.5rem;
            cursor: pointer
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -100%;
            width: calc(var(--nav-width) + 156px);
            height: 100vh;
            background-color: var(--primary-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed)
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden
        }

        .nav_logo,
        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem
        }

        .nav_logo {
            margin-bottom: 2rem
        }

        .nav_logo-icon,
        .nav_icon {
            font-size: 1.25rem;
            color: var(--white-color)
        }

        .nav_logo-name {
            color: var(--white-color);
            font-weight: 700
        }

        .nav_link {
            position: relative;
            color: #E0E0E0;
            margin-bottom: 1.5rem;
            transition: .3s
        }

        .nav_link:hover {
            color: var(--white-color)
        }

        .show {
            left: 0
        }

        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem)
        }

        .active {
            color: var(--white-color)
        }

        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color)
        }

        .sign_out {
            display: flex;
            align-items: center;
            color: var(--white-color)
        }

        .sign_out .nav_name {
            background: none;
            border: none;
            color: var(--white-color);
            padding: 0;
            margin-left: .5rem;
            cursor: pointer
        }

        @media screen and (min-width:768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem)
            }

            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem)
            }

            .l-navbar {
                left: 0;
                padding: 1rem 1rem 0 0;
                width: var(--nav-width)
            }

            .l-navbar.show {
                width: calc(var(--nav-width) + 156px)
            }

            .body-pd {
                padding-left: calc(var(--nav-width) + 188px)
            }
        }

        main {
            padding-top: 1.5rem;
            padding-bottom: 3rem;
        }

        .wizard-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .wizard-step-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            text-align: center;
        }

        .wizard-header {
            margin-bottom: 1.5rem;
        }

        .wizard-header .step-icon {
            width: 60px;
            height: 60px;
            background-color: #e9f7ef;
            color: var(--primary-color);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .wizard-header h3 {
            font-weight: 700;
        }

        #attendance-section {
            display: none;
        }

        .camera-viewport {
            width: 100%;
            padding-top: 75%;
            position: relative;
            background-color: #111;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 3px solid var(--border-color);
        }

        #camera-feed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #camera-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-color-light);
        }

        #reader {
            width: 100% !important;
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        #qr-reader-placeholder {
            padding: 2rem;
            border: 2px dashed var(--border-color);
            border-radius: 0.75rem;
            color: var(--text-color-light);
        }

        #scan-result-display {
            display: none;
        }

        .result-card {
            background-color: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: left;
        }

        .result-card strong {
            color: var(--primary-color);
        }

        .history-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 3rem;
        }

        .history-card .card-header {
            background-color: var(--primary-color);
            color: var(--white-color);
            font-weight: 600;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .history-table tbody td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.5em 0.7em;
        }
    </style>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
        <div class="header_img">
            <form action="{{ route('logout') }}" method="POST" class="sign_out">
                @csrf
                <i class='bx bx-log-out nav_icon'></i><button type="submit" class="nav_name">Keluar</button>
            </form>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo"><img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon"
                        style="width: 25px; height: auto;"><span class="nav_logo-name">SIMPEG</span></a>
                <div class="nav_list">
                    <a href="{{ route('dashboard.pegawai') }}"
                        class="nav_link {{ request()->routeIs('dashboard.pegawai') ? 'active' : '' }}"><i
                            class='bx bx-grid-alt nav_icon'></i><span class="nav_name">Menu Utama</span></a>
                    <a href="{{ route('presensiqr.pegawai') }}"
                        class="nav_link {{ request()->routeIs('presensiqr.pegawai') ? 'active' : '' }}"><i
                            class='bx bx-qr nav_icon'></i><span class="nav_name">Presensi QR</span></a>
                    <a href="{{ route('presensi.history') }}"
                        class="nav_link {{ request()->routeIs('presensi.history') ? 'active' : '' }}"><i
                            class='bx bx-history nav_icon'></i><span class="nav_name">Riwayat Presensi</span></a>
                    <a href="{{ route('slipgaji.pegawai') }}"
                        class="nav_link {{ request()->routeIs('slipgaji.*') ? 'active' : '' }}"><i
                            class='bx bx-receipt nav_icon'></i><span class="nav_name">Slip Gaji</span></a>
                </div>
            </div>
        </nav>
    </div>

    <main>
        <div class="wizard-container">
            <div id="photo-validation-container">
                <div class="wizard-step-card">
                    <div class="wizard-header">
                        <div class="step-icon"><i class="bi bi-person-bounding-box"></i></div>
                        <h3>Langkah 1: Mengambil Foto</h3>
                        <p class="text-muted">Untuk memulai, kami perlu memastikan ini benar-benar Anda.</p>
                    </div>
                    <div class="camera-viewport mb-3">
                        <video id="camera-feed" autoplay style="display: none;"></video>
                        <div id="camera-placeholder"><i class="bi bi-camera-video-off" style="font-size: 4rem;"></i>
                            <p class="mt-2">Kamera belum aktif</p>
                        </div>
                    </div>

                    <div id="location-display" class="alert alert-secondary mt-3" role="alert" style="display: none; font-size: 0.9em;">
                        <i class="bi bi-geo-alt-fill me-2"></i> <span id="location-status">Mendeteksi lokasi...</span>
                    </div>

                    <div class="d-grid gap-2 mb-2">
                        <button type="button" class="btn btn-primary2 btn-lg" id="btn-start-camera"><i
                                class="bi bi-camera-video me-2"></i> Buka Kamera</button>
                        <button type="button" class="btn btn-success btn-lg" id="btn-capture-validate"
                            style="display: none;"><i class="bi bi-camera me-2"></i> Ambil Foto</button>
                    </div>
                    <div id="status-display" class="alert mt-3" role="alert" style="display: none;"></div>
                    <canvas id="photo-canvas" style="display: none;"></canvas>
                </div>
            </div>

            <div id="attendance-section">
                <div class="wizard-step-card">
                    <div class="wizard-header">
                        <div class="step-icon"><i class="bi bi-qr-code-scan"></i></div>
                        <h3>Langkah 2: Pindai Kode QR</h3>
                        <p class="text-muted">Arahkan kamera ke kode QR yang disediakan.</p>
                    </div>
                    <div id="reader">
                        <div id="qr-reader-placeholder">
                            <h5>Arahkan kamera ke Kode QR untuk memulai.</h5>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary2 mt-3" id="btn-start-scan"><i
                            class="bi bi-camera-fill me-2"></i>Mulai Pindai</button>

                    <div id="scan-result-display" class="mt-4">
                        <hr>
                        <div class="wizard-header pt-2">
                            <div class="step-icon"><i class="bi bi-check2-circle"></i></div>
                            <h3>Langkah 3: Konfirmasi Presensi</h3>
                            <p class="text-muted">Kode QR terdeteksi. Silakan lakukan presensi.</p>
                        </div>
                        <div class="result-card">
                            <p class="mb-2"><strong>Aktivitas:</strong> <span id="result-nama-aktivitas"></span></p>
                            <p class="mb-2"><strong>Waktu Scan:</strong> <span id="result-waktu-scan"></span></p>
                            <p class="mb-3"><strong>Status Terakhir:</strong> <span id="result-status-terakhir">Belum
                                    Scan</span></p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <button type="button" class="btn btn-success flex-fill" id="btn-presensi-masuk"><i
                                        class="bi bi-box-arrow-in-right me-2"></i>Presensi Masuk</button>
                                <button type="button" class="btn btn-danger flex-fill" id="btn-presensi-keluar"><i
                                        class="bi bi-box-arrow-left me-2"></i>Presensi Keluar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="history-card">
                    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Riwayat Presensi Hari Ini
                        ({{ now()->translatedFormat('d F Y') }})</div>
                    <div class="card-body p-2 p-md-3">
                        @if ($todayPresensiRecords->isEmpty())
                            <p class="text-center text-muted m-3">Belum ada presensi tercatat.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table history-table text-center">
                                    <thead>
                                        <tr>
                                            <th class="text-start">Aktivitas</th>
                                            <th>Status</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayPresensiRecords as $presensi)
                                            <tr>
                                                <td class="text-start">{{ $presensi->activity->nama_aktivitas ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($presensi->status == 'masuk') <span
                                                            class="badge bg-success">Masuk</span>
                                                    @elseif($presensi->status == 'keluar') <span
                                                            class="badge bg-danger">Keluar</span>
                                                    @else <span
                                                            class="badge bg-secondary">{{ ucfirst($presensi->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $presensi->updated_at->format('H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showNavbar = (t, n, e, o) => { const l = document.getElementById(t), d = document.getElementById(n), c = document.getElementById(e), a = document.getElementById(o); l && d && c && a && l.addEventListener("click", () => { d.classList.toggle("show"), c.classList.toggle("body-pd"), a.classList.toggle("body-pd") }) }; showNavbar("header-toggle", "nav-bar", "body-pd", "header");

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let qrCodeActivityUuid = null;
            let html5QrcodeScanner = null;
            const video = document.getElementById('camera-feed');
            const canvas = document.getElementById('photo-canvas');
            const statusDisplay = document.getElementById('status-display');
            const btnStartCamera = document.getElementById('btn-start-camera');
            const btnCaptureValidate = document.getElementById('btn-capture-validate');
            const cameraPlaceholder = document.getElementById('camera-placeholder');
            const validationContainer = document.getElementById('photo-validation-container');
            const attendanceSection = document.getElementById('attendance-section');
            let stream = null;

            // Variabel untuk menyimpan koordinat lokasi
            let currentLatitude = null;
            let currentLongitude = null;

            // Fungsi untuk mendapatkan lokasi pengguna
            function getLocation() {
                const locationDisplay = document.getElementById('location-display');
                const locationStatus = document.getElementById('location-status');
                locationDisplay.style.display = 'block';

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            currentLatitude = position.coords.latitude;
                            currentLongitude = position.coords.longitude;
                            locationStatus.innerHTML = `Lokasi terdeteksi: <strong>${currentLatitude.toFixed(5)}, ${currentLongitude.toFixed(5)}</strong>`;
                            locationDisplay.classList.remove('alert-secondary', 'alert-danger');
                            locationDisplay.classList.add('alert-success');
                        },
                        (error) => {
                            console.error("Error getting location: ", error);
                            locationStatus.textContent = 'Gagal mendapatkan lokasi. Pastikan izin lokasi diberikan.';
                            locationDisplay.classList.remove('alert-secondary', 'alert-success');
                            locationDisplay.classList.add('alert-danger');
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                } else {
                    locationStatus.textContent = 'Geolocation tidak didukung oleh browser ini.';
                    locationDisplay.classList.remove('alert-secondary');
                    locationDisplay.classList.add('alert-warning');
                }
            }
            
            // Panggil fungsi untuk mendapatkan lokasi saat halaman dimuat
            getLocation();

            function showStatus(message, type = 'info') {
                statusDisplay.textContent = message;
                statusDisplay.className = `alert alert-${type} mt-3`;
                statusDisplay.style.display = 'block';
            }

            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    video.srcObject = stream;
                    video.style.display = 'block';
                    cameraPlaceholder.style.display = 'none';
                    btnStartCamera.style.display = 'none';
                    btnCaptureValidate.style.display = 'block';
                } catch (err) {
                    console.error("Error accessing camera: ", err);
                    showStatus('Gagal mengakses kamera. Pastikan Anda memberikan izin.', 'danger');
                    btnStartCamera.style.display = 'block';
                    btnCaptureValidate.style.display = 'none';
                }
            }

            async function captureAndValidate() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                const capturedPhotoData = canvas.toDataURL('image/jpeg');
                stream.getTracks().forEach(track => track.stop());
                video.style.display = 'none';
                btnCaptureValidate.style.display = 'none';

                if (currentLatitude === null || currentLongitude === null) {
                    Swal.fire({
                        title: 'Lokasi Tidak Ditemukan',
                        text: 'Tidak dapat mengambil foto karena lokasi belum terdeteksi. Harap berikan izin lokasi dan coba lagi.',
                        icon: 'error'
                    });
                    return;
                }

                Swal.fire({ title: 'Mengambil Foto...', text: 'Mohon tunggu sebentar.', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                try {
                    const response = await fetch('{{ route("validate.presence") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({
                            photo: capturedPhotoData,
                            latitude: currentLatitude,
                            longitude: currentLongitude
                        })
                    });
                    const result = await response.json();
                    Swal.close();

                    if (response.ok && result.status === 'success') {
                        Swal.fire({ title: 'Mengambil Foto Berhasil!', text: result.message, icon: 'success', timer: 1500, showConfirmButton: false })
                            .then(() => {
                                validationContainer.style.display = 'none';
                                attendanceSection.style.display = 'block';
                            });
                    } else {
                        Swal.fire({ title: 'Mengambil Foto Gagal!', text: result.message || 'Wajah tidak cocok. Coba lagi.', icon: 'error' })
                            .then(() => startCamera());
                    }
                } catch (error) {
                    Swal.fire({ title: 'Error Koneksi', text: 'Terjadi kesalahan saat menghubungi server.', icon: 'error' })
                        .then(() => startCamera());
                }
            }

            btnStartCamera.addEventListener('click', startCamera);
            btnCaptureValidate.addEventListener('click', captureAndValidate);

            const qrReaderPlaceholder = document.getElementById('qr-reader-placeholder');
            const scanResultDisplay = document.getElementById('scan-result-display');
            const btnStartScan = document.getElementById('btn-start-scan');

            function onScanSuccess(decodedText, decodedResult) {
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.clear().catch(err => console.error("Gagal stop scanner:", err));
                    btnStartScan.style.display = 'block';
                }

                let activityUuid = null;
                const trimmedText = decodedText.trim();
                try {
                    const qrData = JSON.parse(trimmedText);
                    if (qrData && qrData.uuid) {
                        activityUuid = qrData.uuid;
                    }
                } catch (e) {
                    console.log("QR Code bukan JSON, diasumsikan sebagai UUID biasa.");
                    activityUuid = trimmedText;
                }

                if (activityUuid) {
                    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
                    if (uuidRegex.test(activityUuid)) {
                        checkPresensiStatus(activityUuid);
                    } else {
                        Swal.fire('Format Tidak Valid', 'Isi QR Code tidak sesuai format UUID yang diharapkan.', 'error');
                    }
                } else {
                    Swal.fire('Gagal Membaca QR Code', 'Tidak dapat menemukan UUID di dalam QR Code.', 'error');
                }
            }

            async function checkPresensiStatus(activityUuid) {
                document.getElementById('result-status-terakhir').textContent = 'Memuat...';
                scanResultDisplay.style.display = 'none';
                Swal.fire({ title: 'Mengecek Aktivitas...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                try {
                    const response = await fetch(`/api/presensi/status/${activityUuid}/{{ Auth::id() }}`, {
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    Swal.close();

                    if (response.ok) {
                        qrCodeActivityUuid = data.activity.uuid;
                        document.getElementById('result-nama-aktivitas').textContent = data.activity.nama_aktivitas;
                        document.getElementById('result-waktu-scan').textContent = new Date().toLocaleTimeString('id-ID');
                        scanResultDisplay.style.display = 'block';

                        const statusText = document.getElementById('result-status-terakhir');
                        const masukBtn = document.getElementById('btn-presensi-masuk');
                        const keluarBtn = document.getElementById('btn-presensi-keluar');

                        if (data.status === 'masuk') {
                            statusText.textContent = 'Status Terakhir: Masuk';
                            statusText.className = "fw-bold text-success";
                            masukBtn.disabled = true;
                            keluarBtn.disabled = false;
                        } else if (data.status === 'keluar') {
                            statusText.textContent = 'Status Terakhir: Keluar';
                            statusText.className = "fw-bold text-danger";
                            masukBtn.disabled = false;
                            keluarBtn.disabled = true;
                        } else {
                            statusText.textContent = 'Belum Ada Presensi';
                            statusText.className = "fw-bold text-secondary";
                            masukBtn.disabled = false;
                            keluarBtn.disabled = true;
                        }
                    } else {
                        Swal.fire('Error', data.message || 'Gagal mengambil data aktivitas.', 'error');
                        scanResultDisplay.style.display = 'none';
                    }
                } catch (error) {
                    Swal.close();
                    Swal.fire('Error Koneksi', 'Tidak dapat terhubung ke server untuk verifikasi aktivitas.', 'error');
                    console.error("Error checking status:", error);
                }
            }

            async function sendPresensi(status) {
                if (!qrCodeActivityUuid) return;
                let formData = new FormData();
                formData.append('uuid_aktivitas', qrCodeActivityUuid);
                formData.append('status', status);
                formData.append('_token', csrfToken);
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch('{{ route('presensi.store_scan') }}', { method: 'POST', body: formData });
                    const data = await response.json();
                    if (response.ok) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message || 'Gagal presensi.', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                }
            }

            document.getElementById('btn-presensi-masuk').addEventListener('click', () => sendPresensi('masuk'));
            document.getElementById('btn-presensi-keluar').addEventListener('click', () => sendPresensi('keluar'));

            btnStartScan.addEventListener('click', function () {
                scanResultDisplay.style.display = 'none';
                qrReaderPlaceholder.style.display = 'none';
                btnStartScan.style.display = 'none';
                html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
                html5QrcodeScanner.render(onScanSuccess, (e) => { });
            });

            document.getElementById('btn-presensi-masuk').disabled = true;
            document.getElementById('btn-presensi-keluar').disabled = true;
        });
    </script>
</body>
</html>