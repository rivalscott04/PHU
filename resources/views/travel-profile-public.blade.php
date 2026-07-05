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
    <link href="{{ asset('css/travel-profile-public.css') }}" rel="stylesheet" />
    @include('partials.public-trust-styles')
</head>

<body>
    <div class="travel-profile-page">
        <div class="container">
            <header class="travel-page-head">
                <a href="{{ route('travel.public') }}" class="back-btn back-btn--inline">
                    <i class="fas fa-arrow-left me-1"></i>Daftar
                </a>
                <div class="travel-page-head__content">
                    <p class="travel-page-head__eyebrow">Profil Kepercayaan</p>
                    <h1>{{ $travel->Penyelenggara }}</h1>
                    <p class="travel-page-head__subtitle">Ringkasan indeks kepercayaan dari data pengawasan Kanwil Kemenhaj NTB, bukan sertifikat resmi</p>
                    <div class="travel-meta-bar">
                        <span class="info-pill"><i class="fas fa-map-marker-alt"></i> {{ $travel->kab_kota }}</span>
                        <span class="info-pill"><i class="fas fa-building"></i> {{ $travel->Status }}</span>
                        @if($travel->nilai_akreditasi)
                            <span class="info-pill"><i class="fas fa-award"></i> Akreditasi {{ $travel->nilai_akreditasi }}</span>
                        @endif
                    </div>
                </div>
            </header>

            <div class="trust-hero">
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
                                <p class="text-muted mb-0 px-2">
                                    {{ $trust['description'] }}
                                </p>
                                @if($trust['updated_at'])
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="far fa-clock me-1"></i>
                                        Diperbarui {{ \App\Support\PublicTrustIndex::formattedUpdatedAt($trust['updated_at']) }}
                                    </p>
                                @endif
                            @else
                                <div class="py-3">
                                    <i class="fas fa-circle-info text-muted d-block mb-2" style="font-size:1.75rem;"></i>
                                    <h5 class="fw-semibold fs-6">Data Belum Tersedia</h5>
                                    <p class="text-muted mb-0 px-2 small">
                                        Indeks kepercayaan untuk travel ini belum dihitung. Silakan cek kembali nanti.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h5 class="fw-bold">Informasi Travel</h5>
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

            <section class="profile-section-block">
                <div class="profile-section-block__head">
                    <h2>Apa yang Membentuk Indeks Ini?</h2>
                    <p>Faktor-faktor di bawah ini mempengaruhi angka indeks kepercayaan travel ini.</p>
                </div>
                <div class="row g-2">
                    @foreach ($signals as $signal)
                        <div class="col-md-6">
                            <div class="card signal-card">
                                <div class="signal-card__icon signal-card__icon--{{ $signal['tone'] }}">
                                    <i class="fas {{ $signal['icon'] }}"></i>
                                </div>
                                <h6 class="fw-bold">{{ $signal['title'] }}</h6>
                                <p class="text-muted mb-0">{{ $signal['detail'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="profile-panel">
                <button class="profile-panel-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trustFaqPanel" aria-expanded="false" aria-controls="trustFaqPanel">
                    <span><i class="fas fa-circle-question me-2"></i>Pertanyaan yang Sering Diajukan</span>
                    <i class="fas fa-chevron-down profile-panel-toggle__chevron"></i>
                </button>
                <div class="collapse" id="trustFaqPanel">
                    <div class="profile-panel__body trust-faq">
                        <div class="accordion accordion-flush" id="trustFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Apa itu Indeks Kepercayaan Masyarakat?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#trustFaq">
                                    <div class="accordion-body text-muted">
                                        Indeks Kepercayaan Masyarakat adalah angka 0 sampai 100 (plus bintang) yang
                                        menunjukkan seberapa baik catatan travel menurut data yang tercatat
                                        di sistem PANTAU Kanwil Kemenhaj NTB, misalnya pengaduan, hasil
                                        pengawasan, kelengkapan dokumen, dan aktivitas layanan.
                                        Semakin tinggi angkanya, semakin baik catatannya.
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
                                        <p class="mb-2">Sistem PANTAU mengecek 6 hal berikut. Semakin banyak masalah yang ditemukan, semakin rendah indeks kepercayaannya:</p>
                                        <ul class="mb-2 ps-3">
                                            <li><strong>Pengaduan masyarakat</strong>: semakin banyak pengaduan, semakin turun nilainya</li>
                                            <li><strong>Temuan pengawasan</strong>: temuan serius menurunkan nilai</li>
                                            <li><strong>Tindak lanjut</strong>: temuan yang belum diselesaikan menurunkan nilai</li>
                                            <li><strong>BAP (Berita Acara Pemberangkatan)</strong>: yang belum dilaporkan menurunkan nilai</li>
                                            <li><strong>Izin &amp; sertifikat</strong>: yang kedaluwarsa menurunkan nilai</li>
                                            <li><strong>Aktivitas layanan</strong>: jarang melayani jamaah menurunkan nilai</li>
                                        </ul>
                                        <p class="mb-0">Hasil akhir ditampilkan sebagai angka <strong>0 sampai 100</strong> dan bintang. Travel tanpa masalah di atas akan mendapat nilai lebih tinggi.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Apakah ini sertifikat resmi atau jaminan dari Kanwil?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#trustFaq">
                                    <div class="accordion-body text-muted">
                                        <strong>Tidak.</strong> Data ini diterbitkan oleh Kanwil Kemenhaj NTB sebagai
                                        informasi transparansi,
                                        <span class="trust-caveat">bukan sertifikat resmi, bukan akreditasi,
                                        dan bukan jaminan bahwa layanan travel pasti baik</span>.
                                        Angka ini hanya rangkuman data pengawasan yang sudah tercatat.
                                        Tetap periksa sendiri izin travel, kontrak, dan reputasinya
                                        sebelum memutuskan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trust-disclaimer">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Catatan penting:</strong>
                Indeks ini dihitung otomatis dari data pengawasan dan pengaduan di sistem PANTAU Kanwil Kemenhaj NTB.
                Informasi ini
                <span class="trust-caveat">bukan sertifikat resmi, bukan akreditasi,
                dan bukan jaminan bahwa layanan travel pasti baik</span>.
                Selalu lakukan pengecekan mandiri sebelum memilih.
            </div>

            @if(session('success'))
                <div class="profile-flash alert alert-success" role="alert">
                    <i class="fas fa-circle-check me-1"></i>{{ session('success') }}
                </div>
            @endif

            <div class="profile-actions">
                <button type="button" class="btn btn-primary btn-profile-cta" data-bs-toggle="modal" data-bs-target="#pengaduanTravelModal">
                    <i class="fas fa-paper-plane me-1"></i>Ajukan Pengaduan
                </button>
                <a href="{{ route('travel.public') }}" class="btn btn-outline-primary btn-profile-cta">
                    <i class="fas fa-list me-1"></i>Lihat Travel Lain
                </a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pengaduanTravelModal" tabindex="-1" aria-labelledby="pengaduanTravelModalLabel" aria-hidden="true"
         @if($errors->any()) data-auto-open="1" @endif>
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content public-pengaduan-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="pengaduanTravelModalLabel">Ajukan Pengaduan</h5>
                        <p class="public-pengaduan-modal__subtitle mb-0">
                            Laporkan masalah terkait <strong>{{ $travel->Penyelenggara }}</strong>
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @include('partials.public-pengaduan-form', [
                        'formId' => 'profile',
                        'lockedTravel' => true,
                        'travel' => $travel,
                        'inModal' => true,
                    ])
                </div>
            </div>
        </div>
    </div>

    @include('partials.kanwil-contact', ['variant' => 'footer-compact'])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/public-pengaduan-form.js') }}"></script>
</body>
</html>
