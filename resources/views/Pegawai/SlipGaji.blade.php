<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Slip Gaji - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {--header-height: 3rem;--nav-width: 68px;--primary-color: #3e6455;--primary-color-alt: #315044;--white-color: #FFFFFF;--light-bg: #f8f9fa;--card-bg: #ffffff;--text-color: #495057;--text-color-light: #6c757d;--border-color: #e9ecef;--font-family: 'Inter', sans-serif;--z-fixed: 100;}
        *, ::before, ::after { box-sizing: border-box; }
        body {position: relative;margin: var(--header-height) 0 0 0;padding: 0 1rem;font-family: var(--font-family);background-color: var(--light-bg);transition: .5s;}
        a { text-decoration: none; }
        .header{width:100%;height:var(--header-height);position:fixed;top:0;left:0;display:flex;align-items:center;justify-content:space-between;padding:0 1rem;background-color:var(--primary-color);z-index:var(--z-fixed);transition:.5s}.header_toggle{color:var(--white-color);font-size:1.5rem;cursor:pointer}.header_img{display:flex;align-items:center;}.l-navbar{position:fixed;top:0;left:-100%;width:calc(var(--nav-width) + 156px);height:100vh;background-color:var(--primary-color);padding:.5rem 1rem 0 0;transition:.5s;z-index:var(--z-fixed)}.nav{height:100%;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden}.nav_logo,.nav_link{display:grid;grid-template-columns:max-content max-content;align-items:center;column-gap:1rem;padding:.5rem 0 .5rem 1.5rem}.nav_logo{margin-bottom:2rem}.nav_logo-icon,.nav_icon{font-size:1.25rem;color:var(--white-color)}.nav_logo-name{color:var(--white-color);font-weight:700}.nav_link{position:relative;color:#E0E0E0;margin-bottom:1.5rem;transition:.3s}.nav_link:hover{color:var(--white-color)}.show{left:0}.body-pd{padding-left:calc(var(--nav-width) + 1rem)}.active{color:var(--white-color)}.active::before{content:'';position:absolute;left:0;width:2px;height:32px;background-color:var(--white-color)}.sign_out{display:flex;align-items:center;color:var(--white-color)}.sign_out .nav_name{background:none;border:none;color:var(--white-color);padding:0;margin-left:.5rem;cursor:pointer}
        @media screen and (min-width:768px){body{margin:calc(var(--header-height) + 1rem) 0 0 0;padding-left:calc(var(--nav-width) + 2rem)}.header{height:calc(var(--header-height) + 1rem);padding:0 2rem 0 calc(var(--nav-width) + 2rem)}.l-navbar{left:0;padding:1rem 1rem 0 0;width:var(--nav-width)}.l-navbar.show{width:calc(var(--nav-width) + 156px)}.body-pd{padding-left:calc(var(--nav-width) + 188px)}}

        /* --- PERUBAHAN DI SINI --- */
        main {
            padding-top: 1.5rem; /* Menambahkan jarak atas pada konten utama */
            padding-bottom: 3rem;
        }
        /* ------------------------- */

        .info-strip { background-color: #f7f8fc; border-radius: 0.5rem; padding: 0.75rem 1.25rem; display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; }
        .accordion-item { border: 1px solid var(--border-color) !important; border-radius: 0.75rem !important; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .accordion-header .accordion-button { border-radius: calc(0.75rem - 1px) !important; background-color: var(--card-bg); color: var(--text-color); font-weight: 600; }
        .accordion-header .accordion-button:not(.collapsed) { background-color: #f7f8fc; box-shadow: none; color: var(--primary-color); }
        .pdf-slip-container{font-family:Arial,sans-serif;font-size:10pt;width:200mm;height:260mm;margin:20px;border:1px solid #000;padding:15mm;box-sizing:border-box;page-break-after:always;position:relative;overflow:hidden}.pdf-background-watermark{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;justify-content:center;align-items:center;z-index:1}.pdf-background-watermark img{max-width:550px;opacity:.1}.pdf-content-foreground{position:relative;z-index:2}.pdf-header{text-align:center;margin-bottom:20px}.pdf-info-grid{display:flex;flex-wrap:wrap;margin-bottom:10px}.pdf-info-grid>div{width:50%;display:flex;margin-bottom:5px}.pdf-info-grid .label{width:120px;flex-shrink:0}.pdf-details-grid{display:flex;margin-top:15px;border-top:1px solid #000;border-bottom:1px solid #000;padding:10px 0;margin-bottom:15px}.pdf-details-grid .column{width:50%;padding-right:10px}.pdf-details-grid .item{display:flex;justify-content:space-between;margin-bottom:5px}.pdf-total{text-align:right;font-size:12pt;margin-top:15px;font-weight:bold}.pdf-signature-section{display:flex;justify-content:space-around;margin-top:30px;font-size:9pt}
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
                <a href="#" class="nav_logo">
                    <img src="{{ asset('img/logoak.png') }}" alt="Logo" class="nav_logo-icon" style="width: 25px; height: auto;">
                    <span class="nav_logo-name">SIMPEG</span>
                </a>
                <div class="nav_list">
                    <a href="{{ route('dashboard.pegawai') }}" class="nav_link {{ request()->routeIs('dashboard.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-grid-alt nav_icon'></i><span class="nav_name">Menu Utama</span>
                    </a>
                    <a href="{{ route('presensiqr.pegawai') }}" class="nav_link {{ request()->routeIs('presensiqr.pegawai') ? 'active' : '' }}">
                        <i class='bx bx-qr nav_icon'></i><span class="nav_name">Presensi QR</span>
                    </a>
                    <a href="{{ route('presensi.history') }}" class="nav_link {{ request()->routeIs('presensi.history') ? 'active' : '' }}">
                        <i class='bx bx-history nav_icon'></i><span class="nav_name">Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('slipgaji.pegawai') }}" class="nav_link {{ request()->routeIs('slipgaji.*') ? 'active' : '' }}">
                        <i class='bx bx-receipt nav_icon'></i><span class="nav_name">Slip Gaji</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <main>
        <div class="container-fluid">
            <div class="page-header">
                <h3>Riwayat Slip Gaji</h3>
                <p class="text-muted">Pilih periode untuk melihat rincian atau unduh slip gaji Anda.</p>
            </div>

            <div class="card export-card mb-4">
                 <div class="input-group input-group-lg">
                     <input type="month" id="periode-export" name="periode" class="form-control" aria-label="Pilih Periode untuk Ekspor PDF">
                     <button class="btn btn-danger" type="button" id="btn-export-pdf"><i class="bi bi-file-earmark-arrow-down-fill me-2"></i>Unduh PDF</button>
                 </div>
            </div>

            @if($daftarGaji->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-file-earmark-zip"></i>
                    <h5 class="mt-3">Slip Gaji Belum Tersedia</h5>
                    <p class="text-muted">Saat ini belum ada data slip gaji yang dapat ditampilkan.</p>
                </div>
            @else
                <div class="accordion" id="payslipAccordion">
                    @foreach ($daftarGaji as $gaji)
                    <div class="accordion-item" 
                        data-periode="{{ $gaji->tanggal_penggajian->format('Y-m') }}" 
                        data-export-data="{{ json_encode([
                                'periode' => $gaji->tanggal_penggajian->format('F Y'),
                                'nama' => Auth::user()->nama,
                                'total_jam_kerja' => $gaji->total_jam,
                                'total_jam_lembur' => $gaji->total_jam_lembur ?? 0,
                                'aktivitas_dihadiri' => $gaji->aktivitas_dihadiri ?? 0,
                                'tipe_pembayaran' => $gaji->slipGaji ? $gaji->slipGaji->tipe_pembayaran : '',
                                'gaji_berdasarkan_tipe' => $gaji->slipGaji ? $gaji->slipGaji->gaji_berdasarkan_tipe : 0,
                                'tunjangan' => $gaji->slipGaji ? $gaji->slipGaji->tunjangan : 0,
                                'total_gaji' => $gaji->slipGaji ? $gaji->slipGaji->total_gaji : 0,
                                'tanggal_persetujuan' => $gaji->tanggal_penggajian->format('d F Y'),
                            ]) }}">
                        <h2 class="accordion-header" id="heading-{{ $loop->index }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loop->index }}" aria-expanded="false" aria-controls="collapse-{{ $loop->index }}">
                                <div class="accordion-summary w-100 d-flex justify-content-between">
                                    <span class="summary-period">{{ $gaji->tanggal_penggajian->format('F Y') }}</span>
                                    <span class="summary-amount">Rp {{ number_format($gaji->slipGaji ? $gaji->slipGaji->total_gaji : 0, 0, ',', '.') }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-{{ $loop->index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $loop->index }}" data-bs-parent="#payslipAccordion">
                            <div class="accordion-body">
                                <div class="info-strip">
                                    <span><strong>Total Jam Kerja:</strong> {{ $gaji->total_jam }} jam</span>
                                    <span><strong>Total Jam Lembur:</strong> {{ $gaji->total_jam_lembur ?? 0 }} jam</span>
                                    <span><strong>Aktivitas Dihadiri:</strong> {{ $gaji->aktivitas_dihadiri ?? 0 }}</span>
                                    <span><strong>Status:</strong> <span class="badge bg-success">{{ $gaji->status_persetujuan }}</span></span>
                                </div>
                                @if ($gaji->slipGaji)
                                <h6>Rincian Gaji</h6>
                                <dl class="row">
                                    <dt class="col-sm-6">Gaji Pokok</dt>
                                    <dd class="col-sm-6 text-end">Rp {{ number_format($gaji->slipGaji->gaji_berdasarkan_tipe, 0, ',', '.') }}</dd>

                                    <dt class="col-sm-6">Tunjangan</dt>
                                    <dd class="col-sm-6 text-end">Rp {{ number_format($gaji->slipGaji->tunjangan, 0, ',', '.') }}</dd>
                                </dl>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                                    <span>Total Gaji Diterima</span>
                                    <span>Rp {{ number_format($gaji->slipGaji->total_gaji, 0, ',', '.') }}</span>
                                </div>
                                @else
                                <div class="alert alert-light text-center">Detail slip gaji belum tersedia.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">{{ $daftarGaji->links() }}</div>
            @endif
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk sidebar...
            const showNavbar=(t,n,e,o)=>{const l=document.getElementById(t),d=document.getElementById(n),c=document.getElementById(e),a=document.getElementById(o);l&&d&&c&&a&&l.addEventListener("click",()=>{d.classList.toggle("show"),c.classList.toggle("body-pd"),a.classList.toggle("body-pd")})};showNavbar("header-toggle","nav-bar","body-pd","header");

            function formatRupiah(amount) {
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0
                }).format(amount)
            }
            const exportBtn = document.getElementById("btn-export-pdf");
            if (exportBtn) {
                exportBtn.addEventListener("click", function() {
                    const periodeInput = document.getElementById("periode-export").value;
                    if (!periodeInput) return Swal.fire("Gagal", "Silakan pilih periode terlebih dahulu.", "error");

                    const logoUrl = "{{ asset('img/logoak2.png') }}";
                    const [tahun, bulan] = periodeInput.split('-');
                    const namaBulan = new Date(periodeInput + '-02').toLocaleString('id-ID', { month: 'long' });

                    let slipHtml = '';
                    let slipFound = false;

                    const slipElement = document.querySelector(`.accordion-item[data-periode="${periodeInput}"]`);

                    if (slipElement) {
                        const data = JSON.parse(slipElement.dataset.exportData);
                        if (data.total_gaji) {
                            slipFound = true;
                            const gajiBerdasarkanTipe = formatRupiah(data.gaji_berdasarkan_tipe);
                            const tunjangan = formatRupiah(data.tunjangan);
                            const totalGaji = formatRupiah(data.total_gaji);
                            const tipePembayaranText = (data.tipe_pembayaran || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                            slipHtml = `
                                <div class="pdf-slip-container">
                                    <div class="pdf-background-watermark"><img src="${logoUrl}" alt="Watermark"></div>
                                    <div class="pdf-content-foreground">
                                        <div class="pdf-header"><h3>Slip Gaji</h3><p>Periode: ${data.periode}</p></div>
                                        <div class="pdf-info-grid">
                                            <div><span class="label">Nama</span><span class="value">: ${data.nama}</span></div>
                                            <div><span class="label">Total Jam Kerja</span><span class="value">: ${data.total_jam_kerja} jam</span></div>
                                            <div><span class="label">Total Jam Lembur</span><span class="value">: ${data.total_jam_lembur} jam</span></div>
                                            <div><span class="label">Aktivitas Dihadiri</span><span class="value">: ${data.aktivitas_dihadiri}</span></div>
                                            <div><span class="label">Tipe Pembayaran</span><span class="value">: ${tipePembayaranText}</span></div>
                                        </div>
                                        <div class="pdf-details-grid">
                                            <div class="column" style="width:100%">
                                                <div class="item"><span>Gaji Pokok</span><span>${gajiBerdasarkanTipe}</span></div>
                                                <div class="item"><span>Tunjangan</span><span>${tunjangan}</span></div>
                                            </div>
                                        </div>
                                        <div class="pdf-total"><span>Total Gaji: </span><span>${totalGaji}</span></div>
                                        <div class="pdf-signature-section">
                                            <div><span>Diterima Oleh</span><div style="height:20mm"></div><span>${data.nama}</span></div>
                                        </div>
                                    </div>
                                </div>`;
                        }
                    }

                    if (!slipFound) return Swal.fire("Data Tidak Ditemukan", `Tidak ada data slip gaji untuk periode ${namaBulan} ${tahun} yang bisa diekspor.`, 'warning');

                    const filename = `slip-gaji-${namaBulan.toLowerCase().replace(' ', '-')}-${tahun}.pdf`;
                    html2pdf().from(slipHtml).set({
                        margin: 0, filename: filename, image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, logging: false, useCORS: true },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    }).save().then(() => {
                         Swal.fire('Berhasil!', 'Slip gaji berhasil diunduh dalam format PDF.', 'success');
                    });
                });
            }
        });
    </script>
</body>
</html>