<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $travel->Penyelenggara }}, Indeks Kepercayaan | {{ config('app.name') }}</title>
    <meta name="description" content="Profil kepercayaan masyarakat untuk {{ $travel->Penyelenggara }} berdasarkan data pengawasan Kanwil Kementerian Haji dan Umroh NTB." />

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Roboto:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/public-theme.css') }}" rel="stylesheet" />

    <style>
        .profile-section { padding-top: 100px; padding-bottom: min(5vw, 60px); }

        .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 0.85rem;
            margin-right: 0.35rem;
            margin-bottom: 0.35rem;
        }

        .info-grid dt {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--phu-text-muted, #5a5a5a);
            margin-bottom: 0.2rem;
        }

        .info-grid dd {
            font-weight: 600;
            color: var(--phu-text, #333333);
            margin-bottom: 1rem;
        }
    </style>

    @include('partials.public-trust-styles')
</head>

<body>
    <a href="{{ route('travel.public') }}" class="back-btn">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
    </a>

    <section class="profile-section">
        <div class="container" style="max-width: 960px;">
            {{-- Hero --}}
            <div class="trust-hero">
                <div class="trust-hero__top">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-uppercase mb-2" style="font-size:0.72rem; letter-spacing:0.08em; opacity:0.75;">
                                Profil Kepercayaan Masyarakat
                            </p>
                            <h1 class="h2 fw-bold mb-2">{{ $travel->Penyelenggara }}</h1>
                            <div>
                                <span class="info-pill"><i class="fas fa-map-marker-alt"></i> {{ $travel->kab_kota }}</span>
                                <span class="info-pill"><i class="fas fa-building"></i> {{ $travel->Status }}</span>
                                @if($travel->nilai_akreditasi)
                                    <span class="info-pill"><i class="fas fa-award"></i> Akreditasi {{ $travel->nilai_akreditasi }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <div class="col-md-5 border-end">
                        <div class="trust-hero__score-wrap">
                            @if($trust['has_data'])
                                <div class="trust-gauge" style="border-color: {{ $trust['color'] }};">
                                    <span class="trust-gauge__number" style="color: {{ $trust['color'] }};">{{ $trust['score'] }}</span>
                                    <span class="trust-gauge__of">dari 100</span>
                                </div>
                                <div class="trust-gauge__stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa{{ $i <= $trust['stars'] ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="trust-gauge__label bg-{{ $trust['bg_class'] }} text-white">
                                    {{ $trust['label'] }}
                                </span>
                                <p class="text-muted mb-0 px-3" style="font-size:0.92rem; line-height:1.65;">
                                    {{ $trust['description'] }}
                                </p>
                                @if($trust['updated_at'])
                                    <p class="text-muted small mt-3 mb-0">
                                        <i class="far fa-clock me-1"></i>
                                        Diperbarui {{ \App\Support\PublicTrustIndex::formattedUpdatedAt($trust['updated_at']) }}
                                    </p>
                                @endif
                            @else
                                <div class="py-4">
                                    <i class="fas fa-circle-info text-muted d-block mb-3" style="font-size:2.5rem;"></i>
                                    <h5 class="fw-semibold">Data Belum Tersedia</h5>
                                    <p class="text-muted mb-0 px-3" style="font-size:0.92rem;">
                                        Indeks kepercayaan untuk travel ini belum dihitung. Silakan cek kembali nanti.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-7 p-4">
                        <h5 class="fw-bold mb-3">Informasi Travel</h5>
                        <dl class="info-grid row mb-0">
                            <div class="col-sm-6">
                                <dt>Pimpinan</dt>
                                <dd>{{ $travel->Pimpinan ?: 'Tidak ada' }}</dd>
                            </div>
                            <div class="col-sm-6">
                                <dt>Telepon</dt>
                                <dd>{{ $travel->Telepon ?: $travel->telepon ?: 'Tidak ada' }}</dd>
                            </div>
                            <div class="col-sm-6">
                                <dt>Jenis Layanan</dt>
                                <dd>
                                    @forelse ($travel->getAvailableServices() as $service)
                                        <span class="badge bg-light text-dark border me-1">{{ $service }}</span>
                                    @empty
                                        Tidak ada
                                    @endforelse
                                </dd>
                            </div>
                            <div class="col-sm-6">
                                <dt>Status Izin</dt>
                                <dd>
                                    @php
                                        $licenseBadge = match($travel->getLicenseStatus()) {
                                            'Active' => 'success',
                                            'Expired' => 'danger',
                                            default => 'secondary',
                                        };
                                        $licenseLabel = match($travel->getLicenseStatus()) {
                                            'Active' => 'Aktif',
                                            'Expired' => 'Kedaluwarsa',
                                            default => 'Tidak tersedia',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $licenseBadge }}">{{ $licenseLabel }}</span>
                                </dd>
                            </div>
                            @if($travel->alamat_kantor_baru || $travel->alamat_kantor_lama)
                                <div class="col-12">
                                    <dt>Alamat Kantor</dt>
                                    <dd class="mb-0">{{ $travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Sinyal kepercayaan --}}
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Apa yang Membentuk Indeks Ini?</h4>
                <p class="text-muted mb-4">Ringkasan sederhana berdasarkan data resmi di sistem PANTAU Kanwil NTB.</p>
                <div class="row g-3">
                    @foreach ($signals as $signal)
                        <div class="col-md-6">
                            <div class="card signal-card p-3">
                                <div class="signal-card__icon signal-card__icon--{{ $signal['tone'] }}">
                                    <i class="fas {{ $signal['icon'] }}"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $signal['title'] }}</h6>
                                <p class="text-muted mb-0 small">{{ $signal['detail'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FAQ / Metodologi --}}
            <div class="card border-0 shadow-sm mb-4 trust-faq">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-circle-question me-2 text-success"></i>Pertanyaan yang Sering Diajukan</h5>
                    <div class="accordion accordion-flush" id="trustFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Apa itu Indeks Kepercayaan Masyarakat?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#trustFaq">
                                <div class="accordion-body text-muted">
                                    Indeks ini menunjukkan seberapa baik travel memenuhi standar pengawasan berdasarkan data pengaduan, pengawasan, kepatuhan administratif, dan aktivitas layanan. Semakin tinggi angkanya, semakin baik catatan travel di mata Kanwil.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Bagaimana angka ini dihitung?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#trustFaq">
                                <div class="accordion-body text-muted">
                                    Sistem menghitung secara otomatis dari data operasional: jumlah pengaduan masyarakat, hasil pengawasan, kelengkapan dokumen (BA Pemberangkatan & sertifikat), status izin, dan aktivitas pelayanan jamaah. Angka ditampilkan dalam skala 0 sampai 100.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Apakah ini penilaian resmi dari Kanwil?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#trustFaq">
                                <div class="accordion-body text-muted">
                                    Bukan. Ini adalah informasi transparansi dari Kanwil Kementerian Haji dan Umroh NTB untuk membantu masyarakat membuat keputusan yang lebih terinformasi. Tetap lakukan pengecekan mandiri sebelum memilih travel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trust-disclaimer mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan penting:</strong>
                Indeks Kepercayaan dihitung otomatis dari data pengawasan dan pengaduan di sistem PANTAU.
                Informasi ini bersifat informatif dan bukan jaminan mutu layanan travel.
                Jika Anda mengalami masalah, silakan ajukan pengaduan melalui halaman utama.
            </div>

            <div class="text-center">
                <a href="{{ url('/') }}#pengaduan" class="btn btn-success rounded-pill px-4 py-2 fw-semibold" style="background:#1acc8d; border-color:#1acc8d;">
                    <i class="fas fa-paper-plane me-2"></i>Ajukan Pengaduan
                </a>
            </div>
        </div>
    </section>

    @include('partials.kanwil-contact', ['variant' => 'footer-compact'])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
